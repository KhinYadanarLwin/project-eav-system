# Project Entity-Attribute-Value System

## Setup Instructions

### Prerequisites
Make sure you have the following installed on your system:
- PHP (Recommended version: 8.0+)
- Composer
- MySQL (or any supported database)
- Laravel (if not included via Composer)

### Installation Steps
1. **Clone the repository**
   ```sh
   git clone <your-repo-url>
   cd <your-project-folder>
   ```

2. **Install dependencies**
   ```sh
   composer install
   ```

3. **Set up the environment file**
   ```sh
   cp .env.example .env
   ```
   - Configure database settings in the `.env` file.
   - SQL dump file with database name 'project_eav_system' can be found in the dropbox.
  (Note: if you already used databse seeding, no need to import SQL dump file)

4. **Generate application key**
   ```sh
   php artisan key:generate
   ```

5. **Run migrations and seed the database**
   ```sh
   php artisan migrate --seed
   ```
6. Install Passport
    php artisan passport:install
   
7. **Serve the application**
   ```sh
   php artisan serve
   ```
   The application should now be running at `http://127.0.0.1:8000`.

## API Documentation

For API documentation, please visit:
[Postman API Docs](https://documenter.getpostman.com/view/17132551/2sAYdkG8GC)

## Test Credentials
Use the following credentials for testing:

- **Admin User**
  - Email: `super_admin@eav.com`
  - Password: `12345678`
