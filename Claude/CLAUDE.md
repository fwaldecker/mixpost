# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Essential Commands

### Development
```bash
# Frontend development with Vite (hot reload)
npm run dev

# Build frontend assets for production
npm run build

# Run Laravel Horizon queue worker for scheduled posts
php artisan horizon

# Run scheduled posts manually
php artisan mixpost:run-scheduled-posts

# Clear caches
php artisan mixpost:clear-services-cache
php artisan mixpost:clear-settings-cache
```

### Testing & Quality
```bash
# Run test suite (Pest)
composer test

# Run tests with coverage
composer test-coverage

# Static analysis (PHPStan)
composer analyse

# Format code (Laravel Pint)
composer format
```

### Data Management
```bash
# Import account metrics and audience
php artisan mixpost:import-account-audience
php artisan mixpost:import-account-data

# Process metrics
php artisan mixpost:process-metrics

# Clean up old data
php artisan mixpost:delete-old-data
```

## Architecture Overview

Mixpost is a Laravel package (not a standalone application) for social media management that integrates into existing Laravel applications.

### Core Patterns
- **Service Layer**: Each external service (Facebook, Twitter, Mastodon, Tenor, Unsplash) has a dedicated service class in `src/Services/`
- **Provider Abstraction**: Social providers extend `SocialProvider` abstract class with standardized interfaces for authentication, posting, and data import
- **Media Pipeline**: Centralized media handling with conversions (resize, thumbnails) and temporary storage management
- **Job Queue Architecture**: Background jobs for publishing posts, importing metrics, processing media using Laravel Horizon

### Frontend Stack
- Vue.js 3 + Inertia.js for SPA-like experience
- TailwindCSS with custom component library
- Vite with SSL support for local development
- TipTap editor for rich text content

## Key Directories

- `/src/` - Core package source code
  - `SocialProviders/` - Provider implementations (Facebook, Twitter, Mastodon)
  - `Services/` - External service integrations
  - `Http/Controllers/` - API and web controllers
  - `Jobs/` - Background job processors
  - `Models/` - Eloquent models with UUID traits

- `/resources/js/` - Vue.js frontend
  - `Components/` - Reusable Vue components
  - `Pages/` - Inertia page components
  - `Composables/` - Vue composition API utilities

- `/docker/everyday-os/` - Docker deployment configuration for Everyday OS platform

## Database & Models

Key models use UUID primary keys and include:
- `Post` - Social media posts with versions
- `Account` - Connected social accounts
- `Media` - Uploaded media with conversions
- `Service` - External service configurations

## Testing Strategy

- Uses Pest PHP testing framework
- Test files mirror source structure in `/tests/`
- Factories provided for all models
- Focus on controller and action class testing

## Deployment

- Supports Docker deployment via Everyday OS configuration
- Requires Redis for queues and caching
- Laravel Horizon for queue management
- Media storage on local filesystem or cloud (S3 compatible)