# Design Patterns and Guidelines

## Architecture Principles

### Laravel Package Architecture
- This is a **package**, not a standalone app
- All code lives in `/src/` following PSR-4 autoloading
- Service provider (`MixpostServiceProvider`) is the main entry point
- Package discovery via `composer.json` extra.laravel configuration

### Domain-Driven Design Elements
- **Models**: Core domain entities with business rules
- **Actions**: Single-purpose classes for business operations
- **Services**: External integrations and complex operations
- **Jobs**: Async processing for time-consuming tasks
- **Events/Listeners**: Decoupled event-driven communication

## Key Design Patterns

### 1. Abstract Factory Pattern
```php
// Social providers created via factory
SocialProviderManager::forAccount($account)
```
- Each provider extends `SocialProvider` abstract class
- Standardized interface across all platforms
- Provider-specific implementations hidden

### 2. Strategy Pattern
```php
// Different posting strategies per platform
$provider->publishPost($postData)
```
- Each platform has unique posting requirements
- Strategy selected based on provider type
- Extensible for new platforms

### 3. Repository Pattern
- Models handle data persistence
- Query scopes for common queries
- Relationships defined in models

### 4. Service Layer Pattern
```php
// External services abstracted
MediaService::upload($file)
TenorService::search($query)
```
- Isolates external dependencies
- Easier testing with mocks
- Consistent error handling

### 5. Job Queue Pattern
```php
// Background processing
PublishPost::dispatch($post)->onQueue('publish')
ImportAccountData::dispatch($account)->delay(60)
```
- Async processing for heavy operations
- Retry logic for failures
- Queue priorities for different tasks

### 6. Observer Pattern
- Model events (creating, created, updating, etc.)
- Event listeners for cross-cutting concerns
- Webhooks for external notifications

## Code Organization Guidelines

### Controller Guidelines
- Keep controllers thin
- Single responsibility per method
- Use form requests for validation
- Return Inertia responses for web
- Use API resources for JSON

### Action Class Guidelines
- Single public method: `handle()` or `execute()`
- Clear, descriptive class names
- Dependency injection in constructor
- Return explicit types
- Throw specific exceptions

### Service Class Guidelines
- Group related functionality
- Static methods for stateless operations
- Instance methods for stateful operations
- Clear method names indicating action
- Handle external API errors gracefully

### Job Guidelines
- Implement ShouldQueue interface
- Set appropriate queue and delays
- Use middleware for rate limiting
- Implement retry logic
- Log failures appropriately

### Model Guidelines
- Use UUID trait for primary keys
- Define all relationships
- Use query scopes for complex queries
- Add casts for data types
- Implement accessors/mutators sparingly

## Frontend Architecture

### Vue Component Structure
```vue
<script setup>
// 1. Imports
// 2. Props/Emits definitions
// 3. Reactive state
// 4. Computed properties
// 5. Lifecycle hooks
// 6. Methods
// 7. Watchers
</script>

<template>
  <!-- Template -->
</template>
```

### Composables Pattern
```javascript
// usePostEditor.js
export function usePostEditor() {
  // Shared logic for post editing
  return { /* ... */ }
}
```
- Extract reusable logic
- Share state between components
- Keep components focused

### State Management
- Component state for local data
- Provide/inject for component trees
- Props/events for parent-child
- Global stores sparingly

## Database Design

### Schema Conventions
- Tables: plural, snake_case (posts, media_files)
- Columns: snake_case (created_at, user_id)
- Foreign keys: {table}_id (account_id)
- Indexes on foreign keys and query fields
- Soft deletes where appropriate

### Migration Guidelines
- One change per migration
- Reversible migrations when possible
- Use schema builder methods
- Add indexes for performance
- Document complex changes

## Security Guidelines

### Input Validation
- Always validate user input
- Use form requests for validation
- Sanitize data before storage
- Escape output in views

### Authentication & Authorization
- Use Laravel's built-in auth
- Policy classes for authorization
- Middleware for route protection
- Rate limiting for APIs

### Sensitive Data
- Never commit secrets
- Use environment variables
- Encrypt sensitive database fields
- Audit log for critical actions

## Performance Guidelines

### Database Optimization
- Eager load relationships
- Use query scopes
- Chunk large datasets
- Cache expensive queries
- Add appropriate indexes

### Frontend Optimization
- Lazy load components
- Code splitting with Vite
- Optimize images before upload
- Use CDN for static assets
- Minimize API calls

### Queue Optimization
- Appropriate queue for job type
- Batch similar operations
- Use queue priorities
- Monitor queue health
- Clean up completed jobs

## Testing Guidelines

### Test Structure
- Mirror source structure
- One test class per source class
- Descriptive test names
- Arrange-Act-Assert pattern

### Test Coverage
- Controllers: Happy path + edge cases
- Actions: All execution paths
- Services: Mock external calls
- Models: Relationships and scopes
- Jobs: Success and failure scenarios

### Test Data
- Use factories for models
- Minimal data for tests
- Clean up after tests
- Avoid testing framework code

## Error Handling

### Exception Strategy
- Specific exception classes
- Catch at appropriate level
- Log with context
- User-friendly messages
- Don't expose internals

### Validation Errors
- Clear error messages
- Field-specific errors
- Translate error messages
- Handle gracefully in UI

### External Service Errors
- Retry with backoff
- Fallback strategies
- Circuit breaker pattern
- Log for monitoring
- Notify admins if critical