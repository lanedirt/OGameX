<h1 align="center"><img align="center" src="https://github.com/lanedirt/OGameX/assets/6917405/c81061d5-0310-4574-a91d-1ea155b567c0" alt="OGameX logo" /></h1>

<p align="center">
<a href="https://main.ogamex.dev">Live demo 🚀</a> • <a href="#installation">Installation 📦</a> • <a href="https://github.com/lanedirt/OGameX/blob/main/CONTRIBUTING.md">Contributing 💻</a> 
</p>


<h3 align="center">
Open-source OGame redesign clone
</h3>

<div align="center">
    
[<img src="https://img.shields.io/github/v/release/lanedirt/OGameX?include_prereleases&logo=github">](https://github.com/lanedirt/OGameX/releases) 
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-docker-compose.yml?label=docker-compose%20build">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-docker-compose.yml) 
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-sqlite.yml?label=tests">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-sqlite.yml) 
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-phpstan-code-analysis.yml?label=static code analysis">](https://github.com/lanedirt/OGameX/actions/workflows/run-phpstan-code-analysis.yml)
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-laravel-pint-code-style-checker.yml?label=psr-12%20code%20style">](https://github.com/lanedirt/OGameX/actions/workflows/run-laravel-pint-code-style-checker.yml)

</div>

<div align="center">

[![good first issues open](https://img.shields.io/github/issues/lanedirt/OGameX/good%20first%20issue.svg?logo=github)](https://github.com/lanedirt/OGameX/issues?q=is%3Aopen+is%3Aissue+label%3A"good+first+issue")
[<img alt="Discord" src="https://img.shields.io/discord/1278814992988110922?logo=discord&logoColor=%237289da&label=join%20discord%20chat&color=%237289da">](https://discord.gg/HJ4QRxxB5N)

</div>


OGameX is an open-source OGame redesign clone. This clone is built fully from scratch using the Laravel 11.x framework and uses modern PHP practices. All major functionality is covered by unit and feature tests which automatically run on every build. 

We welcome any and all contributions to this project! If you want to help out, please read the [contributing](#contributing) section. If you have any questions you can [join the OGameX discord](https://discord.com/invite/HJ4QRxxB5N) to get in touch with the maintainers and other contributors.

Disclaimer: this project is purely fan-based and does not contain any commercial features. All backend code is written from scratch. The rights and concepts for the artwork and frontend belong to the original creators: GameForge GmbH. Support them by checking out the official version: https://ogame.org.

## 🖥️ Live demo
- Main branch (nightly builds): [https://main.ogamex.dev](https://main.ogamex.dev)
- Latest stable release **(0.8.0)**: [https://release.ogamex.dev](https://release.ogamex.dev)

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
- [9. Sponsorship](#sponsorship)
- [10. License](#license)

## <a name="screenshots"></a> 🖥️ Example screenshots


<img width="1142" alt="Screenshot 2024-10-06 at 15 41 14" src="https://github.com/user-attachments/assets/7f9041ad-82cd-42b0-acd1-0036c0f49da2">
<img width="1129" alt="Screenshot 2024-10-06 at 15 41 45" src="https://github.com/user-attachments/assets/d8a9e612-1433-4750-9f5f-05246f642740">
<img width="1142" alt="Screenshot 2024-10-06 at 15 42 05" src="https://github.com/user-attachments/assets/aaf9ede8-0aab-4985-87f5-3016eef4fa5f">
<img width="1132" alt="Screenshot 2024-10-06 at 15 42 44" src="https://github.com/user-attachments/assets/cb112ca3-73d5-42ba-98f1-be844533be41">
<img width="1147" alt="Screenshot 2024-10-06 at 15 43 18" src="https://github.com/user-attachments/assets/d90a0651-c841-4f3a-a119-8abde4c45b90">

## <a name="author"></a> ✨ About the author

My ([@lanedirt](https://github.com/lanedirt)) journey into software development began in 2007 at the age of 14 when I discovered the source code for Ugamela, an early open-source PHP clone of OGame. I really liked running my own browser game server and dedicated myself to modifying this version and translating it to Dutch, leading to the launch of OGameX.nl. This server, active from 2007 to 2009, nurtured a small yet engaged community. This experience not only sparked my passion for software development but also laid the groundwork for my professional career. OGame has always held a special place in my heart, which is why now, 15 years later, I've decided to return to it and create this open-source clone from the ground up.

## <a name="goal"></a> ✨ Goal

The primary goal of this fan-based project is to engineer a faithful rendition of OGame, specifically reflecting its state prior to the Lifeforms update introduced in 2020. This initiative, purely fan-based and non-commercial, is pursued strictly for educational purposes.

## <a name="current-state"></a> 🖥️ Current State of the Project

OGameX is under active development with several core features already implemented:

- Registration and login
- Planets and resource management (metal, crystal, deuterium, energy)
- Building and updating resources, facilities, shipyards, and defenses
- Galaxy overview
- Planet switching
- Highscore system
- Messages system
- Battle system
- Basic admin panel
- Fleet dispatch missions
  - Transport
  - Deployment
  - Colonisation
  - Espionage
  - Attack
  - Recycle

### <a name="upcoming-features"></a> a) Upcoming Features

New features are continuously being added. Upcoming major features:
- Support for Moons 
  - Moon creation through debris field after battle
  - Moon buildings
  - Phalanx
  - Moon destruction fleet dispatch mission
- Alliance system
- ACS fleet dispatch missions


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
  ```
  $ git clone https://github.com/lanedirt/OGameX.git
  $ cd OGameX
  ```

2. Copy `.env.example` to `.env`.
  ```
  $ cp .env.example .env
  ```

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
  ```
  $ git clone https://github.com/lanedirt/OGameX.git
  $ cd OGameX
  ```

2. Copy `.env.example-prod` to `.env`.
  ```
  $ cp .env.example-prod .env
  ```

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
  $ php artisan key:generate --force
  $ php artisan migrate --force
  $ php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
  ```

After completing the setup, visit https://localhost to access OGameX. You first need to create an account (no email validation), afterwards you can login using that account.
> Note: The production version runs in forced-HTTPS (redirect) mode by default using a self-signed SSL certificate. If you want to access the application via HTTP, open `.env` and change `APP_ENV` from `production` to `local`.

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

Did you encounter issues in this project? Please open a ticket on GitHub and we'll try to help you out as soon as possible.

## <a name="sponsorship"></a> 💰 Sponsorship
We thank the following parties for sponsoring this project:

<table>
  <tr>
    <td align="center" width="200px">
      <a href="https://www.jetbrains.com/">
        <img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" width="100" alt="JetBrains Logo">
      </a>
      <br>
      <strong>JetBrains</strong>
      <br>
      Providing free open-source licenses for PhpStorm, WebStorm, and DataGrip.
    </td>
    <td align="center" width="200px">
      <!-- Placeholder for future sponsor -->
    </td>
    <td align="center" width="200px">
      <!-- Placeholder for future sponsor -->
    </td>
  </tr>
</table>

Interested in supporting OGameX? We welcome sponsorships of all sizes! Your support helps us maintain and improve this open-source project. Please get in touch with us via GitHub or Discord to discuss sponsorship opportunities.

## <a name="license"></a> 📰 License

The OGameX Laravel source code is open-source software licensed under the MIT license. See the LICENSE file for more details. All rights and concepts related to OGame are owned by GameForge GmbH.

