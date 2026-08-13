# HelloWorld reference module

This module is a small reference module for contributors. It demonstrates the supported OGameX module boundary:

- a `ModuleServiceProvider` that calls `parent::boot()`;
- module configuration read through the `helloworld` config namespace;
- an additive `admin.nav` view slot;
- an authenticated, in-game admin route and module view.

## Try it locally

```bash
composer dump-autoload
php artisan module:enable HelloWorld
php artisan module:list
```

Sign in as an administrator and open `/admin/hello-world`. The module also adds a link to the existing admin navigation bar.

Disable it when finished:

```bash
php artisan module:disable HelloWorld
```

## Tests

This module keeps its example tests with the module, not in the OGameX core
suite. Run only its tests with:

```bash
php artisan test --testsuite=Modules --filter=HelloWorld
```

## Start a real module

Run `php artisan module:make MyFeature`, then compare its provider with `app/Providers/HelloWorldServiceProvider.php`. Keep the generated module's alias consistent across `module.json`, its provider's `$nameLower`, view namespace, and route names.

Use ordinary Laravel routes, controllers, models, migrations, policies, queues, and tests inside your module. See [`docs/modules.md`](../../docs/modules.md) for how modules are structured and loaded.
