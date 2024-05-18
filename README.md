<p align="center">
    <img src="https://github.com/lanedirt/OGameX/assets/6917405/c81061d5-0310-4574-a91d-1ea155b567c0" alt="OGameX logo" />
</p>

<div align="center">
    
[<img src="https://img.shields.io/github/v/release/lanedirt/OGameX?include_prereleases&logo=github">](https://github.com/lanedirt/OGameX/releases) 
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-docker-compose.yml?label=docker-compose%20build">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-docker-compose.yml) 
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-sqlite.yml?label=tests">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-sqlite.yml) 
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-phpstan-code-analysis.yml?label=static code analysis">](https://github.com/lanedirt/OGameX/actions/workflows/run-phpstan-code-analysis.yml)
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-laravel-pint-code-style-checker.yml?label=psr-12%20code%20style">](https://github.com/lanedirt/OGameX/actions/workflows/run-laravel-pint-code-style-checker.yml)

</div>

<div align="center">
    
[![good first issues open](https://img.shields.io/github/issues/lanedirt/OGameX/good%20first%20issue.svg?logo=github)](https://github.com/lanedirt/OGameX/issues?q=is%3Aopen+is%3Aissue+label%3A"good+first+issue")
    
</div>


OGameX is an open-source OGame clone aiming to recreate the official OGame experience, developed originally by GameForge GmbH. This clone is built from scratch using the Laravel 11.x framework and uses modern PHP practices.

## 🖥️ Live demo
- Main branch (nightly builds): [https://main.ogamex.dev](https://main.ogamex.dev)
- Latest stable release **(0.5.0)**: [https://release.ogamex.dev](https://release.ogamex.dev)

## 📝 Table of Contents
- [1. Example screenshots](#screenshots)
- [2. About the author](#author)
- [3. Goal](#goal)
- [4. Current State of the Project](#current-state)
  - [a) Upcoming Features](#upcoming-features)
- [5. Contributing](#contributing)
- [6. Disclaimer](#disclaimer)
- [7. Installation](#installation)
  - [a) Development: Install OGameX using Docker](#development)
  - [b) Production: Install OGameX using Docker](#production)
  - [c) Tips for initial setup](#tips-for-initial-setup)
- [8. Support](#support)
- [9. License](#license)

## <a name="screenshots"></a> 🖥️ Example screenshots
<img width="1130" alt="Screenshot 2024-04-28 at 16 57 38" src="https://github.com/lanedirt/OGameX/assets/6917405/ae1a4852-5c88-42ad-9242-27c8e088577c">
<img width="1174" alt="Screenshot 2024-04-28 at 16 57 09" src="https://github.com/lanedirt/OGameX/assets/6917405/f586c9f0-c335-45ae-bdb6-8793dda83103">
<img width="1142" alt="Screenshot 2024-04-28 at 16 58 08" src="https://github.com/lanedirt/OGameX/assets/6917405/37a419e0-5ace-457d-b1b7-382dc317782c">

## <a name="author"></a> ✨ About the author

My ([@lanedirt](https://github.com/lanedirt)) journey into software development began in 2007 at the age of 14 when I discovered the source code for Ugamela, an early open-source PHP clone of OGame. I really liked running my own browser game server and dedicated myself to modifying this version and translating it to Dutch, leading to the launch of OGameX.nl. This server, active from 2007 to 2009, nurtured a small yet engaged community. This experience not only sparked my passion for software development but also laid the groundwork for my professional career. OGame has always held a special place in my heart, which is why now, 15 years later, I've decided to return to it and create this open-source clone from the ground up.

## <a name="goal"></a> ✨ Goal

The primary goal of this fan-based project is to engineer a faithful rendition of OGame, specifically reflecting its state prior to the Lifeforms update introduced in 2020. This initiative, purely fan-based and non-commercial, is pursued strictly for educational purposes.

## <a name="current-state"></a> 🖥️ Current State of the Project

OGameX is under active development with several core features already implemented:

- Basic registration and login
- Planet creation and resource management (metal, crystal, deuterium, energy)
- Building and updating resources, facilities, shipyards, and defenses
- Basic galaxy overview
- Planet switching
- Highscore system
- Basic messages system
- Fleet dispatch missions
  - Transport
  - Deployment
  - Colonisation
- Basic admin panel

### <a name="upcoming-features"></a> a) Upcoming Features

New features are continuously being added. Upcoming features:
- Remaining fleet dispatch missions (espionage, recycling, attack, ACS)
- Battlesystem
- Support for Moons
    - Moon destruction fleet dispatch mission
- Alliance system
- Improve galaxy overview
- Improve message system

## <a name="contributing"></a> 🚀 Contributing

Contributions are warmly welcomed, whether in development, testing, or spreading the word. Feel free to submit pull requests or contact me for any other contributions.

A good starting point are issues labeled as "good first issue".

[![good first issues open](https://img.shields.io/github/issues/lanedirt/OGameX/good%20first%20issue.svg?logo=github)](https://github.com/lanedirt/OGameX/issues?q=is%3Aopen+is%3Aissue+label%3A"good+first+issue")

Read the [CONTRIBUTING.md](https://github.com/lanedirt/OGameX/blob/main/CONTRIBUTING.md) file for more information.

## <a name="disclaimer"></a> 📓 Disclaimer

This project is a non-commercial hobby project. All rights and concepts related to OGame are owned by GameForge GmbH. We encourage supporters to try the official OGame at https://ogame.org to support its creators.

## <a name="installation"></a> 🖥️ Installation
The easiest way to get OGameX up and running on your own machine is by running the Docker containers via the docker-compose.yml file that is supplied in this repository.

Alternatively, you can also deploy this project manually on any host that supports at least the following:
- PHP >= 8.2
- MySQL/MariaDB
- Ability to enable specific PHP extensions (Ctype, cURL, DOM, Fileinfo and more...)

See the full list of requirements for Laravel 11.x and how to deploy to a server here: https://laravel.com/docs/11.x/deployment.

### <a name="development"></a> a) Development: Install OGameX using Docker
For local development you can run the default docker-compose file that is included in this repository. This configuration is optimized for development and includes several tools that are useful for debugging and testing.

1. Clone the repository.
2. Copy `.env.example` to `.env`.
3. Launch the project using Docker Compose:
  ```
  $ docker compose up -d
  ```
  > Note: The default setup binds to ports 80/443. Modify `docker-compose.yml` if needed.
  
4. Access the "ogame-app" Docker container:
  ```
  $ docker exec -it ogame-app /bin/bash
  ```

5. Run Laravel setup commands to download composer dependencies, generate an encryption key and prepare the database:
  ```
  $ composer install
  $ php artisan key:generate
  $ php artisan migrate
  ```

After completing the setup, visit http://localhost to access OGameX. You first need to create an account (no email validation), afterwards you can login using that account.

### <a name="production"></a> b) Production: Install OGameX using Docker
For production there is a separate docker-compose file that is used to run the application. This file is called `docker-compose.prod.yml`. This configuration contains
several performance optimizations and security settings that are not present in the development configuration.

***Caution:*** the production configuration is not yet fully optimized and should be used with caution. As an example, the database root user uses a default password which should be changed to something unique. 
You should review all settings before deploying this project to a publicly accessible server.

1. Clone the repository.
2. Copy `.env.example-prod` to `.env`.
3. Launch the project using Docker Compose:
  ```
  $ docker compose -f docker-compose.prod.yml up -d --build --force-recreate
  ```
> Note: The default setup binds to ports 80/443. Modify `docker-compose.yml` if needed.

4. Access the "ogame-app" Docker container:
  ```
  $ docker exec -it ogame-app /bin/bash
  ```

5. Run Laravel setup commands to download composer dependencies, generate an encryption key, cache configuration and prepare the database:
  ```
  $ composer install --no-dev
  $ php artisan key:generate
  $ php artisan migrate --force
  $ php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
  ```

After completing the setup, visit http://localhost to access OGameX. You first need to create an account (no email validation), afterwards you can login using that account.

### <a name="tips-for-initial-setup"></a> c) Tips for initial setup
- **Admin account**: By default, the first registered user is assigned the admin role which can see the admin bar and is able to change server settings. You can also assign the admin role manually via the command line:
  ```
  $ php artisan ogamex:assign-admin-role {username}
  ```
  To remove the admin role from a user, use the following command:
  ```
    $ php artisan ogamex:remove-admin-role {username}
  ```

## <a name="support"></a> 📞 Support

Encountered issues? Open a ticket on GitHub.

## <a name="license"></a> 📰 License

The OGameX Laravel source code is open-source software licensed under the MIT license. See the LICENSE file for more details. All rights and concepts related to OGame are owned by GameForge GmbH.

