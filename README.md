# GameTracker

A Laravel application for tracking game events and players.

## Setup

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Configure your database in the `.env` file
6. Run migrations:
   ```bash
   php artisan migrate
   ```
7. Seed the database with initial data:
   ```bash
   php artisan db:seed
   ```
8. Start the development server:
   ```bash
   composer dev
   ```

## Demo Data

To quickly set up demo data for testing and development purposes, run:

```bash
php artisan demo:setup
```

This command will:
- Create demo users (admin and regular users)
- Create demo events (past, current, and upcoming)
- Create demo games for each event
- Create demo players (users participating in events)
- Set up relationships between games and players

After running the command, you can log in with the following credentials:
- Admin: admin@example.com / password
- User: user@example.com / password

### Options

- `--force`: Force the operation to run even in production environment

### Safety Features

The command includes several safety features:
- Confirmation prompt before proceeding
- Production environment check with --force option requirement
- Database transaction to ensure data integrity
- Comprehensive error handling with troubleshooting tips

## Testing

Run tests with:

```bash
./vendor/bin/pest
```

## License

This project is licensed under the MIT License.
