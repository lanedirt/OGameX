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

When submitting a pull request, please make sure to follow these guidelines:

### 1. PSR-12 Coding Standard
The easiest way to check if your contributed code adheres to the conventions is to run the Laravel Pint script which is auto installed via Composer:

`$ ./vendor/bin/pint --test --preset psr12`

Tip: it's possible to let Laravel Pint attempt to fix the code for you by running it without the --test flag:

`$ ./vendor/bin/pint --preset psr12`

### 2. PHPStan static code analysis
Make sure that your code passes the PHPStan static code analysis. You can run PHPStan locally using the following command:

`$ ./vendor/bin/phpstan analyse --memory-limit=256M`

### 3. Include feature or unittests
Your PR should include feature or unittests where possible to cover the changes you made. OGameX uses the default Laravel testing framework which covers feature and unittests by default. If you're not sure how to write tests, please ask.
To run the tests locally, you can use the following command:
`$ ./vendor/bin/phpunit`

### 4. Keep it simple
If your PR is too big, it will be hard to review. Try to keep it simple and small. If you want to do more than one thing, send multiple pull requests.

### 5. Keep it clean
Please remove any unrelated changes from your PR. If you have changes in your branch that are not related to the PR, please create a new branch for them.

If you have any questions, feel free to reach out to the maintainer(s). Thank you in advance for your contributions! 🎉