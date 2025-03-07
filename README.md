## Online Access

You can access live web app at;

https://simulation.olgunportfolio.com

## Requirements

For run this application, you need:
- PHP 8.1 or higher
- Composer
- Node.js and npm
- PHP SQLite driver

## How to Install and Run

Follow these steps for setup application in your local computer.

### 1. Clone the Repository

First, you must download code to your computer:

```bash
git clone https://github.com/olgunozoktas/football-leauge-simulator.git
cd football-leauge-simulation
```

### 2. Install PHP Dependencies

Now we install all PHP packages with Composer:

```bash
composer install
```

If you have problem, maybe try:

```bash
composer install --ignore-platform-reqs
```

### 3. Setup Environment

Copy example environment file and generate application key:

```bash
cp .env.example .env
php artisan key:generate
```

Now open `.env` file and configure database settings. You can use SQLite for easy setup:

```
DB_CONNECTION=sqlite
# Comment out or remove these lines:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

If you want use SQLite, also create empty database file:

```bash
touch database/database.sqlite
```

### 4. Run Migrations

Now we create database tables:

```bash
php artisan migrate
```

If you want fresh database, use:

```bash
php artisan migrate:fresh
```

### 5. Install JavaScript Dependencies

Now we install all frontend packages:

```bash
npm install
```

### 6. Build and Run Frontend

For development mode with hot reload (very nice for development!):

```bash
npm run dev
```

For production build:

```bash
npm run build
```

### 7. Run Laravel Server

In new terminal window, run Laravel server:

```bash
php artisan serve
```

Now you can access application at `http://localhost:8000` in your browser!

## How to Use Application

1. When you first open application, you see dashboard with teams and fixtures
2. Click "Initialize Teams" button for create teams if not exist
3. Click "Generate Fixtures" button for create match schedule
4. Use "Play Next Week" button for simulate next week matches
5. Or use "Play All" button for simulate all matches at once
6. You can see standings table and match results
7. After week 4, you can see championship predictions!

## Running Tests

For run all tests and make sure everything working good:

```bash
php artisan test
```

## Troubleshooting

If you have problem with application, try these solutions:

1. Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

2. If database problems, try fresh migration:
```bash
php artisan migrate:fresh
```

3. If frontend not working, try rebuild:
```bash
npm run build
```

4. If still have problem, please open issue on GitHub or contact me!

## Enjoy!

I hope you enjoy this football simulation! May best team win! 🏆
