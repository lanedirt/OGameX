# OGameX - An Open-Source OGame Redesign Clone
[<img src="https://img.shields.io/github/v/release/lanedirt/OGameX?include_prereleases&logo=github">](https://github.com/lanedirt/OGameX/releases)  [<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-docker-compose.yml?label=docker-compose%20build">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-docker-compose.yml) [<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-sqlite.yml?label=tests">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-sqlite.yml)

OGameX is an open-source OGame clone aiming to recreate the official OGame experience, developed originally by GameForge GmbH. This clone is built from scratch using the Laravel 11.x framework and uses modern PHP practices.

### Demo sites where you can see OGameX in action:
- Main branch (nightly builds): [https://main.ogamex.dev](https://main.ogamex.dev)
- Latest stable release **(0.1.0)**: [https://release.ogamex.dev](https://release.ogamex.dev)

## Table of Contents
- [Example screenshots](#examples)
- [Background](#background)
- [Goal](#goal)
- [Current State of the Project](#current-state-of-the-project)
  - [Upcoming Features](#upcoming-features)
- [Contributing](#contributing)
- [Disclaimer](#disclaimer)
- [Installation](#installation)
  - [Install OGameX using Docker](#install-ogamex-using-docker)
- [Support](#support)
- [License](#license)

## Example screenshots
<img width="1051" alt="Screenshot 2024-03-20 at 22 15 36" src="https://github.com/lanedirt/OGameX/assets/6917405/f054a8fc-ca2c-43d1-9831-d2886a7cad40">
<img width="1075" alt="Screenshot 2024-03-20 at 22 15 44" src="https://github.com/lanedirt/OGameX/assets/6917405/b3a01356-1e2c-48f7-8603-159caedc2c2b">
<img width="1084" alt="Screenshot 2024-03-20 at 22 15 55" src="https://github.com/lanedirt/OGameX/assets/6917405/1c137e70-c3c2-47c7-b91f-b7a9eb10b1bc">

## Background

My journey into software development began in 2007 at the age of 14 when I discovered the source code for Ugamela, an early open-source PHP clone of OGame. I really liked running my own browser game server and dedicated myself to modifying this version and translating it to Dutch, leading to the launch of OGameX.nl. This server, active from 2007 to 2009, nurtured a small yet engaged community. This experience not only sparked my passion for software development but also laid the groundwork for my professional career. OGame has always held a special place in my heart, which is why now, 15 years later, I've decided to return to it and create this open-source clone from the ground up.

## Goal

The primary goal of this fan-based project is to engineer a faithful rendition of OGame, specifically reflecting its state prior to the Lifeforms update introduced in 2020. This initiative, purely fan-based and non-commercial, is pursued strictly for educational purposes.

## Current State of the Project

OGameX is under active development with several core features already implemented:

- Basic registration and login
- Planet creation and resource management (metal, crystal, deuterium, energy)
- Building and updating resources, facilities, shipyards, and defenses
- Basic galaxy overview
- Planet switching
- Highscore system

### Upcoming Features

New features are continiously being added. Upcoming features:

- Messages system
- Fleet dispatch missions (combat, transport, espionage, and more)
- Alliance system

## Contributing

Contributions are warmly welcomed, whether in development, testing, or spreading the word. Feel free to submit pull requests or contact me for any other contributions.

## Disclaimer

This project is a non-commercial hobby project. All rights and concepts related to OGame are owned by GameForge GmbH. We encourage supporters to try the official OGame at https://ogame.org to support its creators.

## Installation
The easiest way to get OGameX up and running on your own machine is by running the Docker containers via the docker-compose.yml file that is supplied in this repository.

Alternatively, you can also deploy this project manually on any host that supports at least the following:
- PHP >= 8.2
- MySQL/MariaDB
- Ability to enable specific PHP extensions (Ctype, cURL, DOM, Fileinfo and more...)

See the full list of requirements for Laravel 11.x and how to deploy to a server here: https://laravel.com/docs/11.x/deployment.

### Install OGameX using Docker

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

## Support

Encountered issues? Open a ticket on GitHub.

## License

The OGameX Laravel source code is open-source software licensed under the MIT license. See the LICENSE file for more details. All rights and concepts related to OGame are owned by GameForge GmbH.

