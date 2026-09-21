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

### Modules

New modules should follow the [module tutorial](docs/modules.md). Use
`Modules/HelloWorld` as the reference implementation. Keep module routes, views,
data, and tests inside the module, and verify that disabling it removes its
application behavior.

### Before you open a PR

- **One issue, one PR.** Each pull request must address a single issue or concern. Only include changes directly related to the issue being fixed or the feature being added. Do not bundle unrelated changes, including small cleanups or fixes to other areas. Submit those separately. If you notice something unrelated that needs fixing while working on your PR, open a separate issue or PR for it. This keeps reviews focused and the git history clean.
- **Branch from `main`.** Always create your feature branch from the latest `main`. Do not base a branch on another feature branch or an outdated commit.
- **Address open change requests first.** If you have an existing PR with a change request from a reviewer, resolve that feedback before opening a new PR. Reviewers have limited time, and stacking new PRs on top of unresolved ones makes it harder to keep quality high. PRs opened while another of your PRs has an unresolved change request may be closed until the existing one is resolved.

> ⚠️ Important: Pull requests that do not follow the above guidelines will not be merged and may be closed without review.

When submitting a pull request, please make sure to also follow these technical guidelines:

### 1. General code style guidelines
In general: when making changes to an existing class, method, or file, **use the same code and naming conventions that are already established in that scope**.

1. **Adhere to existing styles**
   - Match the formatting, naming, and structural conventions already used in the project or the file you are modifying. Consistency takes priority over personal preference. For example, if a certain function uses `$snake_case`, do not add variables with `$camelCase`.

2. **Prioritize clarity over compactness**
   - Avoid code golfing (trying to minimize amount of lines of code) or overly clever constructs that are hard to read. Code should be easy to read and understand at a glance for all levels of programmers.

3. **Avoid duplication**
   - Reuse existing functions or extract common logic into helper methods or utilities instead of repeating code.

4. **Use helper methods instead of inline anonymous functions**
   - Encapsulate meaningful or reusable logic in named methods to improve clarity, maintainability, and discoverability.

> ⚠️ Important: Pull requests that do not follow the established conventions and practices outlined above will not be merged until they are updated to comply.

### 2. PSR-12 Coding Standard
The easiest way to check if your contributed code adheres to the PSR-12 conventions is to run the Laravel Pint script which is auto installed via Composer:

```
$ composer run cs -- --test
```

Tip: it's possible to let Laravel Pint attempt to fix the code for you by running the following composer script:

```
$ composer run cs
```

### 3. Automated Refactoring
Make sure that your code has been refactored to match our set Rector standards. You can run Rector locally using the following composer script:

```
$ composer run rector
```

### 4. PHPStan static code analysis
Make sure that your code passes the PHPStan static code analysis. You can run PHPStan locally using the following composer script:

```
$ composer run stan
```

### 5. Laravel unit and feature tests
Your PR should include feature or unit tests where possible to cover the changes you made. OGameX uses the default Laravel testing framework which covers feature and unit tests by default.
To run the tests locally, you can use the following command:

```
$ composer run tests
```

You are also able to apply the `--filter` parameter to run a specific class or method such as :

```
$ composer run tests -- --filter PlanetServiceTest
```

### 6. Custom race condition tests
If you are working on a feature that might introduce race conditions, please include tests that cover these scenarios. OGameX already contains some custom tests that can be run via php artisan commands. These tests support running multiple requests in parallel and in multiple iterations in order to simulate conditions that could cause race conditions.

These tests are located in the `app/Console/Commands/Test/` directory and can be run using the following command:

```bash
$ php artisan ogamex:test:race-condition-unitqueue
$ php artisan ogamex:test:race-condition-game-mission
```

### 7. Run CSS and JS build
OGameX uses Vite to compile the CSS and JS assets. Before submitting a PR, make sure to run the following command to compile the assets.

```
$ npm run build
```

During development, you can run the Vite dev server to automatically recompile assets and enable hot-reload:

```
$ npm run dev
```

## AI-Assisted Contributions

AI-assisted contributions are welcome at OGameX, and many of us use AI tooling in our own workflow. What matters is the quality of what you submit, not how you wrote it. That said, **AI-assisted contributions are held to the same bar as any other contribution.** In the past we have received PRs that were clearly unreviewed AI output: code that didn't fit the project, broke existing behavior, or showed no understanding of the game mechanics it was trying to implement. These create a heavy burden on reviewers and will be closed without detailed review.

If you use a coding agent, point it at [AGENTS.md](AGENTS.md), which contains the project's build commands, repository layout, and conventions.

### The ground rules

1. **Understand what you are submitting.** You don't need to have written every line by hand, but you must be able to explain what your code does and why. During review, you are the one answering questions, not your AI agent. If you cannot explain a change, it is not ready.

2. **Understand the game.** OGameX is a faithful recreation of OGame. Contributions that get game mechanics wrong (fleet timing, combat formulas, resource calculations, building requirements, research dependencies) will not be merged regardless of code quality. If you are not familiar with how a feature works in OGame, research it first or ask in Discussions before writing code.

3. **Test for real.** Run the application and verify your change works in the browser. "It compiles" or "the tests pass" is not sufficient. Confirm the feature actually behaves correctly in the running game, and include screenshots or a brief description of your manual testing in the PR.

4. **Review your own code first.** Read through your entire diff line by line before opening a PR. Run the project's code quality tools (Rector, Pint, PHPStan) and make sure your tests actually test the behavior you changed, not just that "something runs without errors." This is where you catch the mistakes AI makes.

5. **Sound like a human.** Issues, PR descriptions, and review replies should read like you wrote them. Generic AI-generated walls of text make it harder for reviewers to understand your intent. Write concisely in your own words.

6. **Be transparent.** If AI tools were involved in generating your code, mention it in the PR description.

If you are new to programming or to this codebase, AI can be an excellent learning tool: ask it to explain the code it generates, work in small steps (understand, plan, implement, review), and feed it context about how OGame works, such as the relevant mission classes, existing tests, and the specific mechanics involved.
