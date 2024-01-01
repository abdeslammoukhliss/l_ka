
# Use an official httpd base image
FROM php:apache

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the Laravel project files into the container

RUN apt-get update \
    && apt-get install -y libonig-dev git unzip \
    && rm -rf /var/lib/apt/lists/*



# Clone the GitHub repository using the access token
COPY . /var/www/html
COPY .env.example .env

# Set Laravel environment variables
ENV APP_KEY=your_app_key
ENV DB_CONNECTION=mysql
ENV DB_HOST=127.0.0.1
ENV DB_PORT=3306
ENV DB_DATABASE=forge
ENV DB_USERNAME=root
ENV DB_PASSWORD=

# Expose port 80
EXPOSE 8000
RUN a2enmod rewrite
RUN docker-php-ext-install pdo mbstring
RUN docker-php-ext-install mysqli pdo_mysql
RUN php composer.phar install
CMD php artisan serve --host=0.0.0.0 --port=8000
