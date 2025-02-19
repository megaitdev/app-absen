# Laravel 11 with Stisla Template

Welcome to the repository for Laravel 11 integrated with Stisla Bootstrap 5 template!

## Overview

This repository contains a Laravel 11 template that comes pre-configured with authentication from Breeze and utilizes the Stisla Bootstrap template for the UI. An this library used in this project :

-   Bootstrap 4.6.1
-   Fontawesome 5.15.4
-   ChartJs 2.9.4
-   Jquery 3.6.0
-   Datatables 1.10.18
-   Sweetalert 2.1.2
-   etc

## Installation Guide

### Prerequisites

-   PHP >= 8.2
-   Composer
-   Node.js
-   npm

### Installation Steps

1. Clone the repository:

    ```bash
    git clone https://github.com/megaitdev/laravel11-stisla.git
    ```

2. Navigate into the project directory:

    ```bash
    cd laravel11-stisla
    ```

3. Install PHP dependencies:

    ```bash
    composer install
    ```

4. Edit Credential Information
   Edit AuthenticatesUsers, on path vendor/laravel/ui/auth-backend/AuthenticatesUsers

    ```php
    public function username()
    {
        return 'username';
    }
    ```

5. Copy the `.env.example` file and rename it to `.env`:

    ```bash
    cp .env.example .env
    ```

6. Generate application key:

    ```bash
    php artisan key:generate
    ```

7. Run database migrations:

    ```bash
    php artisan migrate
    ```

8. Install JavaScript dependencies & run vite:

    ```bash
    npm install
    ```

    ```bash
    npm run dev
    ```

9. Start the development server:
    ```bash
    php artisan serve
    ```
