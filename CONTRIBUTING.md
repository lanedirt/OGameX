# Contributing
Contributions are **welcome** and will be fully **credited**. There are various ways to contribute:

- Report bugs or request new features/improvements via the [Issues](https://github.com/lanedirt/ogamex/issues) page.
- Start a discussion around new ideas or suggestions on the [Discussions](https://github.com/lanedirt/ogamex/discussions) page.
- Contribute code via Pull Requests on [Github](https://github.com/lanedirt/ogamex).

## Issues
Before submitting a new issue, please check the issues and pull requests to avoid duplicates.

## Discussions
If you have an idea or suggestion, feel free to start a discussion on the [Discussions](https://github.com/lanedirt/ogamex/discussions) page. This is a great way to get feedback and discuss new ideas before submitting a pull request.

## Pull Requests
If you would like to contribute via pull requests, a good way to get started is to filter the issues list by the [good first issues](https://github.com/lanedirt/OGameX/labels/good%20first%20issue) label. This label is used for issues that are easy to fix and a good starting point for new contributors.

[![good first issues open](https://img.shields.io/github/issues/lanedirt/OGameX/good%20first%20issue.svg?logo=github)](https://github.com/lanedirt/OGameX/issues?q=is%3Aopen+is%3Aissue+label%3A"good+first+issue")

Refer to the [Installation section](https://github.com/lanedirt/OGameX#installation) in the main README.md for how to get your local development environment setup.

When submitting a pull request, please make sure to follow these guidelines:

### 1. Follow existing conventions in the code you're working with
When making changes to an existing class, method, or file, **use the same code and naming conventions that are already established in that scope**.

The goal is consistency and readability. Even if the overall codebase isn’t fully standardized yet, new code should blend in with the style of the surrounding code.

Example for naming convention:

```php
// Existing code in the method uses snake_case
$unit_queue = new UnitQueue();
$unit_amount = $unit_queue->amount();

// ❌ Wrong (introduces camelCase into snake_case method)
$unitAttackPower = $unit_queue->attackPower();

// ✅ Correct (matches existing snake_case convention)
$unit_attack_power = $unit_queue->attackPower();
```

### 2. PSR-12 Coding Standard
The easiest way to check if your contributed code adheres to the PSR-12 conventions is to run the Laravel Pint script which is auto installed via Composer:

```
$ composer run cs -- --test
```

Tip: it's possible to let Laravel Pint attempt to fix the code for you by running the following composer script:

```
$ composer run cs
```

### 3. PHPStan static code analysis
Make sure that your code passes the PHPStan static code analysis. You can run PHPStan locally using the following composer script:

```
$ composer run stan
```

### 4. Laravel unit and feature tests
Your PR should include feature or unit tests where possible to cover the changes you made. OGameX uses the default Laravel testing framework which covers feature and unit tests by default.
To run the tests locally, you can use the following command:

```
$ php artisan test
```

You are also able to apply the `--filter` parameter to run a specific class or method such as :

```
$ php artisan test --filter PlanetServiceTest
```

### 5. Custom race condition tests
If you are working on a feature that might introduce race conditions, please include tests that cover these scenarios. OGameX already contains some custom tests that can be run via php artisan commands. These tests support running multiple requests in parallel and in multiple iterations in order to simulate conditions that could cause race conditions.

These tests are located in the `console/Commands/Tests` directory and can be run using the following command:

```bash
$ php artisan test:race-condition-unitqueue
$ php artisan test:race-condition-game-mission
```

### 6. Run CSS and JS build
OGameX uses Laravel Mix to compile the CSS and JS assets. Before submitting a PR, make sure to run the following command to compile the assets.
This command can be run in watch mode to automatically recompile the assets when changes are made which is useful during development.

```
$ npm run dev watch
```
