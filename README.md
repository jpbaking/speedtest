![LibreSpeed Logo](https://github.com/librespeed/speedtest/blob/master/.logo/logo3.png?raw=true)

# LibreSpeed

No Flash, No Java, No Websocket, No Bullshit.

This is a very lightweight speed test implemented in Javascript, using XMLHttpRequest and Web Workers.

## About this fork

This fork ([jpbaking/speedtest](https://github.com/jpbaking/speedtest)) restyles LibreSpeed with the **lazyway-io-design** system and streamlines it down to a single, modern UI. Notable changes versus upstream:

### Design

* **lazyway-io-design theme** throughout: flat white surfaces, brand blue `#12279E`, accent amber `#D9821F`, IBM Plex Sans + IBM Plex Mono typography, hairline borders, and modest radii.
* **Modern UI only.** The classic design and the old `index.html` design switcher (`design-switch.js`, `config.json`, `?design=` overrides, the `USE_NEW_DESIGN` env var) have been removed — `index.html` *is* the modern UI.
* The logo doubles as a **home link**, and the footer uses a blue section link with an amber dot separator.
* Themed, responsive **share** and **privacy** dialogs that hug their content and behave correctly on narrow screens.
* The shareable **result image** (`results/index.php`) is redrawn with the lazyway palette and IBM Plex fonts.
* The **connection stability** page (`stability.html`) is themed to match; its `Server:` selector now hides when an external target (Google, Cloudflare, …) is chosen, since it only applies to local servers.
* The privacy policy's *Data removal* section reflects an upcoming on-demand deletion feature.

### Deployment

* A hardened `docker-compose.yaml` (kept outside this repo) runs the container `read_only` with dropped Linux capabilities, `tmpfs` mounts for the few writable paths, a named volume for the SQLite telemetry database, and an `.env` file for all runtime configuration.

> Upstream project: [librespeed/speedtest](https://github.com/librespeed/speedtest).

## Try it

[Take a speed test](https://librespeed.org)

## Compatibility

All modern browsers are supported: IE11, latest Edge, latest Chrome, latest Firefox, latest Safari.
Works with mobile versions too.

## Features

* Download
* Upload
* Ping
* Jitter
* IP Address, ISP, distance from server (optional)
* Telemetry (optional)
* Results sharing (optional)
* Multiple Points of Test (optional)
* Connection stability test with latency charting, loss tracking, threshold alerts, and CSV export

![Screenrecording of a running Speedtest](https://speedtest.fdossena.com/mpot_v7.gif)

## Server requirements

* A reasonably fast web server with Apache 2 (nginx, IIS also supported)
* PHP 5.4 or newer (other backends also available)
* MariaDB or MySQL database to store test results (optional, Microsoft SQL Server, PostgreSQL and SQLite also supported)
* A fast! internet connection

## Installation

Assuming you have PHP and a web server installed, the installation steps are quite simple.

1. Download the source code and extract it
1. Copy the project files to your web server's shared folder (ie. `/var/www/html/speedtest` for Apache). For the current layout, the web root should contain `index.html`, `stability.html`, `speedtest.js`, `speedtest_worker.js`, `stability_worker.js`, `favicon.ico`, and the `backend` folder.
1. Also copy the contents of `frontend/` into the same web root so the modern UI assets end up in `styling/`, `javascript/`, `images/`, and `fonts/` next to the HTML files.
1. Optionally, copy the results folder too, and set up the database using the config file in it.
1. Be sure your permissions allow read and execute access where needed.
1. Visit YOURSITE/speedtest/index.html and voila!

### Installation Video

This video shows the installation process of a standalone LibreSpeed server: [Quick start installation guide for Debian 12](https://fdossena.com/?p=speedtest/quickstart_deb12.frag)

More videos will be added later.

## Android app

A template to build an Android client for your LibreSpeed installation is available [here](https://github.com/librespeed/speedtest-android).

## CLI client

A command line client is available [here](https://github.com/librespeed/speedtest-cli).

## .NET client

A .NET client library is available in the [`LibreSpeed.NET`](https://github.com/Memphizzz/LibreSpeed.NET) repo ([NuGet](https://www.nuget.org/packages/LibreSpeed.NET)), maintained by [MemphiZ](https://github.com/Memphizzz).

## Development

If you want to contribute or develop with LibreSpeed, see [DEVELOPMENT.md](DEVELOPMENT.md) for information about using npm for development tasks, linting, and formatting.

## Stability test

LibreSpeed includes a standalone connection stability test at `stability.html`, linked from the main interface. It repeatedly measures ping over a selected duration and reports current, average, minimum, maximum, jitter, and failed request percentage values with a live chart.

The stability test can target the local LibreSpeed backend, one of the configured multiple points of test, or built-in external targets such as Google, Cloudflare, and Apple. It also supports optional latency threshold alerts and CSV export of the collected samples. Docker deployments copy `stability.html` and `stability_worker.js` into the web root and reuse the same server list configuration as the main UI.

## Docker

A docker image is available on [GitHub](https://github.com/librespeed/speedtest/pkgs/container/speedtest), check our [docker documentation](doc_docker.md) for more info about it.
The image is built every week to include an updated version of the ipinfo-DB used for ISP detection. Also this ensures, that the latest security patches in PHP are installed. Therefore we recommend to use the `latest` image.

## Go backend

A Go implementation is available in the [`speedtest-go`](https://github.com/librespeed/speedtest-go) repo, maintained by [Maddie Zhan](https://github.com/maddie).

## Rust backend

A Rust implementation is available in the [`speedtest-rust`](https://github.com/librespeed/speedtest-rust) repo, maintained by [Sudo Dios](https://github.com/sudodios).

## Node.js backend

A partial Node.js implementation is available in the `node` branch, developed by [dunklesToast](https://github.com/dunklesToast). It's not recommended to use at the moment.

## Donate

[![Donate with Liberapay](https://liberapay.com/assets/widgets/donate.svg)](https://liberapay.com/fdossena/donate)
[Donate with PayPal](https://www.paypal.me/sineisochronic)

## License

Copyright (C) 2016-2024 Federico Dossena

Modifications Copyright (C) 2026 Joseph Baking

This fork modifies the original LibreSpeed (theming, removal of the
classic UI/design switcher, themed result and stability pages, and
deployment changes). The affected files have been changed from their
upstream versions; see this repository's commit history for the specific
modifications and their dates. This work remains licensed under the GNU
Lesser General Public License, version 3 or (at your option) any later
version.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/lgpl>.

### Bundled fonts

This fork bundles the **IBM Plex** font files used to render the shareable
result image (`results/IBMPlexSans-*.ttf`, `results/IBMPlexMono-*.ttf`).
IBM Plex is © 2017 IBM Corp. and is licensed separately under the **SIL
Open Font License, Version 1.1** — see [`results/IBMPlex-OFL.txt`](results/IBMPlex-OFL.txt).
The web UI additionally loads IBM Plex from Google Fonts at runtime.
