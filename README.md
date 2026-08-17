<div align="center">

🌟 **If you find this project useful, please consider giving it a star!** 🌟

</div>
<p align="center"><img align="center" src="https://github.com/lanedirt/OGameX/assets/6917405/c81061d5-0310-4574-a91d-1ea155b567c0" alt="OGameX logo" /></p>

<p align="center">
<a href="https://main.ogamex.dev">Live demo 🚀</a> • <a href="#installation">Installation 📦</a> • <a href="https://github.com/lanedirt/OGameX/blob/main/CONTRIBUTING.md">Contributing 💻</a>
</p>


<p align="center">
<strong>Open-source OGame redesign clone</strong>
</p>

<div align="center">

[<img src="https://img.shields.io/github/v/release/lanedirt/OGameX?include_prereleases&logo=github">](https://github.com/lanedirt/OGameX/releases)
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-docker-compose-prod.yml?label=docker-compose%20build">](https://github.com/lanedirt/OGameX/actions/workflows/run-docker-compose-prod.yml)
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-tests-docker-compose.yml?label=tests">](https://github.com/lanedirt/OGameX/actions/workflows/run-tests-docker-compose.yml)
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-phpstan-code-analysis.yml?label=static%20code%20analysis">](https://github.com/lanedirt/OGameX/actions/workflows/run-phpstan-code-analysis.yml)
[<img src="https://img.shields.io/github/actions/workflow/status/lanedirt/OGameX/run-laravel-pint-code-style-checker.yml?label=psr-12%20code%20style">](https://github.com/lanedirt/OGameX/actions/workflows/run-laravel-pint-code-style-checker.yml)

</div>

<div align="center">

[![good first issues open](https://img.shields.io/github/issues/lanedirt/OGameX/good%20first%20issue.svg?logo=github)](https://github.com/lanedirt/OGameX/issues?q=is%3Aopen+is%3Aissue+label%3A"good+first+issue")
[<img alt="Discord" src="https://img.shields.io/discord/1278814992988110922?logo=discord&logoColor=%237289da&label=join%20discord%20chat&color=%237289da">](https://discord.gg/HJ4QRxxB5N)

</div>


OGameX is an open-source OGame redesign clone. This clone is built fully from scratch using the latest Laravel framework version and uses modern PHP practices. All major functionality is covered by unit and feature tests which automatically run on every build.

We welcome any and all contributions to this project! If you want to help out, please read the [contributing](#contributing) section. If you have any questions you can [join the OGameX discord](https://discord.com/invite/HJ4QRxxB5N) to get in touch with the maintainers and other contributors.

Disclaimer: this project is purely fan-based and does not contain any commercial features. All backend code is written from scratch. The rights and concepts for the artwork and frontend belong to the original creators: GameForge GmbH. Support them by checking out the official version: https://ogame.org.

## 🖥️ Live demo
- Main branch (nightly builds): [https://main.ogamex.dev](https://main.ogamex.dev)
- Latest stable release **(0.14.0)**: [https://release.ogamex.dev](https://release.ogamex.dev)

## 📝 Table of Contents
- [1. Example screenshots](#screenshots)
- [2. About the author](#author)
- [3. Goal](#goal)
- [4. Roadmap](#roadmap)
  - [a) Upcoming Features](#upcoming-features)
- [5. Contributing](#contributing)
- [6. Disclaimer](#disclaimer)
- [7. Installation](#installation)
  - [a) Development](#development)
  - [b) Production](#production)
- [8. Upgrade](#upgrade)
- [9. Support](#support)
- [10. Sponsorship](#sponsorship)
- [11. License](#license)
- [12. OGameX related projects](#related-projects)

## <a name="screenshots"></a> 🖥️ 1. Example screenshots


<img width="1142" alt="Screenshot 2024-10-06 at 15 41 14" src="https://github.com/user-attachments/assets/7f9041ad-82cd-42b0-acd1-0036c0f49da2">
<img width="1129" alt="Screenshot 2024-10-06 at 15 41 45" src="https://github.com/user-attachments/assets/d8a9e612-1433-4750-9f5f-05246f642740">
<img width="1142" alt="Screenshot 2024-10-06 at 15 42 05" src="https://github.com/user-attachments/assets/aaf9ede8-0aab-4985-87f5-3016eef4fa5f">
<img width="1132" alt="Screenshot 2024-10-06 at 15 42 44" src="https://github.com/user-attachments/assets/cb112ca3-73d5-42ba-98f1-be844533be41">
<img width="1147" alt="Screenshot 2024-10-06 at 15 43 18" src="https://github.com/user-attachments/assets/d90a0651-c841-4f3a-a119-8abde4c45b90">

## <a name="author"></a> ✨ 2. About the author

My ([@lanedirt](https://github.com/lanedirt)) journey into software development began in 2007 at the age of 14 when I discovered the source code for Ugamela, an early open-source PHP clone of OGame. I really liked running my own browser game server and dedicated myself to modifying this version and translating it to Dutch, leading to the launch of OGameX.nl. This server, active from 2007 to 2009, nurtured a small yet engaged community. This experience not only sparked my passion for software development but also laid the groundwork for my professional career. OGame has always held a special place in my heart, which is why now, 15 years later, I've decided to return to it and create this open-source clone from the ground up.

## <a name="goal"></a> ✨ 3. Goal

The primary goal of this fan-based project is to engineer a faithful rendition of OGame, specifically reflecting its state prior to the Lifeforms update introduced in 2022. This initiative, purely fan-based and non-commercial, is pursued strictly for educational purposes.

## <a name="roadmap"></a> 🖥️ 4. Roadmap

OGameX is under active development with a lot of core features already implemented and working:

- Planets / buildings / research / shipyard / defense / galaxy / highscores / messages
- Fleet dispatch missions (transport, deployment, colonisation, espionage, attack, recycle, moon destruction)
- Missile attacks
- Battle engine
  - Rust version for high performance via PHP FFI (up to 200x faster compared to PHP)
  - PHP version as fall-back
- Moon
  - Moon creation through debris field after battle
  - Moon buildings, phalanx, jump gate
- Wreck fields / Space Dock
- Character classes
- In-game chat
- Buddies / notes / fleet templates
- Admin panel
- Expedition mission with various outcomes
- Basic dark matter features (non-commercial), including merchant and planet relocate
- Alliances
- ACS fleet dispatch missions
- Multi-language

### <a name="upcoming-features"></a> Upcoming Features

The next major upcoming features that are being worked on:

- Improved fleet mission processing via worker queue
- All dark matter features (non-commercial)
- General bugfixes and stability improvements
- More admin / server management features

## <a name="contributing"></a> 🚀 5. Contributing

Contributions are warmly welcomed, whether in development, testing, or spreading the word. Feel free to submit pull requests or contact me for any other contributions.

A good starting point are issues labeled as "good first issue".

[![good first issues open](https://img.shields.io/github/issues/lanedirt/OGameX/good%20first%20issue.svg?logo=github)](https://github.com/lanedirt/OGameX/issues?q=is%3Aopen+is%3Aissue+label%3A"good+first+issue")

Read the [CONTRIBUTING.md](https://github.com/lanedirt/OGameX/blob/main/CONTRIBUTING.md) file for more information.

## <a name="disclaimer"></a> 📓 6. Disclaimer

This project is a non-commercial hobby project. All rights and concepts related to OGame are owned by GameForge GmbH. We encourage supporters to try the official OGame at https://ogame.org to support its creators.

## <a name="installation"></a> 🖥️ 7. Installation

Docker Compose is the **only supported** install path (this is what CI tests). Full steps — troubleshooting, post-install, credentials, SSL, admin commands — are in **[docs/install.md](docs/install.md)**.

First boot of `ogamex-app` can take up to ~10 minutes (Composer + Rust). Wait until the container is healthy before opening the site.

### <a name="development"></a> a) Development

```
$ git clone https://github.com/lanedirt/OGameX.git
$ cd OGameX
$ docker compose up -d
```

Then open http://localhost. The first registered (non-Legor) account becomes admin.

> Windows: development compose is slow; prefer [production compose](#production) for usable speed (OPcache on, so PHP edits are not live). Artisan: `docker compose exec -it ogamex-app bash`.

### <a name="production"></a> b) Production

***Caution:*** bundled production compose is not fully hardened (default DB password `toor`). Review settings before a public bind.

```
$ git clone https://github.com/lanedirt/OGameX.git
$ cd OGameX
$ cp .env.example-prod .env
$ docker compose -f docker-compose.prod.yml up -d --build --force-recreate
```

Then open https://localhost (self-signed certificate; `APP_ENV=production` forces HTTPS).

## <a name="upgrade"></a> 🖥️ 8. Upgrade

Upgrade, admin role, and password reset: **[docs/install.md](docs/install.md#upgrade)**.

## <a name="support"></a> 📞 9. Support

Did you encounter issues in this project? Please open a ticket on GitHub and we'll try to help you out as soon as possible.

## <a name="sponsorship"></a> 💰 10. Sponsorship
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

## <a name="license"></a> 📰 11. License

The OGameX Laravel source code is open-source software licensed under the MIT license. See the LICENSE file for more details. All rights and concepts related to OGame are owned by GameForge GmbH.

## <a name="related-projects"></a> 🌍 12. OGameX related projects

The following projects either host OGameX servers or are based on the OGameX core.

*Note: these projects are maintained independently and are not affiliated with the OGameX project or its maintainers in any way.*

| Project Name | Description | Link | Type |
|-------------|-------------|------|------|
| MWarfare | MWarfare is derived from the OGameX project, it takes the game forward using modern technology and game design to create a more realistic version of OGame that the community has long deserved. | https://mwarfare.com/ | Not Open Source
| OGameX Combat Simulator | High-performance OGame combat simulator based on the OGameX Rust battle engine. Features a blazing fast Rust-powered WASM engine (~200x faster than PHP), client-side calculations that work offline, and accurate battle simulations based on OGameX formulas. | [GitHub](https://github.com/rbardtke/OgameX-Combat-Simulator) | Open Source (MIT) |
| Space Rivals | Built on top of the OGameX core with many different customizations and features. | https://space-rivals.net | Not Open Source |

Do you want your own OGameX-related project to be listed here? Create an issue in the [GitHub issues page](https://github.com/lanedirt/OGameX/issues) and provide details about your project.
