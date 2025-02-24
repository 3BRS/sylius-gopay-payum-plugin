#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# project root
cd "$(dirname "$DIR")"

mkdir -p tests/Application/public/media/image

set -x
APP_ENV="test" php -d error_reporting="E_ALL ^ E_DEPRECATED" -d memory_limit=1G vendor/bin/behat "$@"
