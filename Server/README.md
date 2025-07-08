Server on Laravel

## Setup Requirements
- PHP > 8.3
- php-fpm
- Composer
- NGINX

## Installation Steps

### PHP and Composer Setup
```bash
composer install
```

### Environment Configuration
```bash
cp .env.example .env
# Edit .env to configure database connection
```

### Database Setup
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```



### NGINX Configuration
```bash
# Add configuration to sites-available
sudo ln -s /etc/nginx/sites-available/your-config /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```