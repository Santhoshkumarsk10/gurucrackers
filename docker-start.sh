#!/bin/bash
set -e

# Render binds to a dynamic web port given by $PORT (usually 10000)
WEB_PORT="${PORT:-80}"
echo "Configuring Apache to listen on port ${WEB_PORT}..."
sed -i "s/80/${WEB_PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

# Remove any existing symlink and cleanly recreate storage link
rm -rf /var/www/html/public/storage
php artisan storage:link || true

# Run database migrations automatically on deployment
php artisan migrate --force || true

# Restore WhatsApp session from MySQL database if available before starting Node service
echo "Restoring WhatsApp session from database if available..."
mkdir -p /var/www/html/whatsapp-service/auth_info
chmod -R 777 /var/www/html/whatsapp-service/auth_info || true
php artisan whatsapp:restore-session --force || true
chmod -R 777 /var/www/html/whatsapp-service/auth_info || true

# Cache Laravel configuration, routes, and views
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Node.js WhatsApp background service strictly on internal port 3001 with correct dynamic webhook URL
echo "Starting WhatsApp background service on internal port 3001..."
(cd /var/www/html/whatsapp-service && env PORT=3001 WHATSAPP_PORT=3001 WEB_PORT="${WEB_PORT}" LARAVEL_WEBHOOK_URL="http://127.0.0.1:${WEB_PORT}/api/whatsapp/webhook" node server.js) &

# Ensure storage directories, logs, and bootstrap/cache exist and are fully owned/writable by Apache (www-data)
echo "Fixing storage and log permissions for www-data..."
mkdir -p /var/www/html/storage/app/db_backups \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
touch /var/www/html/storage/logs/laravel.log
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
chmod 666 /var/www/html/storage/logs/*.log 2>/dev/null || true

# Start Apache in the foreground
echo "Starting Apache web server on port ${WEB_PORT}..."
exec apache2-foreground
