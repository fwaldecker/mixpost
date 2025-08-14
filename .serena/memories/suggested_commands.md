# Suggested Commands for Development

## Development Workflow

### Frontend Development
```bash
# Start Vite dev server with hot reload
npm run dev

# Build production assets
npm run build
```

### Backend Queue Processing
```bash
# Start Laravel Horizon (required for scheduled posts)
php artisan horizon

# Check Horizon status
php artisan horizon:status
```

## Testing & Code Quality

### Run Tests
```bash
# Run all tests with Pest
composer test

# Run tests with code coverage
composer test-coverage

# Run specific test file
./vendor/bin/pest tests/Http/Controllers/PostController.php
```

### Code Quality Checks
```bash
# Static analysis with PHPStan
composer analyse

# Format code with Laravel Pint
composer format

# Check code without fixing
./vendor/bin/pint --test
```

## Package Management

### PHP Dependencies
```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Install with dev dependencies
composer install --dev
```

### JavaScript Dependencies
```bash
# Install dependencies
npm install

# Update dependencies
npm update

# Clean install
rm -rf node_modules package-lock.json && npm install
```

## Artisan Commands

### Mixpost Specific
```bash
# Run scheduled posts
php artisan mixpost:run-scheduled-posts

# Import account data
php artisan mixpost:import-account-audience
php artisan mixpost:import-account-data

# Process metrics
php artisan mixpost:process-metrics

# Clear caches
php artisan mixpost:clear-services-cache
php artisan mixpost:clear-settings-cache

# Clean old data
php artisan mixpost:delete-old-data
```

### Laravel Standard
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Generate IDE helper files
php artisan ide-helper:generate
```

## Git Commands (macOS)

### Basic Operations
```bash
# Check status
git status

# Stage changes
git add .
git add -p  # Interactive staging

# Commit
git commit -m "feat: add new feature"

# Push changes
git push origin branch-name

# Pull latest
git pull origin main
```

### Branching
```bash
# Create and switch branch
git checkout -b feature/new-feature

# Switch branch
git checkout main

# Delete local branch
git branch -d branch-name

# Delete remote branch
git push origin --delete branch-name
```

## System Commands (macOS)

### File Operations
```bash
# List files with details
ls -la

# Find files
find . -name "*.php" -type f

# Search in files (using ripgrep)
rg "pattern" --type php

# Watch file changes
fswatch -o . | xargs -n1 -I{} echo "File changed"
```

### Process Management
```bash
# Find process using port
lsof -i :8000

# Kill process
kill -9 PID

# Monitor system
top
htop  # If installed
```

### Docker (if using Everyday OS deployment)
```bash
# Build containers
docker-compose build

# Start services
docker-compose up -d

# View logs
docker-compose logs -f

# Enter container
docker exec -it container_name bash

# Stop services
docker-compose down
```

## Debugging

### PHP Debugging
```bash
# Dump and die in code
dd($variable);

# Log to Laravel log
\Log::info('Debug message', ['data' => $data]);

# Use Laradumps (if available)
ld($variable)->die();
```

### JavaScript Debugging
```javascript
// Console logging
console.log('Debug:', variable);
console.table(arrayData);
console.error('Error:', error);

// Vue DevTools available in browser
```

## Performance

### Monitoring
```bash
# Check Laravel Telescope (if installed)
php artisan telescope:prune

# Monitor Redis
redis-cli monitor

# Check queue jobs
php artisan queue:failed
php artisan queue:retry all
```

## Quick Fixes

```bash
# Fix permissions (macOS)
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs

# Clear everything and rebuild
composer dump-autoload
php artisan optimize:clear
npm run build

# Restart services
php artisan horizon:terminate
php artisan horizon
```