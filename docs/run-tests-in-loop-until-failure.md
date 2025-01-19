# Running Tests in Loop Until Failure

When dealing with flaky tests that fail intermittently, it can be helpful to run them repeatedly until a failure occurs. This helps identify inconsistent behavior and reproduce issues for debugging.

```bash
while php vendor/bin/phpunit --filter FleetDispatchEspionageTest --stop-on-failure; do echo; done
```

### Command Breakdown

- `while` - Starts a loop that continues as long as the following command succeeds (returns exit code 0)
- `php vendor/bin/phpunit` - Runs PHPUnit tests
- `--filter FleetDispatchEspionageTest` - Only runs tests matching this filter pattern
- `--stop-on-failure` - Stops execution upon the first test failure
- `do echo; done` - Adds a blank line between test runs for better readability

## Usage

This command will:
1. Run the specified test(s)
2. If the test passes, it will run again
3. If the test fails, the loop will stop
4. Each test run is separated by a blank line for clarity

This approach is particularly useful when:
- Debugging intermittent test failures
- Verifying test stability
- Identifying race conditions
- Testing timing-sensitive code