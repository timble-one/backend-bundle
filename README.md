Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
composer require timble-one/backend-bundle
```

If you want Symfony Flex to apply the contrib recipe (bundle registration, Dockerfile
snippet, post-install output), make sure contrib recipes are enabled:

```console
composer config extra.symfony.allow-contrib true
composer require timble-one/backend-bundle
```

The bundle requires the PHP extensions `gd` and `exif` (`ext-gd`, `ext-exif`).
When using a compatible Symfony Docker setup, the Flex recipe can add
`RUN install-php-extensions gd exif` to the `Dockerfile`.

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require timble-one/backend-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    TimbleOne\BackendBundle\BackendBundle::class => ['all' => true],
];
```

Publishing the Symfony Flex Recipe (maintainers)
------------------------------------------------

This repository contains a draft contrib recipe under:

- `recipes-contrib/timble-one/backend-bundle/0.0.7/manifest.json`
- `recipes-contrib/timble-one/backend-bundle/0.0.7/post-install.txt`

Symfony Flex does not read recipes from this bundle repository directly. To make the
recipe available to users, submit these files to
[`symfony/recipes-contrib`](https://github.com/symfony/recipes-contrib) at:

- `timble-one/backend-bundle/0.0.7/manifest.json`
- `timble-one/backend-bundle/0.0.7/post-install.txt`

Notes:

- Contrib recipes are opt-in. Users must set `extra.symfony.allow-contrib=true`.
- The Dockerfile snippet only works in Docker images that provide the
  `install-php-extensions` helper.
