# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build & Development Commands

All PHP commands must run inside Docker: `docker compose exec php php <command>`

```bash
make dev          # Start development server (http://grafikart.localhost:8000)
make seed         # Populate database with test data
make test         # Run all tests (PHPUnit + frontend)
make tt           # Run PHPUnit tests
make lint         # Run PHPStan static analysis
make format       # Format code (PHP-CS-Fixer + Prettier)
make migrate      # Run database migrations
make migration    # Generate a new migration
```

### Running Specific Tests
```bash
docker compose exec php php vendor/bin/phpunit tests/Path/To/Test.php
```

### Generate TypeScript Types
After modifying DTOs, regenerate TypeScript types:
```bash
docker compose exec php php bin/console app:ts
```

## Architecture

### Backend Structure (Symfony 7.4, PHP 8.4)

**Domain-Driven Design** - Business logic organized in `src/Domain/`:
- Each domain (Course, Auth, Blog, Forum, Premium...) contains its entities, repositories, and services
- Domain events for cross-domain communication

**HTTP Layer** (`src/Http/`):
- `Admin/` - Inertia-based admin panel controllers and DTOs
- `Controller/` - Public-facing controllers
- `Api/` - API endpoints

**Admin Panel Pattern** - Uses Inertia.js with React:
- Controllers extend `InertiaController` which provides CRUD helpers (`crudIndex`, `crudEdit`, `crudCreate`, `crudStore`, `crudUpdate`)
- Data flow uses separate DTOs:
  - `*ItemData` - List view data (for index pages)
  - `*FormData` - Form display data (what React receives)
  - `*FormInput` - Form submission data (what server validates)
- DTOs use `#[TypeScript]` attribute for automatic type generation

### Frontend Structure (React 19, Tailwind CSS 4)

Located in `assets/`:
- `pages/` - Inertia page components (courses/, plans/, users/)
- `components/ui/` - shadcn/ui components
- `components/` - Shared components (layout.tsx, form.tsx, form-field.tsx)
- `types/` - TypeScript types (auto-generated via `app:ts`)
- Two entry points: `app.ts` (public) and `admin.tsx` (admin panel)

### Object Mapping System

Custom `ObjectMapper` (`src/Component/ObjectMapper/`) handles entity-DTO conversion:
- `#[Map]` attribute on DTO properties for automatic mapping
- Transformers for complex conversions (collections, URLs, etc.)

## Code Style

- PHP: PHP-CS-Fixer with Symfony style, **no Yoda conditions** (`$var === true` not `true === $var`)
- Frontend: Prettier with Tailwind plugin
- PHPStan level 8

## Creating Admin CRUD

See `.junie/crud.md` for detailed instructions. Summary:

1. Create `*ItemData` DTO for list view
2. Create controller extending `InertiaController`
3. Run `docker compose exec php php bin/console app:ts`
4. Create React `index.tsx` component
5. For forms: add `*FormData` (display) and `*FormInput` (validation) DTOs
6. Add edit/update/create/store methods to controller
7. Create React `form.tsx` component
