#!/bin/bash
# Install Composer dependencies for bc-carbon-fields plugin
PLUGIN_DIR="/var/www/html/wp-content/plugins/bc-carbon-fields"

if [ -f "$PLUGIN_DIR/composer.json" ] && [ ! -d "$PLUGIN_DIR/vendor" ]; then
    echo "Installing Composer dependencies for bc-carbon-fields..."
    cd "$PLUGIN_DIR"
    composer install --no-dev --quiet 2>&1
    echo "Composer install complete."
fi
