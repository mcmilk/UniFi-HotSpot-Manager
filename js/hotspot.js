
/**
 * UniFi HotSpot Manger
 *
 * Copyright (c) 2017, Tino Reichardt <milky dash unifi at mcmilk.de>
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * This file contains mostly "render" functions for the datatables.
 * https://cdn.datatables.net/
 */

var debug = 1;
function log() {
  if (window.console && console.log && debug)
    console.log('[hotspot] ' + Array.prototype.join.call(arguments,' '));
}

/**
 * see https://datatables.net/reference/option/columns.render
 * type: filter / display / type / sort
 * filter + display => schick machen
 */
function fmt_wificode(val, type, row) {
  //log("fmt_wificode() val="+val+" type="+type);
  if (!val) return "-";
  if (type === 'display' || type === 'filter') {
    return val.slice(0, 5) + "-" + val.slice(5, 10);
  }
  return val;
}

function fmt_datetime(val, type) {
  //log("fmt_datetime() val="+val+" type="+type);
  if (type === 'display' || type === 'filter') {
    return (moment(val*1000).format("YYYY-MM-DD")) + " um " + (moment(val*1000).format("HH:mm"));
  }
  return val;
}

function fmt_human(bytes, type, row) {
  //log("fmt_human() val="+bytes+" type="+type);
  if (typeof bytes === undefined) bytes = 0;
  if (type === 'display' || type === 'filter') {
    //console.log(row);
    var sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
  }
  return bytes;
};

function fmt_human_mb(mbytes, type, row) {
  return fmt_human(1024 * 1024 * mbytes, type, row);
};

function fmt_duration(seconds, type) {
  //log("fmt_duration() val="+seconds+" type="+type);
  if (type === 'display' || type === 'filter') {
    seconds *= 60;
    var years = parseInt(seconds/60/60/24/365);
    seconds -= years * 60 * 60 * 24 * 365;
    var days = parseInt(seconds/60/60/24);
    seconds -= days * 60 * 60 * 24;
    var hours = parseInt(seconds/60/60);
    seconds -= hours * 60 * 60;
    var minutes = parseInt(seconds/60);
    seconds -= minutes * 60;

    r = "";
    if (years)   { if (years == 1)   { r += "1 Jahr ";   } else { r += years   + " Jahre "; } }
    if (days)    { if (days == 1)    { r += "1 Tag ";    } else { r += days    + " Tage "; } }
    if (hours)   { if (hours == 1)   { r += "1 Stunde "; } else { r += hours   + " Stunden "; } }
    if (minutes) { if (minutes == 1) { r += "1 Minute "; } else { r += minutes + " Minuten "; } }
    if (seconds) { r += seconds + " Sekunden" }

    return r;
  }
  return seconds;
}
