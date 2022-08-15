[![deutsche Version](https://logos.oxidmodule.com/de2_xs.svg)](README.md)
[![english version](https://logos.oxidmodule.com/en2_xs.svg)](README.en.md)

# D3 Order Manager Extension: LINK Mobility Mobile Messaging

Connection of the LINK Mobility API (message dispatch via SMS) to the D3 Order Manager

## Table of content

- [Installation](#installation)
- [How to use](#how-to-use)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [License](#license)

## Installation

This package requires an OXID eShop installed with Composer in a version defined in the [composer.json](composer.json).

Open a command line and navigate to the root directory of the shop (parent directory of source and vendor). Execute the following command. Adapt the path details to your installation environment.

```bash
php composer require d3/linkmobility4ordermanager:^1.0
``` 

Activate the module in Shopadmin under "Extensions -> Modules".

## How to use

The extension integrates itself directly in the OXID module "Order manager". Open the entry of the task to be added and select the action "Send SMS" in the actions in the section "Information". Configure this according to your requirements. 

Please set the necessary access settings to LINK Mobility in the interface module for the OXID shop.

## Changelog

See [CHANGELOG](CHANGELOG.md) for further informations.

## Contributing

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue. Don't forget to give the project a star! Thanks again!

- Fork the Project
- Create your Feature Branch (git checkout -b feature/AmazingFeature)
- Commit your Changes (git commit -m 'Add some AmazingFeature')
- Push to the Branch (git push origin feature/AmazingFeature)
- Open a Pull Request

## License
(status: 2022-09-01)

Distributed under the GPLv3 license.

```
Copyright (c) D3 Data Development (Inh. Thomas Dartsch)

This software is distributed under the GNU GENERAL PUBLIC LICENSE version 3.
```

For full copyright and licensing information, please see the [LICENSE](LICENSE.md) file distributed with this source code.