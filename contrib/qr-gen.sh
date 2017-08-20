#!/bin/sh
############################################################################
# Script for creating a small png file, which can be used on the tickets.
#
# /TR 2017-08-15
############################################################################

SSID="Some SSID"
PASS="Some WiFi Password"
OPTS="-s2 "

qrencode $OPTS -lL -t png -o qr-code.png "WIFI:T:WPA;S:$SSID;P:$PASS;;"
optipng qr-code.png

# 74x74
#qrencode $OPTS -lL -t png -o wifi_L.png "WIFI:T:WPA;S:$SSID;P:$PASS;;"
#qrencode $OPTS -lM -t png -o wifi_M.png "WIFI:T:WPA;S:$SSID;P:$PASS;;"

# 82x82 (to big for the tickets)
#qrencode $OPTS -lQ -t png -o wifi_Q.png "WIFI:T:WPA;S:$SSID;P:$PASS;;"

# 90x90 (to big for the tickets)
#qrencode $OPTS -lH -t png -o wifi_H.png "WIFI:T:WPA;S:$SSID;P:$PASS;;"
