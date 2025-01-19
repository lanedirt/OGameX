# Debug GitHub Actions Remotely

This guide explains how to debug GitHub Actions workflows by connecting directly to the runner via SSH while the workflow is running.

## Setup Instructions

### 1. Add tmate Session to Your Workflow

Add the following step to your workflow file (`.github/workflows/*.yml`) where you want to enable debugging:

```yaml
      - name: Setup tmate session
        uses: mxschmitt/action-tmate@v3
        with:
          limit-access-to-actor: true # Restricts SSH access to the workflow triggerer
```

### 2. Trigger the Workflow

Commit and push your changes or create a Pull Request to trigger the GitHub Actions workflow. The workflow will pause at the tmate step.

### 3. Connect to the Runner

1. Navigate to the running workflow in the GitHub Actions interface
2. In the workflow logs, look for the SSH connection details that look like:
   ```
   SSH: ssh nsTgW8TY3aLYS6Z5bNSDQMxK7@nyc1.tmate.io
   ```
   or
   ```
   SSH: ssh -i <path-to-private-SSH-key> nsTgW8TY3aLYS6Z5bNSDQMxK7@nyc1.tmate.io
   ```

3. Copy the SSH command and execute it in your local terminal to connect to the runner

### 4. Debug and Continue

- Once connected, you can investigate the runner environment and execute any commands
- The workflow is paused during the debugging session
- To resume the workflow execution, run:
  ```bash
  sudo touch /continue
  ```

## Security Note

The `limit-access-to-actor: true` option ensures that only the user who triggered the workflow can connect to the debugging session. This is recommended for security purposes. In order for this to work you need to have a valid SSH key in your GitHub
account settings.

## Tips

- The SSH session provides full access to the runner environment
- You can inspect files, run commands, and test different approaches
- Remember to remove the tmate step from your workflow after debugging is complete
- The session will automatically timeout after a period of inactivity

For more information, see the [action-tmate documentation](https://github.com/mxschmitt/action-tmate).