# Codebase Structure

## Directory Layout

### Core Package (`/src/`)
- `SocialProviders/` - Social media provider implementations
  - Each provider extends `SocialProvider` abstract class
  - Standardized interfaces for auth, posting, metrics
- `Services/` - External service integrations (Tenor, Unsplash, etc.)
- `Http/Controllers/` - Web and API controllers
- `Models/` - Eloquent models with UUID primary keys
- `Jobs/` - Background job processors for async tasks
- `Actions/` - Business logic action classes
- `Commands/` - Artisan console commands
- `Events/` & `Listeners/` - Event-driven architecture
- `Rules/` - Custom validation rules
- `Enums/` - Enum definitions for constants
- `Concerns/` - Traits and concerns for code reuse
- `AIProviders/` - AI service integrations

### Frontend (`/resources/`)
- `js/Components/` - Reusable Vue components
- `js/Pages/` - Inertia page components
- `js/Composables/` - Vue composition utilities
- `js/Stores/` - State management
- `css/` - Stylesheets and Tailwind config
- `views/` - Blade templates

### Database (`/database/`)
- `migrations/` - Database schema migrations
- `migrations-upgrade/` - Upgrade migrations
- `factories/` - Model factories for testing

### Configuration
- `/config/` - Package configuration files
- `/routes/` - Route definitions (web, api, broadcast)
- `/stubs/` - File stubs for generators

### Testing (`/tests/`)
- Test structure mirrors source code
- `Http/` - Controller tests
- `Actions/` - Action class tests
- `Commands/` - Command tests

## Key Design Patterns

1. **Service Provider Pattern** - Main entry point via `MixpostServiceProvider`
2. **Repository Pattern** - Data access abstraction
3. **Action Classes** - Single responsibility business logic
4. **Job Queue Pattern** - Async processing with Laravel Horizon
5. **Factory Pattern** - Social provider instantiation
6. **Observer Pattern** - Model events and listeners
7. **Strategy Pattern** - Different posting strategies per platform