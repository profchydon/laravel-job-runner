#!/bin/bash

echo "Starting Laravel Sail with PostgreSQL and Vite setup..."

# Copy env.example to .env if it doesn't exist
if [ ! -f .env ]; then
    echo "Copying env.example to .env..."
    cp .env.example .env
else
    echo ".env file already exists. Skipping copy."
fi

# Ensure Sail is installed
if ! [ -x "$(command -v sail)" ]; then
    echo "Sail is not installed. Installing Sail..."
    composer require laravel/sail --dev
    php artisan sail:install
fi

# Bring up Docker containers
echo "Bringing up Docker containers..."
./vendor/bin/sail up -d

# Ensure npm dependencies are installed
echo "Installing npm dependencies..."
./vendor/bin/sail npm install

# Run Vite build
echo "Building assets with Vite..."
./vendor/bin/sail npm run build

# Run migrations and seed database
echo "Setting up database..."
./vendor/bin/sail artisan migrate

# Generate application key
echo "Generating application key..."
./vendor/bin/sail artisan key:generate

# Clear and cache Laravel configuration
echo "Clearing and caching Laravel configuration..."
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan config:cache

# Start background jobs
echo "Running background jobs..."
./vendor/bin/sail artisan job:run-background

echo "Setup completed successfully! Access your app at http://localhost."
