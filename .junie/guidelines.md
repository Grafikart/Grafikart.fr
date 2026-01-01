# Build/Configuration Instructions

The project is a Symfony application running in a Dockerized environment.

To run php use `docker compose exec php php` never the one on the host machine.

## Prerequisites

- Docker & Docker Compose
- Make (optional, but recommended)
- Bun (for frontend tasks)

## Testing Information

### Configuring and Running Tests

The project uses PHPUnit and Paratest for testing. A dedicated Docker Compose configuration is available for testing (`docker-compose.test.yml`).

- **Run tests using PHPUnit directly**:
  ```bash
  docker-compose exec php php vendor/bin/phpunit
  ```
- **Run a specific test file**:
  ```bash
  docker-compose exec php php vendor/bin/phpunit tests/Path/To/Test.php
  ```

### Adding New Tests

- **Unit/Integration Tests**: Place them in the `tests/` directory following the project structure (mirroring `src/`).
- **Web Tests**: Extend `App\Tests\WebTestCase` for functional tests. It provides helper methods for authentication (`login`), JSON requests (`jsonRequest`), and DOM assertions (`expectH1`, `expectAlert`, etc.).
- **Database in Tests**: Use the `dama/doctrine-test-bundle` which is already configured to wrap each test in a transaction for isolation.

## Additional Development Information

### Code Style

The project follows the Symfony code style with some specificities:
- **PHP**: Managed by `php-cs-fixer`. Configuration is in `.php-cs-fixer.dist.php`.
    - Short array syntax (`['key' => 'value']`).
    - **No Yoda style** (`$variable === true` instead of `true === $variable`).
- **Frontend**: Managed by `prettier`.

### Formatting and Linting

- **Format PHP code**:
  ```bash
  make format
  ```
- **Run Static Analysis (PHPStan)**:
  ```bash
  docker-compose exec php php vendor/bin/phpstan analyze
  ```
- **Rector**:
  ```bash
  docker-compose exec php php vendor/bin/rector process
  ```

### Domain-Driven Design (DDD)

The project is organized by domains within `src/Domain/`. Each domain should ideally contain its entities, services, and repositories. The `src/Http/` directory handles the web layer (Controllers, Forms, DTOs).

### Structure

The backend (Admin) use inertia with React. To create a new page for the backend you have to follow these steps :

- Create a simple DTO file (in src/Http/Admin/Data)
- Then create a new action in the controller if necessary
- Retrieve the data and fill the DTO using the required data
- Use `docker compose exec php php app:ts` to generate the corresponding type
- Create the inertia view in `assets/pages/*` and use `courses/index.tsx` or `courses/form.tsx` as an example
- For form, create a new DTO to reprensent data input
