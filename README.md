
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

[CDN]:https://en.wikipedia.org/wiki/Content_delivery_network/
[MIT license]:https://opensource.org/licenses/MIT
[DataTables]:https://datatables.net/
[jQuery]:https://github.com/jquery/jquery
[Bootstrap]:https://github.com/twbs/bootstrap
