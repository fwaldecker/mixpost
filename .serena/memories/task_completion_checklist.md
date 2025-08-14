# Task Completion Checklist

When completing any coding task in the Mixpost project, ensure you follow these steps:

## 1. Code Quality Checks

### PHP Code
- [ ] Run Laravel Pint formatter: `composer format`
- [ ] Run PHPStan static analysis: `composer analyse`
- [ ] Ensure no PHP syntax errors: `php -l src/**/*.php`

### JavaScript/Vue Code
- [ ] Ensure no ESLint errors (if configured)
- [ ] Check for console.log statements to remove
- [ ] Verify no TypeScript errors (if using TypeScript)

## 2. Testing

### Run Tests
- [ ] Run full test suite: `composer test`
- [ ] If tests fail, fix issues before proceeding
- [ ] Add new tests for new functionality
- [ ] Update existing tests if behavior changed

### Manual Testing
- [ ] Test the feature/fix in browser if frontend change
- [ ] Verify queue jobs work if background processing involved
- [ ] Check different screen sizes for responsive design

## 3. Build Assets

### Frontend Build
- [ ] Build production assets: `npm run build`
- [ ] Ensure build completes without errors
- [ ] Check that built assets work correctly

## 4. Database & Migrations

### If Database Changes Made
- [ ] Run migrations: `php artisan migrate`
- [ ] Test rollback: `php artisan migrate:rollback`
- [ ] Re-run migrations: `php artisan migrate`

## 5. Cache Management

### Clear Caches After Changes
- [ ] Clear application cache: `php artisan cache:clear`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Clear route cache: `php artisan route:clear`
- [ ] Clear view cache: `php artisan view:clear`
- [ ] Clear Mixpost caches:
  - `php artisan mixpost:clear-services-cache`
  - `php artisan mixpost:clear-settings-cache`

## 6. Background Jobs

### If Jobs/Queues Modified
- [ ] Restart Horizon: `php artisan horizon:terminate` then `php artisan horizon`
- [ ] Check failed jobs: `php artisan queue:failed`
- [ ] Process pending jobs if needed

## 7. Documentation

### Update Documentation
- [ ] Update code comments if logic changed
- [ ] Update README if setup/usage changed
- [ ] Update CHANGELOG if significant change
- [ ] Document any new environment variables

## 8. Version Control

### Before Committing
- [ ] Review all changes: `git diff`
- [ ] Ensure no debug code left (dd(), dump(), console.log)
- [ ] Check no sensitive data in commits
- [ ] Verify .gitignore is respected

### Commit Message Format
Use conventional commits:
- `feat:` for new features
- `fix:` for bug fixes
- `docs:` for documentation
- `style:` for formatting
- `refactor:` for code restructuring
- `test:` for test changes
- `chore:` for maintenance

## 9. Final Verification

### System Check
- [ ] Application loads without errors
- [ ] No 500 errors in browser
- [ ] Check Laravel logs: `tail -f storage/logs/laravel.log`
- [ ] Verify Horizon is running if needed
- [ ] Test main user flows still work

## 10. Performance Check

### If Performance Critical
- [ ] Check query performance (N+1 queries)
- [ ] Verify caching is used appropriately
- [ ] Check memory usage for large operations
- [ ] Ensure assets are optimized

## Priority Order

When running completion commands, use this order:
1. `composer format` - Format PHP code
2. `composer analyse` - Static analysis
3. `composer test` - Run tests
4. `npm run build` - Build assets
5. Clear caches if needed
6. Manual verification

## Quick Command Chain

For a complete check, run:
```bash
composer format && composer analyse && composer test && npm run build
```

If all pass, the task is ready for review/merge.