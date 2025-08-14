# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Context

This is the **Everyday OS App** (formerly Mixpost), a Laravel package for social media management currently being enhanced with a Netflix-style UI. The repository contains both the original Mixpost Pro Team codebase and planning documentation for the enhancement project.

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

# Run a single test file
vendor/bin/pest tests/Feature/ExampleTest.php

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
- **UUID Models**: All models use UUID primary keys via `UseUuid` trait for better distributed system compatibility
- **Webhook System**: Events trigger webhooks through `TriggerCommentWebhook` and `PostCommentCreated` for n8n integration

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

## Current Enhancement Project

The codebase is being enhanced with a Netflix-style UI following the plan in `/Claude/project/everyday-os-app/mixpost-enhancement-plan.md`. Key enhancements include:

### Phase 6A: Quick Wins (Current Focus)
- Netflix card grid layout for content browsing
- Simplified navigation (4 clicks → 1 click)
- Basic queue information display
- Dashboard quick actions

### Implementation Roadmap
- **Milestone 1**: Netflix Grid UI (`/Claude/project/everyday-os-app/06-milestone-1-netflix-grid.md`)
- **Milestone 2**: Content Viewer Modal (`/Claude/project/everyday-os-app/07-milestone-2-content-viewer.md`)
- **Milestone 3**: Custom Queue System (`/Claude/project/everyday-os-app/08-milestone-3-queue-system.md`)

Progress tracking: `/Claude/project/everyday-os-app/progress-tracker.md`

## Deployment

### Docker Configuration
- Docker Compose: `/docker/everyday-os/docker-compose.yml`
- Production URL: `https://social.everydaycreator.org`
- Container Port: 9000 → 80
- Services: MySQL 8.0, Redis, Laravel Horizon

### Requirements
- PHP 8.2+
- Laravel 10.x, 11.x, or 12.x
- Redis for queues and caching
- MySQL/PostgreSQL for database
- Media storage: local filesystem or S3-compatible cloud