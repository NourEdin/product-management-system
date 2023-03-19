#!/bin/bash
echo "Entrypoint reached"

composer install

# Run the dev server
symfony server:start