# Alunta Filament

Filament admin components for Alunta billing: a read-only billing section,
Alunta-owned fields and a manual subscription sync action.
Requires PHP ^8.4, Filament ^5.10.3 and Livewire ^4.4.8.

## Installation

Follow the [Alunta Laravel installation guide](https://github.com/KasperStuck/alunta-laravel#installation)
and add all three public VCS repositories to the host application's root
`composer.json`: `alunta-php-sdk`, `alunta-laravel` and `alunta-filament`.
Composer reads repository declarations only from the root project, including
transitive dependencies. Then install the integrations:

```bash
composer require 'kasperstuck/alunta-laravel:^0.1' 'kasperstuck/alunta-filament:^0.1'
```

No GitHub authentication is required once the public repositories and release
tags are available. GitHub limits unauthenticated API requests. An optional
GitHub token supplied through the `COMPOSER_AUTH` environment variable avoids
that limit. Keep tokens out of Git. Packagist may be supported later; this
installation uses VCS repositories.

The application owns migrations, routes, scheduling, configuration and contract
implementations. Installing either package does not enable Alunta.
Add the components to the application's Filament resources explicitly.

## Development

```bash
composer install --no-interaction
composer test
composer analyse
```

PHPStan uses Larastan at level 10. `tests/phpstan-bootstrap.php` supplies a minimal
Laravel container for analysis and is excluded from release archives with the
rest of `tests/`. The dependency direction Filament → Laravel → SDK comes from
the Composer requirements and PHPStan level 10: analysis fails on a class from a
package that is not installed. The Pest architecture rule only checks namespaces
that are installed; it excludes application code. Runtime integration tests stay
in the host application.

## Versioning

Releases use annotated SemVer tags. During 0.x, minor versions may break
compatibility; patch versions preserve it. This library commits no lockfile.

License: [MIT](LICENSE).
