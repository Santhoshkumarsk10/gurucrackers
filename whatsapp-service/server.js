const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion,
    Browsers,
    downloadMediaMessage
} = require('@whiskeysockets/baileys');
const express = require('express');
const pino = require('pino');
const QRCode = require('qrcode');
const path = require('path');
const fs = require('fs');

const app = express();
app.use(express.json({ limit: '25mb' }));
app.use(express.urlencoded({ extended: true, limit: '25mb' }));

// Restrict document and image reads strictly to the application storage directory
const ALLOWED_STORAGE_DIR = path.resolve(__dirname, '../storage/app');

function isSafeFilePath(targetPath) {
    if (!targetPath || typeof targetPath !== 'string') return false;
    const resolvedPath = path.resolve(targetPath);
    return resolvedPath.startsWith(ALLOWED_STORAGE_DIR + path.sep);
}

// Internal authentication secret for microservice endpoints
const INTERNAL_SECRET = process.env.WHATSAPP_INTERNAL_SECRET || 'gc-whatsapp-internal-2026';

app.use((req, res, next) => {
    // Status endpoint is allowed for monitoring
    if (req.path === '/status') {
        return next();
    }

    const clientSecret = req.headers['x-internal-secret'] || (req.headers['authorization']?.startsWith('Bearer ') ? req.headers['authorization'].slice(7) : null);
    if (!clientSecret || clientSecret !== INTERNAL_SECRET) {
        return res.status(401).json({ success: false, error: 'Unauthorized: Invalid or missing internal secret.' });
    }

    next();
});

const PORT = process.env.WHATSAPP_PORT || 3001;
const authDir = path.join(__dirname, 'auth_info');

let sock = null;
let qrCodeData = null;
let qrCodeBase64 = null;
let isConnected = false;
let connectedUser = null;
let isStarting = false;
let allowedPhone = process.env.ALLOWED_WHATSAPP_PHONE || null;
let rejectedReason = null;

async function startWhatsApp() {
    if (isStarting) return;
    isStarting = true;

    try {
        if (!fs.existsSync(authDir)) {
            fs.mkdirSync(authDir, { recursive: true, mode: 0o700 });
        }
        try { fs.chmodSync(authDir, 0o700); } catch (_) {}

        const { state, saveCreds } = await useMultiFileAuthState(authDir);
        let version = [2, 3000, 1015901307];
        try {
            const v = await fetchLatestBaileysVersion();
            if (v && v.version) version = v.version;
        } catch (err) {}

        if (sock) {
            try { sock.ev.removeAllListeners(); } catch (_) {}
            try { sock.end(); } catch (_) {}
            sock = null;
        }

        sock = makeWASocket({
            version,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: false,
            auth: state,
            browser: Browsers.macOS('Desktop'),
            syncFullHistory: false
        });

        sock.ev.on('creds.update', saveCreds);

        // Forward real-time incoming/outgoing customer messages to Laravel webhook
        sock.ev.on('messages.upsert', async ({ messages, type }) => {
            try {
                if (!messages || !Array.isArray(messages)) return;
                for (const msg of messages) {
                    let rawJid = msg.key?.remoteJid || '';
                    if (!rawJid || rawJid.endsWith('@broadcast') || rawJid.includes('status') || rawJid.includes('@g.us')) continue;

                    // Resolve WhatsApp LID (@lid) to actual customer phone number
                    if (rawJid.endsWith('@lid')) {
                        let resolvedJid = null;
                        try {
                            if (sock?.signalRepository?.lidMapping) {
                                resolvedJid = await sock.signalRepository.lidMapping.getPNForLID(rawJid);
                            }
                        } catch (e) {}

                        if (!resolvedJid) {
                            const lidUser = rawJid.split('@')[0].split(':')[0];
                            const reverseFile = path.join(authDir, `lid-mapping-${lidUser}_reverse.json`);
                            if (fs.existsSync(reverseFile)) {
                                try {
                                    const pn = JSON.parse(fs.readFileSync(reverseFile, 'utf8'));
                                    if (pn) resolvedJid = `${pn}@s.whatsapp.net`;
                                } catch (_) {}
                            }
                        }

                        if (resolvedJid) {
                            console.log(`[LID RESOLVED] ${rawJid} -> ${resolvedJid}`);
                            rawJid = resolvedJid;
                        } else {
                            console.warn(`[LID UNRESOLVED] Could not resolve ${rawJid}`);
                        }
                    }

                    // Strip any device suffix like :1, :2 (e.g. 917708765569:1@s.whatsapp.net -> 917708765569)
                    const userPart = rawJid.split('@')[0].split(':')[0];
                    let cleanPhone = userPart.replace(/[^0-9]/g, '');
                    if (cleanPhone.length > 10) {
                        cleanPhone = cleanPhone.slice(-10);
                    }
                    if (!cleanPhone || cleanPhone.length < 10) continue;

                    const fromMe = Boolean(msg.key?.fromMe);
                    const messageId = msg.key?.id || `msg_${Date.now()}`;
                    const pushName = msg.pushName || null;
                    let unixSeconds = Math.floor(Date.now() / 1000);
                    if (msg.messageTimestamp) {
                        if (typeof msg.messageTimestamp === 'number') {
                            unixSeconds = msg.messageTimestamp;
                        } else if (typeof msg.messageTimestamp === 'bigint') {
                            unixSeconds = Number(msg.messageTimestamp);
                        } else if (typeof msg.messageTimestamp.toNumber === 'function') {
                            unixSeconds = msg.messageTimestamp.toNumber();
                        } else if (msg.messageTimestamp.low) {
                            unixSeconds = msg.messageTimestamp.low;
                        }
                    }

                    let innerMsg = msg.message;
                    if (!innerMsg) continue;

                    // Recursively unwrap known wrappers
                    if (innerMsg.ephemeralMessage?.message) innerMsg = innerMsg.ephemeralMessage.message;
                    if (innerMsg.viewOnceMessage?.message) innerMsg = innerMsg.viewOnceMessage.message;
                    if (innerMsg.viewOnceMessageV2?.message) innerMsg = innerMsg.viewOnceMessageV2.message;
                    if (innerMsg.documentWithCaptionMessage?.message) innerMsg = innerMsg.documentWithCaptionMessage.message;

                    // Ignore protocol or system messages
                    if (innerMsg.protocolMessage || innerMsg.senderKeyDistributionMessage) continue;

                    let text = '';
                    let messageType = 'text';
                    let fileName = null;

                    if (innerMsg.conversation) {
                        text = innerMsg.conversation;
                    } else if (innerMsg.extendedTextMessage?.text) {
                        text = innerMsg.extendedTextMessage.text;
                    } else if (innerMsg.imageMessage) {
                        text = innerMsg.imageMessage.caption || '📷 Photo';
                        messageType = 'image';
                    } else if (innerMsg.documentMessage) {
                        fileName = innerMsg.documentMessage.fileName || 'Document.pdf';
                        text = innerMsg.documentMessage.caption || `📄 ${fileName}`;
                        messageType = 'document';
                    } else if (innerMsg.videoMessage) {
                        text = innerMsg.videoMessage.caption || '🎥 Video';
                        messageType = 'video';
                    } else if (innerMsg.audioMessage) {
                        text = '🎵 Voice Message';
                        messageType = 'audio';
                    } else if (innerMsg.buttonsResponseMessage?.selectedDisplayText) {
                        text = innerMsg.buttonsResponseMessage.selectedDisplayText;
                    } else if (innerMsg.templateButtonReplyMessage?.selectedDisplayText) {
                        text = innerMsg.templateButtonReplyMessage.selectedDisplayText;
                    } else if (innerMsg.interactiveResponseMessage) {
                        try {
                            const body = JSON.parse(innerMsg.interactiveResponseMessage.nativeFlowResponseMessage?.paramsJson || '{}');
                            text = body.id || 'Interactive Reply';
                        } catch (_) {
                            text = 'Interactive Reply';
                        }
                    } else if (innerMsg.contactMessage) {
                        text = `👤 Contact: ${innerMsg.contactMessage.displayName || 'Shared Contact'}`;
                    } else if (innerMsg.locationMessage) {
                        text = `📍 Location shared`;
                    }

                    if (!text && messageType === 'text') continue;

                    let mediaUrl = null;
                    let savedFileName = fileName;

                    // If message contains media (image, document, video, audio), download and store in public storage
                    if (innerMsg.imageMessage || innerMsg.documentMessage || innerMsg.videoMessage || innerMsg.audioMessage) {
                        try {
                            const mediaWrapper = {
                                key: msg.key,
                                message: innerMsg
                            };
                            let buffer = null;
                            try {
                                buffer = await downloadMediaMessage(
                                    mediaWrapper,
                                    'buffer',
                                    {},
                                    {
                                        logger: pino({ level: 'silent' }),
                                        reuploadRequest: (update) => sock.updateMediaMessage(update)
                                    }
                                );
                            } catch (e1) {
                                // Fallback to raw msg
                                buffer = await downloadMediaMessage(
                                    msg,
                                    'buffer',
                                    {},
                                    {
                                        logger: pino({ level: 'silent' }),
                                        reuploadRequest: (update) => sock.updateMediaMessage(update)
                                    }
                                );
                            }

                            if (buffer && buffer.length > 0) {
                                let ext = '.bin';
                                if (innerMsg.imageMessage) {
                                    const mime = (innerMsg.imageMessage.mimetype || '').toLowerCase();
                                    if (mime.includes('png')) ext = '.png';
                                    else if (mime.includes('webp')) ext = '.webp';
                                    else if (mime.includes('gif')) ext = '.gif';
                                    else ext = '.jpg';
                                } else if (innerMsg.documentMessage) {
                                    const origName = innerMsg.documentMessage.fileName || '';
                                    const rawExt = path.extname(origName).toLowerCase();
                                    const safeDocExts = ['.pdf', '.txt', '.doc', '.docx', '.xls', '.xlsx'];
                                    ext = safeDocExts.includes(rawExt) ? rawExt : '.bin';
                                } else if (innerMsg.videoMessage) {
                                    ext = '.mp4';
                                } else if (innerMsg.audioMessage) {
                                    ext = '.mp3';
                                }

                                const safeId = (messageId || Date.now().toString()).replace(/[^a-zA-Z0-9_-]/g, '').substring(0, 16);
                                const diskFileName = `wa_${messageType}_${Date.now()}_${safeId}${ext}`;
                                const targetDir = path.resolve(__dirname, '../storage/app/public/whatsapp_media');
                                if (!fs.existsSync(targetDir)) {
                                    fs.mkdirSync(targetDir, { recursive: true, mode: 0o775 });
                                }
                                const fullFilePath = path.join(targetDir, diskFileName);
                                fs.writeFileSync(fullFilePath, buffer);
                                try { fs.chmodSync(fullFilePath, 0o664); } catch (_) {}

                                mediaUrl = `/storage/whatsapp_media/${diskFileName}`;
                                savedFileName = fileName || diskFileName;
                                console.log(`[MEDIA DOWNLOADED] Phone: ${cleanPhone} | Type: ${messageType} | Size: ${buffer.length} bytes | URL: ${mediaUrl}`);
                            }
                        } catch (mediaErr) {
                            console.warn(`[MEDIA DOWNLOAD FAILED] Could not download media for message ${messageId}:`, mediaErr.message);
                        }
                    }

                    console.log(`[WHATSAPP MESSAGE] ${fromMe ? 'OUTBOUND' : 'INBOUND'} | Phone: ${cleanPhone} | Name: ${pushName || 'Customer'} | Msg: ${text.substring(0, 50)}${mediaUrl ? ` | Media: ${mediaUrl}` : ''}`);

                    // Forward to Laravel Webhook
                    try {
                        const webhookResp = await fetch('http://127.0.0.1:8000/api/whatsapp/webhook', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Internal-Secret': INTERNAL_SECRET
                            },
                            body: JSON.stringify({
                                message_id: messageId,
                                remote_jid: rawJid,
                                phone: cleanPhone,
                                from_me: fromMe,
                                sender_name: pushName,
                                message_type: messageType,
                                message_text: text,
                                media_url: mediaUrl,
                                media_filename: savedFileName,
                                timestamp: unixSeconds
                            })
                        });
                        const webhookData = await webhookResp.json();
                        console.log(`[WEBHOOK SUCCESS] Phone: ${cleanPhone} | Webhook responded:`, webhookData);
                    } catch (fErr) {
                        console.error(`[WEBHOOK ERROR] Failed forwarding to Laravel:`, fErr.message);
                    }
                }
            } catch (err) {
                console.error('[MESSAGES.UPSERT ERROR]', err);
            }
        });

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                qrCodeData = qr;
                try {
                    qrCodeBase64 = await QRCode.toDataURL(qr, { width: 300, margin: 2 });
                } catch (e) {
                    qrCodeBase64 = null;
                }
                isConnected = false;
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
                isConnected = false;
                qrCodeData = null;
                qrCodeBase64 = null;
                connectedUser = null;

                if (shouldReconnect) {
                    isStarting = false;
                    setTimeout(startWhatsApp, 3000);
                } else {
                    try {
                        fs.rmSync(authDir, { recursive: true, force: true });
                    } catch (e) {}
                    isStarting = false;
                    setTimeout(startWhatsApp, 2000);
                }
            } else if (connection === 'open') {
                const rawUser = sock.user?.id ? sock.user.id.split(':')[0] : 'Connected';

                // Enforce strict authorized shop phone matching
                if (allowedPhone && rawUser !== 'Connected') {
                    const cleanConnected = String(rawUser).replace(/[^0-9]/g, '');
                    const cleanAllowed = String(allowedPhone).replace(/[^0-9]/g, '');
                    const last10Connected = cleanConnected.slice(-10);
                    const last10Allowed = cleanAllowed.slice(-10);

                    if (last10Connected !== last10Allowed) {
                        console.warn(`[SECURITY REJECTION] Scanned WhatsApp number (+${cleanConnected}) does NOT match authorized shop number (+${cleanAllowed}). Disconnecting immediately!`);
                        rejectedReason = `Security Alert: The scanned WhatsApp account (+${cleanConnected}) does not match your authorized shop WhatsApp number (+${cleanAllowed}). Connection rejected for security!`;
                        isConnected = false;
                        connectedUser = null;
                        isStarting = false;
                        try { await sock.logout(); } catch (_) {}
                        try { fs.rmSync(authDir, { recursive: true, force: true }); } catch (_) {}
                        setTimeout(startWhatsApp, 2000);
                        return;
                    }
                }

                if (!isConnected) {
                    console.log('WhatsApp Service Connected successfully for Authorized Shop User:', rawUser);
                }
                rejectedReason = null;
                isConnected = true;
                qrCodeData = null;
                qrCodeBase64 = null;
                connectedUser = rawUser;
                isStarting = false;
            }
        });
    } catch (e) {
        console.error('Error starting WhatsApp socket:', e);
        isStarting = false;
        setTimeout(startWhatsApp, 5000);
    }
}

// Routes
app.get('/status', async (req, res) => {
    if (req.query.allowed_phone) {
        allowedPhone = String(req.query.allowed_phone).trim();
    }

    // Double-check active session against authorized number
    if (isConnected && connectedUser && allowedPhone && connectedUser !== 'Connected') {
        const cleanConnected = String(connectedUser).replace(/[^0-9]/g, '');
        const cleanAllowed = String(allowedPhone).replace(/[^0-9]/g, '');
        const last10Connected = cleanConnected.slice(-10);
        const last10Allowed = cleanAllowed.slice(-10);

        if (last10Connected !== last10Allowed) {
            console.warn(`[SECURITY AUTO-LOGOUT] Active WhatsApp user (+${cleanConnected}) does not match authorized shop number (+${cleanAllowed}). Logging out!`);
            rejectedReason = `Security Alert: The connected WhatsApp account (+${cleanConnected}) does not match your authorized shop WhatsApp number (+${cleanAllowed}). Disconnected automatically for security!`;
            isConnected = false;
            connectedUser = null;
            try { await sock.logout(); } catch (_) {}
            try { fs.rmSync(authDir, { recursive: true, force: true }); } catch (_) {}
            setTimeout(startWhatsApp, 2000);
            return res.json({
                connected: false,
                user: null,
                qr: qrCodeBase64,
                allowedPhone: allowedPhone,
                rejectedReason: rejectedReason
            });
        }
    }

    res.json({
        connected: isConnected,
        user: connectedUser,
        qr: qrCodeBase64,
        allowedPhone: allowedPhone,
        rejectedReason: rejectedReason
    });
});

app.post('/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({ success: false, error: 'Phone and message are required.' });
        }

        if (!isConnected || !sock) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp service is not connected. Please scan QR in Admin to connect.'
            });
        }

        let cleanPhone = String(phone).replace(/[^0-9]/g, '');
        if (cleanPhone.length === 10) {
            cleanPhone = '91' + cleanPhone;
        }

        const jid = `${cleanPhone}@s.whatsapp.net`;

        // Check if number exists on WhatsApp
        try {
            const waCheck = await sock.onWhatsApp(jid);
            if (waCheck && waCheck.length > 0 && !waCheck[0].exists) {
                return res.status(404).json({
                    success: false,
                    notOnWhatsApp: true,
                    error: `The customer number (+${cleanPhone}) is not registered on WhatsApp. Please call the customer directly.`
                });
            }
        } catch (checkErr) {
            console.warn('onWhatsApp check warning:', checkErr.message);
        }

        // Simulate natural human typing presence
        try {
            await sock.sendPresenceUpdate('composing', jid);
            await new Promise(r => setTimeout(r, 600));
            await sock.sendPresenceUpdate('paused', jid);
        } catch (_) {}

        const result = await sock.sendMessage(jid, { text: String(message) });

        return res.json({
            success: true,
            phone: cleanPhone,
            messageId: result?.key?.id || null
        });
    } catch (err) {
        console.error('Send message error:', err);
        return res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/send-document', async (req, res) => {
    try {
        const { phone, filePath, base64, fileName, caption, mimetype } = req.body;

        if (!phone) {
            return res.status(400).json({ success: false, error: 'Phone is required.' });
        }

        if (!isConnected || !sock) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp service is not connected. Please scan QR in Admin to connect.'
            });
        }

        let buffer = null;
        if (filePath) {
            if (!isSafeFilePath(filePath)) {
                return res.status(403).json({ success: false, error: 'Forbidden: Requested file path is outside allowed storage directory.' });
            }
            if (fs.existsSync(filePath)) {
                buffer = fs.readFileSync(filePath);
            } else {
                return res.status(404).json({ success: false, error: 'Specified document file not found.' });
            }
        } else if (base64) {
            buffer = Buffer.from(base64, 'base64');
        } else {
            return res.status(400).json({ success: false, error: 'Valid filePath or base64 file data required.' });
        }

        let cleanPhone = String(phone).replace(/[^0-9]/g, '');
        if (cleanPhone.length === 10) {
            cleanPhone = '91' + cleanPhone;
        }

        const jid = `${cleanPhone}@s.whatsapp.net`;

        // Check if number exists on WhatsApp
        try {
            const waCheck = await sock.onWhatsApp(jid);
            if (waCheck && waCheck.length > 0 && !waCheck[0].exists) {
                return res.status(404).json({
                    success: false,
                    notOnWhatsApp: true,
                    error: `The customer number (+${cleanPhone}) is not registered on WhatsApp. Please call the customer directly.`
                });
            }
        } catch (checkErr) {
            console.warn('onWhatsApp check warning:', checkErr.message);
        }

        const result = await sock.sendMessage(jid, {
            document: buffer,
            mimetype: mimetype || 'application/pdf',
            fileName: fileName || 'Invoice.pdf',
            caption: caption ? String(caption) : undefined
        });

        return res.json({
            success: true,
            phone: cleanPhone,
            messageId: result?.key?.id || null
        });
    } catch (err) {
        console.error('Send document error:', err);
        return res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/send-image', async (req, res) => {
    try {
        const { phone, filePath, base64, caption } = req.body;

        if (!phone) {
            return res.status(400).json({ success: false, error: 'Phone is required.' });
        }

        if (!isConnected || !sock) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp service is not connected. Please scan QR in Admin to connect.'
            });
        }

        let buffer = null;
        if (filePath) {
            if (!isSafeFilePath(filePath)) {
                return res.status(403).json({ success: false, error: 'Forbidden: Requested file path is outside allowed storage directory.' });
            }
            if (fs.existsSync(filePath)) {
                buffer = fs.readFileSync(filePath);
            } else {
                return res.status(404).json({ success: false, error: 'Specified image file not found.' });
            }
        } else if (base64) {
            buffer = Buffer.from(base64, 'base64');
        } else {
            return res.status(400).json({ success: false, error: 'Valid filePath or base64 image data required.' });
        }

        let cleanPhone = String(phone).replace(/[^0-9]/g, '');
        if (cleanPhone.length === 10) {
            cleanPhone = '91' + cleanPhone;
        }

        const jid = `${cleanPhone}@s.whatsapp.net`;

        // Check if number exists on WhatsApp
        try {
            const waCheck = await sock.onWhatsApp(jid);
            if (waCheck && waCheck.length > 0 && !waCheck[0].exists) {
                return res.status(404).json({
                    success: false,
                    notOnWhatsApp: true,
                    error: `The customer number (+${cleanPhone}) is not registered on WhatsApp. Please call the customer directly.`
                });
            }
        } catch (checkErr) {
            console.warn('onWhatsApp check warning:', checkErr.message);
        }

        const result = await sock.sendMessage(jid, {
            image: buffer,
            caption: caption ? String(caption) : undefined
        });

        return res.json({
            success: true,
            phone: cleanPhone,
            messageId: result?.key?.id || null
        });
    } catch (err) {
        console.error('Send image error:', err);
        return res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/clear-alert', (req, res) => {
    rejectedReason = null;
    return res.json({ success: true });
});

app.post('/logout', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
        }
        try {
            fs.rmSync(authDir, { recursive: true, force: true });
        } catch (e) {}
        isConnected = false;
        qrCodeBase64 = null;
        rejectedReason = null;
        startWhatsApp();
        return res.json({ success: true, message: 'Logged out successfully. Generating new QR...' });
    } catch (err) {
        return res.status(500).json({ success: false, error: err.message });
    }
});

const server = app.listen(PORT, '127.0.0.1', () => {
    console.log(`WhatsApp Microservice running on http://127.0.0.1:${PORT}`);
    startWhatsApp();
});

server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
        console.error(`[ERROR] Port ${PORT} is already in use by another instance! Exiting to prevent WhatsApp session conflicts.`);
        process.exit(1);
    } else {
        console.error('[SERVER ERROR]', err);
    }
});
