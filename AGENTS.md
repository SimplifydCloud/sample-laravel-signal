# Signal conventions

Signal is a Laravel 13 / PHP 8.4+ sample with Blade, plain CSS, and SQLite locally or managed PostgreSQL when deployed. There is no frontend build. Use `composer run setup` then `composer run dev`. The production image listens on port 8080; `/ready` checks the database schema.

Use Form Requests for validation, named routes, escaped Blade output, and Eloquent conventions. Preserve atomic voting and the unique idea/voter receipt. Keep anonymous demo limitations clear. Run `php artisan test` and `vendor/bin/pint` after PHP changes. Never commit `.env`, databases, dependencies, generated caches, or secrets.
