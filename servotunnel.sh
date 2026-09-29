#!/bin/bash
# ==============================================================================
# Guru Crackers - Serveo SSH Live Tunnel
# Target URL: https://gurucrackers.serveo.net
# ==============================================================================

PORT=8000
SUBDOMAIN="gurucrackers"

echo "======================================================================"
echo "🚀 Starting Serveo SSH Tunnel for Guru Crackers..."
echo "======================================================================"
echo "✨ Target URL: https://${SUBDOMAIN}.serveo.net"
echo "Connecting local server (http://127.0.0.1:${PORT}) to Serveo..."
echo "======================================================================"

# SSH Remote Port Forward to Serveo
ssh -o StrictHostKeyChecking=no -o ServerAliveInterval=60 -R ${SUBDOMAIN}:80:localhost:${PORT} serveo.net
