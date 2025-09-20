# Laravel-Foreign-Exchange-Web-App

## Setup Instructions

### Step 1: Clone the repository
```bash
git clone https://github.com/OfficialKardos/Foreign-Exchange-App-Laravel.git
cd Foreign-Exchange-App-Laravel
```

### Step 2: Install dependencies
```bash
composer install
npm install
```

### Step 3: Create .env file
```bash
cp .env.example .env
```

### Step 4: Generate Key
```bash
php artisan key:generate
```

### Step 5: Run database migrations & seeder
```bash
php artisan migrate
php artisan db:seed --class=CurrencySeeder
```

### Step 6 run currency API:
```bash
php artisan exchange:update-rates run
```

### Step 6 Build dev environment
```bash
npm run dev
```

### Step 6 Start the server
```bash
php artisan serve
```