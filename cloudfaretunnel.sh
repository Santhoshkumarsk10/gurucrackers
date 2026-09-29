#!/bin/bash
# ==============================================================================
# Guru Crackers - Instant Live Demo Sharing via Cloudflare Tunnel
# 100% Free, Secure HTTPS Tunnel with zero port forwarding or credit card
# ==============================================================================

echo "🚀 Starting Cloudflare Live Tunnel for Guru Crackers..."

# Ensure cloudflared is accessible
export PATH="$HOME/.local/bin:$PATH"

if ! command -v cloudflared &> /dev/null; then
    echo "❌ cloudflared not found in PATH. Please run: curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o ~/.local/bin/cloudflared && chmod +x ~/.local/bin/cloudflared"
    exit 1
fi

echo "✨ Connecting your local server (http://127.0.0.1:8000) to Cloudflare..."
echo "📱 Share the generated https://*.trycloudflare.com URL with your clients!"
echo "----------------------------------------------------------------------"

cloudflared tunnel --url http://127.0.0.1:8000
