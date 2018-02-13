#! /bin/bash
composer install
chmod +x "`pwd`/onedrive"
ln -s "`pwd`/onedrive" "/usr/local/bin/onedrive"
