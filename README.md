# seat-connector
Provide basic layer for SeAT connectors

[![Latest Unstable Version](https://img.shields.io/packagist/vpre/warlof/seat-connector?style=for-the-badge)](https://packagist.org/packages/warlof/seat-connector)
[![Latest Stable Version](https://img.shields.io/packagist/v/warlof/seat-connector?style=for-the-badge)](https://packagist.org/packages/warlof/seat-connector)
[![Latest Stable Version](https://img.shields.io/github/downloads/warlof/seat-connector/total?style=for-the-badge)](https://packagist.org/packages/warlof/seat-connector)
[![Latest Stable Version](https://img.shields.io/badge/SeAT-3.0.x-blueviolet?style=for-the-badge)](https://github.com/eveseat/seat)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/warlof/seat-connector?style=for-the-badge)](https://codeclimate.com/github/warlof/seat-connector/maintainability)
[![License](https://img.shields.io/github/license/warlof/seat-connector?style=for-the-badge)](https://github.com/warlof/seat-connector/blob/master/LICENSE)

# Preview

The universal SeAT Connector Layer provider a single point of access for both your users and staff.
You can manage users, policies, logs and everything for the same plugin and it will be maintain the same way on all platforms.

![Access Management](./docs/acl.png)
![Identities](./docs/identities.png)
![Logs](./docs/logs.png)
![Registration](./docs/registration.png)
![Settings](./docs/settings.png)
![Users](./docs/users.png)

# Drivers

You'll find bellow all supported platforms

| Platform      | Latest Version                                                                                                 | Maintainer     | Composer Chain                  | Repository                                       |
| ------------- | -------------------------------------------------------------------------------------------------------------- | -------------- | ------------------------------- | ------------------------------------------------ |
| **Teamspeak** | ![Latest Stable Version](https://img.shields.io/packagist/v/warlof/seat-teamspeak?style=for-the-badge)         | Warlof Tutsimo | `warlof/seat-teamspeak`         | https://github.com/warlof/seat-teamspeak         |
| **Discord**   | ![Latest Stable Version](https://img.shields.io/packagist/v/warlof/seat-discord-connector?style=for-the-badge) | Warlof Tutsimo | `warlof/seat-discord-connector` | https://github.com/warlof/seat-discord-connector |
| **Slack**     | ![Latest Stable Version](https://img.shields.io/packagist/v/warlof/slackbot?style=for-the-badge)               | Warlof Tutsimo | `warlof/slackbot`               | https://github.com/warlof/slackbot               |

# Installation

To use this plugin, you need at least one driver as it's only provide business logic for policy and UI.
The connector can be install using `composer require warlof/seat-connector --update-no-dev`
Any driver can be install using `composer require {composer chain} --update-no-dev`

You can add and/or remove drivers at any time.

# Commands

The connector is shipped with two commands :

 - `seat-connector:sync:sets` will refresh driver sets known by SeAT (by default, it will refresh sets for all drivers - you can specify driver using `--driver` argument)
 - `seat-connector:apply:policies` will apply specified policy (by default, it will apply policy on all drivers - you can specify driver using `--driver` argument)

# Structure
![UML Class Schema](./docs/UML.png)

![UML Object Schema](./docs/ConnectorObjectDiagram.png)

Build your own [driver](./docs/ImplementDriver.md)
