# Project Setup

## Prerequisites
Ensure you have the following installed:
- PHP (>=8.2)
- Composer
- MySQL or PostgreSQL
- Git

## Setting Up a New Laravel Project
### 1. Clone the Repository
```sh
git clone https://github.com/proficient-pravin/impero-task.git
cd impero-task
```

### 2. Install Dependencies
```sh
composer install
```

### 3. Environment Configuration
```sh
cp .env.example .env
```
Update `.env` file with database credentials and other configurations.

### 4. Generate Application Key
```sh
php artisan key:generate
```

### 6. Start Development Server
```sh
php artisan serve
```
