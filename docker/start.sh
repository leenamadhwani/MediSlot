#!/bin/bash
set -e

if [ -n "${PORT:-}" ]; then
  echo "Using Render port: $PORT"
  sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
fi

exec /usr/local/bin/apache2-foreground
