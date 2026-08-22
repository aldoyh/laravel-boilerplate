# Laravel Livewire Starter Kit

A modern, opinionated Laravel application starter kit built with **Livewire 4** and **Flux UI**, featuring authentication via **Laravel Fortify**, admin dashboard via **Orchid Platform**, and production-ready tooling.

## 🚀 Features

- **Livewire 4** - Build dynamic, reactive interfaces without JavaScript
- **Flux UI** - Modern, accessible UI components for Livewire
- **Laravel Fortify** - Out-of-the-box authentication scaffolding
- **Orchid Platform** - Powerful admin panel framework
- **Tailwind CSS 4** - Utility-first CSS framework
- **Pest Testing** - Modern PHP testing framework
- **Laravel Pint** - Code style fixer for PHP
- **Laravel Boost** - Development tools and MCP integration
- **Laravel Pail** - Real-time log monitoring
- **Vite** - Fast frontend build tool

## 🛠️ Technology Stack

### Backend
- **PHP 8.3+** - Server-side language
- **Laravel 13** - Web framework
- **Laravel Fortify v1** - Authentication
- **Livewire 4** - Reactive component library
- **Orchid Platform 14** - Admin interface
- **Database** - SQLite (configurable to MySQL, PostgreSQL, etc.)

### Frontend
- **Flux UI v2** - Component library for Livewire
- **Tailwind CSS 4** - Styling
- **Vite 8** - Build tool
- **Alpine.js** - Lightweight JavaScript framework

### Development & Testing
- **Pest 4** - PHP testing framework
- **Laravel Pint 1** - Code formatter
- **Laravel Boost 2** - Development utilities
- **Laravel Sail 1** - Docker development environment
- **Laravel Pail 1** - Log monitoring

## 📋 Requirements

- PHP 8.3 or higher
- Composer
- Node.js 18+ and npm (for frontend assets)
- SQLite (default) or compatible relational database

## ⚡ Quick Start

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Or use the automated setup script:

```bash
composer run setup
```

### 3. Start Development

Run the development server with all services (Laravel server, queue listener, log monitoring, Vite):

```bash
composer run dev
```

This command starts:
- **Laravel Server** - http://localhost:8000
- **Queue Listener** - Background job processing
- **Pail Logs** - Real-time log viewer
- **Vite Dev Server** - Hot module replacement for frontend assets

## 🎯 Available Commands

### Setup & Installation
```bash
composer run setup          # Complete project setup
php artisan migrate         # Run database migrations
php artisan tinker          # Interactive PHP shell
```

### Development
```bash
composer run dev            # Start development environment with all services
npm run dev                 # Start Vite dev server (standalone)
npm run build               # Build assets for production
```

### Code Quality
```bash
composer run lint           # Format PHP code with Pint
composer run lint:check     # Check code style without fixing
composer run test           # Run tests with linting checks
php artisan test            # Run Pest tests only
php artisan test --compact  # Run tests with compact output
```

### Utilities
```bash
php artisan route:list      # Display all registered routes
php artisan config:show     # View configuration values
php artisan tinker          # Interactive PHP REPL
```

## 📁 Project Structure

```
├── app/
│   ├── Actions/             # Business logic actions
│   ├── Concerns/            # Shared traits and mixins
│   ├── Console/             # Artisan commands
│   ├── Enums/               # PHP enums
│   ├── Http/                # Controllers, middleware, requests
│   ├── Livewire/            # Livewire components
│   ├── Models/              # Eloquent models
│   ├── Notifications/       # Notification classes
│   ├── Orchid/              # Orchid admin dashboard
│   ├── Policies/            # Authorization policies
│   ├── Providers/           # Service providers
│   ├── Rules/               # Validation rules
│   └── Support/             # Helper utilities
├── config/                  # Application configuration
├── database/
│   ├── migrations/          # Database migrations
│   ├── factories/           # Model factories for testing
│   └── seeders/             # Database seeders
├── resources/
│   ├── css/                 # Tailwind styles
│   ├── js/                  # JavaScript/Alpine components
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── storage/                 # File storage
├── tests/                   # Test suite
├── bootstrap/               # Bootstrap files
├── public/                  # Publicly accessible files
└── vite.config.js          # Vite configuration
```

## 🔐 Authentication

Authentication is pre-configured with Laravel Fortify, providing:
- User registration
- Email verification
- Login/logout
- Password reset
- Two-factor authentication (optional)
- Account deletion

Visit `/register` to create an account and `/login` to sign in.

## 📊 Admin Dashboard

Orchid Platform provides a powerful admin interface. Access it at `/admin` (requires authentication and admin role).

## 🧪 Testing

This project uses **Pest** for testing. Tests are located in the `tests/` directory.

### Running Tests

```bash
# Run all tests
php artisan test

# Run tests with compact output
php artisan test --compact

# Run specific test file
php artisan test tests/Feature/SomeTest.php

# Run tests matching a filter
php artisan test --filter=testMethodName
```

### Creating Tests

```bash
# Create a feature test
php artisan make:test --pest FeatureNameTest

# Create a unit test
php artisan make:test --pest --unit UnitNameTest
```

## 🎨 Frontend Development

Frontend assets are managed with Vite and TailwindCSS.

### Compile Assets

```bash
# Development mode (with watch and hot reload)
npm run dev

# Production build
npm run build
```

### Using Tailwind

Tailwind CSS is configured in `resources/css/app.css`. Add utility classes directly to your Blade templates or Livewire components.

## 🚀 Deployment

### Using Laravel Cloud (Recommended)

The fastest way to deploy Laravel applications:

```bash
# Visit https://cloud.laravel.com/ to get started
```

### Manual Deployment

Before deploying, ensure:

```bash
# Run all checks
composer run ci:check

# Build frontend assets
npm run build

# Set production environment
# - Update .env with production credentials
# - Set APP_DEBUG=false
# - Set APP_ENV=production
```

Key steps:
1. Push code to your repository
2. SSH into your server
3. Clone the repository
4. Run `composer install --optimize-autoloader --no-dev`
5. Run `npm install && npm run build`
6. Copy `.env.example` to `.env` and configure
7. Run `php artisan key:generate`
8. Run `php artisan migrate --force`
9. Set proper file permissions on `storage/` and `bootstrap/cache/`
10. Configure your web server (Nginx/Apache)

## 🔧 Configuration

### Database

The default database is SQLite. To use MySQL or PostgreSQL:

1. Update `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Run migrations:
   ```bash
   php artisan migrate
   ```

### Mail

Configure mail in `.env`. The default is `log` (outputs to logs for development):

```
MAIL_MAILER=log
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_PORT=2525
# MAIL_USERNAME=...
# MAIL_PASSWORD=...
```

### Cache & Sessions

Configured to use the database by default. Update `.env` to use Redis for better performance:

```
CACHE_STORE=redis
SESSION_DRIVER=database
```

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Flux UI Documentation](https://fluxui.dev)
- [Orchid Platform Documentation](https://orchid.software)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Pest Documentation](https://pestphp.com)

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Write or update tests for your changes
5. Run tests and linting:
   ```bash
   composer run test
   ```
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Code Standards

This project follows PHP standards and uses Laravel Pint for code formatting:

```bash
# Format code
vendor/bin/pint

# Check code style
vendor/bin/pint --test
```

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🆘 Support

For issues and questions:
- Check the [Laravel documentation](https://laravel.com/docs)
- Check existing [GitHub issues](https://github.com/aldoyh/laravel-boilerplate/issues)
- Create a new issue with detailed information

## 🎓 Learning Resources

- [Laravel Beyond CRUD](https://laravel-beyond-crud.com/) - Advanced Laravel patterns
- [Laracasts](https://laracasts.com/) - Video tutorials
- [Laravel News](https://laravel-news.com/) - Latest Laravel news and updates
- [Livewire Screencasts](https://livewire.laravel.com/docs/quickstart) - Livewire tutorials
