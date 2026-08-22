#!/bin/sh
set -eu

project_root="$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)"

php-fpm -F \
    -d display_errors=Off \
    -d display_startup_errors=Off \
    -d expose_php=Off \
    -d log_errors=On &

exec nginx -e /dev/stderr -c "$project_root/nginx.conf"
