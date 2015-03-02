#!/bin/sh

# Token dir as on sweb where this runs
TOKEN_DIR="/opt/local/checkpoints/data/checkpoints/tokens"

# Max TTL age in days
MAX_TTL=1

# Tokens are files with name =~ m/^[0-9]{6,6}$/
# EG: 488360

if [ ! -d "$TOKEN_DIR" ]; then
  echo "$0: Error, $TOKEN_DIR is not a directory" >&2
fi

cd "$TOKEN_DIR"

find . -type f -name '[0-9][0-9][0-9][0-9][0-9][0-9]' -mtime +$MAX_TTL -delete

