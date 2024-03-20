# OGameX - An Open-Source OGame Redesign Clone

OGameX is an open-source project aiming to recreate the OGame experience, developed originally by GameForge GmbH. This clone is built from scratch using the Laravel 11.x framework.

## Background

My journey into software development began in 2007 at the age of 14 when I discovered the source code for Ugamela, an early open-source PHP clone of OGame. I really liked running my own browser game server and dedicated myself to modifying this version and translating it to Dutch, leading to the launch of OGameX.nl. This server, active from 2007 to 2009, nurtured a small yet engaged community. This experience not only sparked my passion for software development but also laid the groundwork for my professional career. OGame has always held a special place in my heart, which is why now, 15 years later, I've decided to return to it and create this open-source clone from the ground up.

## Goal

The primary objective of this project is to develop a fully functional version of OGame, closely mirroring the game's features as they existed around the year 2020, prior to the introduction of the recent Lifeforms update.

## Current State of the Project

OGameX is under active development with several core features already implemented:

- Basic registration and login
- Planet creation and resource management (metal, crystal, deuterium, energy)
- Building and updating resources, facilities, shipyards, and defenses
- Basic galaxy overview

### Upcoming Features

New features are continiously being added. including:

- Planet switching
- Enhanced galaxy page
- Fleet missions (combat, transport, espionage, and more)

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

See the full list of requirements for Laravel 11.x here: https://laravel.com/docs/11.x/deployment.

### Install OGameX using Docker

1. Clone the repository.
2. Copy `.env.example` to `.env`.
3. Launch the project using Docker Compose:
  ```
  $ docker-compose up -d
  ```
  > Note: The default setup binds to ports 80/443. Modify `docker-compose.yml` if needed.
  
4. Access the "ogame-app" Docker container:
  ```
  $ docker exec -it ogame-app /bin/bash
  ```

5. Run Laravel setup commands to generate an encryption key and prepare the database:
  ```
  $ php artisan key:generate
  $ php artisan migrate
  ```

After completing the setup, visit http://localhost to access OGameX. You first need to create an account (no email validation), afterwards you can login using that account.

## Support

Encountered issues? Open a ticket on GitHub.

## License

OGameX is open-source software licensed under the MIT license. See the LICENSE file for more details.

