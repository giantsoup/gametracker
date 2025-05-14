# GameTracker Development Guidelines

This document provides essential information for developers working on the GameTracker project.

## Build/Configuration Instructions

### Prerequisites
- PHP 8.4 or higher
- Composer
- Node.js and npm
- A database (SQLite, MySQL, PostgreSQL)

### Setup Steps

1. **Clone the repository**

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Configure your database connection in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gametracker
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   # Using the dev script that runs multiple services concurrently
   composer dev
   
   # Or run services individually
   php artisan serve  # Laravel server
   npm run dev        # Vite for frontend assets
   ```

## Testing Information

### Testing Configuration

The project uses Pest PHP (built on PHPUnit) for testing. Tests are configured to use an in-memory SQLite database by default.

### Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run a specific test file
./vendor/bin/pest tests/Unit/UserTest.php

# Run tests with coverage report (requires Xdebug or PCOV)
./vendor/bin/pest --coverage
```

### Adding New Tests

1. **Create a new test file**
   - Unit tests go in `tests/Unit/`
   - Feature tests go in `tests/Feature/`
   - Follow the naming convention: `{Subject}Test.php`

2. **Write your test using Pest syntax**
   ```php
   <?php
   
   use App\Models\User;
   
   test('user initials method returns correct initials', function () {
       $user = new User(['name' => 'John Doe']);
       expect($user->initials())->toBe('JD');
   });
   ```

3. **Run your test to verify it works**
   ```bash
   ./vendor/bin/pest tests/Path/To/YourTest.php
   ```

### Test Example

Here's a simple test for the User model's `initials()` method:

```php
<?php

use App\Models\User;

test('user initials method returns correct initials', function () {
    // Create a user with a name
    $user = new User(['name' => 'John Doe']);
    
    // Test that the initials method returns the correct initials
    expect($user->initials())->toBe('JD');
    
    // Test with a different name
    $user->name = 'Jane Smith';
    expect($user->initials())->toBe('JS');
    
    // Test with a single name
    $user->name = 'Madonna';
    expect($user->initials())->toBe('M');
    
    // Test with multiple names
    $user->name = 'John James Doe Smith';
    expect($user->initials())->toBe('JJDS');
});
```

## Additional Development Information

### Code Style

The project follows Laravel's coding standards. You can use Laravel Pint to format your code:

```bash
./vendor/bin/pint
```

### Project Structure

- **Livewire Components**: Located in `app/Livewire/`
- **Models**: Located in `app/Models/`
- **Controllers**: Located in `app/Http/Controllers/`
- **Views**: Located in `resources/views/`
  - Livewire component views: `resources/views/livewire/`
  - Flux component views: `resources/views/flux/`

### Key Components

- **CrudTable**: A reusable Livewire component for displaying and managing data tables (`app/Livewire/CrudTable.php`)
- **User Model**: Includes role management with an enum-based approach (`app/Models/User.php`)
- **UserRole Enum**: Defines user roles and provides helper methods (`app/Enums/UserRole.php`)

### Debugging

- Laravel Pail is included for improved logging and debugging
- Run `composer dev` to start the development environment with logs visible

### Frontend Assets

The project uses Vite for asset compilation:

```bash
# Development
npm run dev

# Production build
npm run build
```

### Database

- The project uses Laravel's migration system for database schema management
- Soft deletes are implemented on the User model
