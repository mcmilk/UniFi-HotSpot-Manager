
## UniFi HotSpot Manager
This is a HotSpot Manager like the default one of Ubiquiti. But it has some advantages and is easier to use ;)

### Features
- allow different Usergroups via Voucher prefixes with just one Controller ;)
- own user access control, no need for users to get real account on the Ubiquiti Controller
- variable printing, creating, filtering and deleting of vouchers
- deleting multiple vouchers in just one step works, but the needed time is about one seconds per voucher :(
- blocking, unblocking and re-connecting of users (the default HotSpot system misses these functions)
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

## Installation
- clone the repository `git clone https://github.com/mcmilk/UniFi-HotSpot-Manager`
- the `private` folder can be setup to some private directory (not meant to stay in webroot)
- the files `js/hotspot.js` and `index.php` should be copied to your web folder
- edit the file `index.php` for setting up the private path
- then go to your `private` path an take a look into the file `config.php.sample`
- modify that file and rename it to `config.php`
- then take a look at the file `user.txt.sample` ... then rename it to `userdb.txt`
- start you browser and modify the user data and you are ready...

## Sample voucher lists
- printed, with german language settings
- printed with english language settings
- the comments in the last line are the same, the headline is setable per user

## License and redistribution
This HotSpot Manager is licensed under the [MIT license], Copyright (c) 2017 Tino Reichardt.

[CDN]:https://en.wikipedia.org/wiki/Content_delivery_network/
[MIT license]:https://opensource.org/licenses/MIT
[DataTables]:https://datatables.net/
[jQuery]:https://github.com/jquery/jquery
[Bootstrap]:https://github.com/twbs/bootstrap
