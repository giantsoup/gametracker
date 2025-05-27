# GameTracker

A Laravel application for tracking board game events, players, and games played during those events.

## Overview

GameTracker is a web application built with Laravel and Livewire that helps organizers manage gaming events. The application allows tracking of:

- **Events**: Gaming sessions with start and end times
- **Players**: Users who participate in events (with optional custom nicknames)
- **Games**: Board games played during events, including duration tracking
- **Game Ownership**: Which players brought which games to events

### Key Features

- User management with role-based permissions (Admin and User roles)
- Event management (create, update, delete, start, end)
- Player participation tracking (join, leave)
- Game tracking with duration metrics
- Admin dashboard with settings, analytics, notifications, and logs
- User settings for profile, password, and appearance
- Responsive UI built with Livewire and Flux components
- Comprehensive test suite using Pest PHP

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
5. Configure your database in the `.env` file (SQLite is the default)
   ```
   # For SQLite (default)
   DB_CONNECTION=sqlite
   # Touch the database file if it doesn't exist
   touch database/database.sqlite

   # For MySQL
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gametracker
   DB_USERNAME=root
   DB_PASSWORD=
   ```
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

## Development

### Requirements

- PHP 8.4 or higher
- Composer
- Node.js and npm
- A database (SQLite, MySQL, PostgreSQL)

### Project Structure

- **Models**: Located in `app/Models/`
  - `User.php`: User accounts with role management
  - `Event.php`: Gaming events with start/end times
  - `Player.php`: Users participating in events
  - `Game.php`: Games played during events

- **Controllers**: Located in `app/Http/Controllers/`
  - `EventController.php`: Handles event viewing
  - Admin controllers in `app/Http/Controllers/Admin/`
    - `AdminController.php`: Admin dashboard features
    - `UserController.php`: User management
    - `EventController.php`: Event management

- **Livewire Components**: Located in `app/Livewire/`
  - Event management components
  - Player management components
  - Game management components
  - Settings components

- **Routes**: Defined in `routes/web.php`
  - Public routes
  - Authenticated user routes
  - Admin-only routes

### Running Tests

The project uses Pest PHP for testing:

```bash
# Run all tests
./vendor/bin/pest

# Run a specific test file
./vendor/bin/pest tests/Feature/PlayerTest.php

# Run tests with coverage report
./vendor/bin/pest --coverage
```

### Code Style

The project follows Laravel's coding standards. You can use Laravel Pint to format your code:

```bash
./vendor/bin/pint
```

## License

This project is licensed under the MIT License.
