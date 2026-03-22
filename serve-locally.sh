#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8888}"
echo "Starting server on http://localhost:${PORT}"
php -S "0.0.0.0:${PORT}" -t public
