# UniFi HotSpot Manager
This is a HotSpot Manager like the default one of [UniFi Ubiquiti](https://unifi-sdn.ubnt.com/), but this one has some advantages and is easier to use.

[![Latest stable release](https://img.shields.io/github/release/mcmilk/UniFi-HotSpot-Manager.svg)](https://github.com/mcmilk/UniFi-HotSpot-Manager/releases)
[![PayPal.me](https://img.shields.io/badge/PayPal-me-blue.svg?maxAge=2592000)](https://www.paypal.me/TinoReichardt)


## Features
- allow different Usergroups via Voucher prefixes with just one [Ubiquiti Controller] ;)
- own user access control, no need for users to get a real admin account on the [Ubiquiti Controller]
- variable printing, creating, filtering and deleting of vouchers
- deleting multiple vouchers with _one click_ just works, but the needed time is about 0.8 seconds per voucher :(
- blocking, unblocking and re-connecting of users (the default HotSpot system misses these functions, but the admin interface has, yes I know... but this is not for users)
- ... a lot more, this is just the first writing of that README

## Included libraries
- [UniFi PHP API](https://github.com/Art-of-WiFi/UniFi-API-browser/tree/master/phpapi), by [Erik Slooff](https://github.com/malle-pietje)
- [Gettext PHP](https://github.com/oscarotero/Gettext) by [Oscar Otero](https://github.com/oscarotero/)
- [FPDF](http://www.fpdf.org/) for voucher creation

## Used libraries via [CDN]
All [CDN] libraries I am including are using the [MIT license], so I use it also ;)
- [jQuery] JavaScript Library - used everywhere
- [DataTables] - [jQuery] plug-in for all the tables
- [Bootstrap] - the HTML, CSS, and JavaScript framework
- [Bootstrap] [X-editable](https://vitalets.github.io/x-editable/) - used for editing within the [DataTables]
- [Bootstrap] [Form Validator](https://1000hz.github.io/bootstrap-validator/) - used for some forms
- [Bootstrap Dialog](https://github.com/nakupanda/bootstrap3-dialog) - used
- [MomentJS](https://momentjs.com/) - Parse, validate, manipulate and display dates and times in JavaScript

## Requirements
- an [Ubiquiti Controller]
- a webserver wit PHP >= 5.5
- some time for installing and testing

## Installation
- clone the repository `git clone https://github.com/mcmilk/UniFi-HotSpot-Manager`
- the `private` folder can be setup to some private directory (not meant to stay in webroot)
- the files `js/hotspot.js` and `index.php` should be copied to your web folder
- edit the file `index.php` for setting up the private path
- then go to your `private` path an take a look into the file `config.php.sample`
- modify that file and rename it to `config.php`
- then take a look at the file `user.txt.sample` ... then rename it to `userdb.txt`
- start your browser and modify the user data...
- for creating an own qr-code of the WiFi settings for your network on the vouchers, just use the script provided here: `contrib/qr-gen.sh`

## Todo
- move userdb.txt to json format (like languages.json)
- find strings, which have not been found by my current i18n initiative ;)
- when all strings are found, users can submit new translations
- show some more information on the "list_clients" action
- the same for the "list_guest_aps" site

## Screenshots
- creating vouchers and language selection
![p1](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/01_CreateVouchers_de.png)
- user management
![p2](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/04_UserManagement.png)
- translating the web user interface is supported also
![p3](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/05_Translations.png)
- list current guests in your network, you can also block or unblock them
![p4](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/03_ListGuests.png)
- same guest list, but a much smaller screen, responsive design ;)
![p5](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/07_GuestListSmall.png)

## Sample voucher lists
- [printed, with german language settings](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/tickets-de.pdf)
- [printed with english language settings](https://github.com/mcmilk/UniFi-HotSpot-Manager/blob/master/contrib/tickets-en.pdf)
- the comments in the last line are the same, the headline can be set per user

## License and redistribution
This HotSpot Manager is licensed under the [MIT license], Copyright (c) 2017 Tino Reichardt.

[CDN]:https://en.wikipedia.org/wiki/Content_delivery_network/
[MIT license]:https://opensource.org/licenses/MIT
[DataTables]:https://datatables.net/
[jQuery]:https://github.com/jquery/jquery
[Bootstrap]:https://github.com/twbs/bootstrap
[Ubiquiti Controller]:https://www.ubnt.com/download/unifi/unifi-cloud-key/uc-ck
