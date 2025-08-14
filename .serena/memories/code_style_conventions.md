# Code Style and Conventions

## General Conventions
- **Indentation**: 4 spaces (no tabs)
- **Line endings**: LF (Unix-style)
- **File encoding**: UTF-8
- **Insert final newline**: Yes
- **Trim trailing whitespace**: Yes (except in Markdown files)

## PHP Conventions

### Naming
- **Classes**: PascalCase (e.g., `PostController`, `MediaService`)
- **Methods**: camelCase (e.g., `getAccountData()`, `processMedia()`)
- **Properties**: camelCase for public, snake_case for protected/private
- **Constants**: UPPER_SNAKE_CASE
- **Database**: snake_case for tables and columns
- **Routes**: kebab-case URLs

### Laravel Specific
- Models use UUID primary keys via `UseUuid` trait
- Controllers follow RESTful conventions
- Service classes for external integrations
- Action classes for business logic (single responsibility)
- Form requests for validation
- Resources for API responses
- Jobs for background processing

### Code Organization
- Keep controllers thin, move logic to actions/services
- Use dependency injection over facades where possible
- Prefer explicit over magic methods
- Type hints for parameters and return types
- Use enums for constants

## Frontend Conventions

### Vue.js
- **Components**: PascalCase filenames and component names
- **Props**: camelCase
- **Events**: kebab-case
- **Composition API** preferred over Options API
- Use `<script setup>` syntax
- DefineOptions for component metadata

### JavaScript/TypeScript
- **Variables**: camelCase
- **Constants**: UPPER_SNAKE_CASE or camelCase
- **Functions**: camelCase
- Prefer const over let, avoid var
- Use async/await over promises
- Destructuring for imports

### CSS/TailwindCSS
- Component classes in Vue files
- Utility-first approach with Tailwind
- Custom components in separate CSS when needed
- Responsive design with mobile-first approach

## File Organization
- One class per file
- File name matches class name
- Group related functionality in directories
- Keep imports organized (framework, package, local)

## Documentation
- PHPDoc blocks for classes and complex methods
- Inline comments for complex logic only
- No unnecessary comments
- Keep README and CHANGELOG updated