#!/bin/bash
set -e

# Render binds to a dynamic web port given by $PORT (usually 10000)
WEB_PORT="${PORT:-80}"
echo "Configuring Apache to listen on port ${WEB_PORT}..."
sed -i "s/80/${WEB_PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

# Remove any existing symlink and cleanly recreate storage link
rm -rf /var/www/html/public/storage
php artisan storage:link || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/public

# Cache Laravel configuration, routes, and views
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Node.js WhatsApp background service strictly on internal port 3001
echo "Starting WhatsApp background service on internal port 3001..."
(cd /var/www/html/whatsapp-service && env PORT=3001 WHATSAPP_PORT=3001 node server.js) &

# Start Apache in the foreground
echo "Starting Apache web server on port ${WEB_PORT}..."
exec apache2-foreground
