load('ext://dotenv', 'dotenv')

# Load .env file
dotenv()

# Parse tilt cli production flag
config.define_bool("prod", args=False)
cfg = config.parse()
use_production = cfg.get('prod', False)
docker_compose_file = "docker-compose.prod.yml" if use_production else "docker-compose.yml"

# Load docker compose files with overrides
docker_compose([docker_compose_file,'docker-compose.overrides.yml'])

# Main OgameX Laravel app
docker_build(
  'ogamex/app',
  '.',
  target="app_dev",
  live_update = [
    sync('./app', '/var/www/app'),
    sync('./config', '/var/www/config'),
    sync('./public', '/var/www/public'),
    sync('./resources', '/var/www/resources'),
    # Add directories here to live-sync them without container restart
])

# Queue worker
docker_build(
  'ogamex/queue-worker',
  '.',
  target="app_dev",
  live_update = [
    sync('./app', '/var/www/app'),
    sync('./config', '/var/www/config'),
    sync('./public', '/var/www/public'),
    sync('./resources', '/var/www/resources'),
    # Have to restart the queue:work process when changes are made
    restart_container()
])

# Scheduler
docker_build(
  'ogamex/scheduler',
  '.',
  target="app_dev",
  live_update = [
    sync('./app', '/var/www/app'),
    sync('./config', '/var/www/config'),
    sync('./public', '/var/www/public'),
    sync('./resources', '/var/www/resources'),
])


# Categorize all docker services defined in docker-compose yml files
dc_resource('ogamex-db', labels=["OgameX.Database"])
dc_resource('ogamex-phpmyadmin', labels=["OgameX.Services"])
dc_resource('ogamex-webserver', labels=["OgameX.Services"])
dc_resource('ogamex-queue-worker', labels=["OgameX.App"], resource_deps=["ogamex-app"])
dc_resource('ogamex-scheduler', labels=["OgameX.App"], resource_deps=["ogamex-app"])
dc_resource('ogamex-app', labels=["OgameX.App"])