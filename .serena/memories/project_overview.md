# Mixpost Pro Team - Project Overview

## Project Purpose
Mixpost Pro Team is a self-hosted social media management software built as a Laravel package. It provides comprehensive social media scheduling, publishing, and analytics capabilities for multiple platforms including Facebook, Twitter/X, Mastodon, LinkedIn, TikTok, YouTube, Pinterest, Reddit, Telegram, Google Business, and Bluesky.

## Tech Stack

### Backend
- **PHP 8.2+** with Laravel framework (10.x, 11.x, or 12.x)
- **Laravel Horizon** for queue management
- **Redis** for caching and queues
- **MySQL/PostgreSQL** for database
- **Composer** for PHP dependency management

### Frontend
- **Vue.js 3** with Composition API
- **Inertia.js** for SPA-like experience without API
- **TailwindCSS 4** for styling
- **Vite** as build tool
- **TipTap** editor for rich text content
- **Chart.js** for analytics visualization

### Testing & Quality
- **Pest PHP** testing framework
- **PHPStan** for static analysis
- **Laravel Pint** for code formatting

## Project Type
This is a **Laravel Package**, not a standalone application. It integrates into existing Laravel applications through the service provider pattern.

## Key Features
- Multi-platform social media posting
- Post scheduling with timezone support
- Media management with conversions
- Analytics and metrics tracking
- Team collaboration with workspaces
- AI content generation integration
- Webhook support
- Two-factor authentication
- Custom theming support