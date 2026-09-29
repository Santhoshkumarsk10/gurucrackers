#!/bin/bash
# ==============================================================================
# Guru Crackers - Fixed Subdomain Live Sharing via Localtunnel
# Fixed URL: https://gurucrackers.loca.lt (Does not change!)
# 100% Free, Secure HTTPS Tunnel with zero domain or credit card needed
# ==============================================================================

PORT=8000
SUBDOMAIN="gurucrackers"

echo "======================================================================"
echo "🚀 Starting Fixed Subdomain Live Tunnel for Guru Crackers..."
echo "======================================================================"

# Fetch public IP for Localtunnel verification if prompted on first browser visit
PUBLIC_IP=$(curl -s https://loca.lt/mytunnelpassword 2>/dev/null || curl -s https://ipv4.icanhazip.com 2>/dev/null)

echo "✨ Fixed Live URL  : https://${SUBDOMAIN}.loca.lt"
if [ -n "$PUBLIC_IP" ]; then
    echo "🔑 One-time Password: $PUBLIC_IP (Enter this once if browser asks)"
fi
echo "📱 Share this exact link with your clients — it will NOT change!"
echo "======================================================================"
echo "Connecting local server (http://127.0.0.1:${PORT}) to https://${SUBDOMAIN}.loca.lt ..."
echo ""

# Run localtunnel with fixed subdomain
npx -y localtunnel --port $PORT --subdomain $SUBDOMAIN
