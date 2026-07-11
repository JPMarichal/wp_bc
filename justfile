cli := "docker exec wp_bc_cli wp"

# Run full maintenance (transients, spam, auto-drafts, DB optimize)
maintenance:
    {{cli}} transient delete --expired
    -{{cli}} comment delete $({{cli}} comment list --status=spam --format=ids) --force
    -{{cli}} post delete $({{cli}} post list --post_status=auto-draft --format=ids) --force
    {{cli}} db optimize

# Run the shell maintenance script
script:
    docker exec wp_bc_cli sh /var/www/html/bin/wp-maintenance.sh

# List all autoloaded options sorted by size (largest first)
autoload-audit:
    {{cli}} option list --autoload=yes --format=table
    @echo "---"
    @echo "Large autoload options (>100KB):"
    {{cli}} db query "SELECT option_name, LENGTH(option_value) AS bytes FROM wp_options WHERE autoload='yes' ORDER BY bytes DESC LIMIT 20"

# Delete a specific autoloaded option by name
autoload-clean option_name:
    {{cli}} option delete {{option_name}}

# Activate a plugin
activate plugin:
    {{cli}} plugin activate {{plugin}}

# Deactivate a plugin
deactivate plugin:
    {{cli}} plugin deactivate {{plugin}}

# Regenerate all media thumbnails
media-regenerate:
    {{cli}} media regenerate

# Clean only expired transients
transient-clean:
    {{cli}} transient delete --expired

# Optimize database tables
db-optimize:
    {{cli}} db optimize

# List active plugins
plugins:
    {{cli}} plugin list

# Run Lighthouse performance audit (all default pages)
perf:
    cd wp-content/themes/generatepress-child && npm run perf

# Run Lighthouse on a specific URL path (e.g. just perf-url /about)
perf-url path:
    cd wp-content/themes/generatepress-child && npm run perf -- {{path}}

# Compress all JPEG images in uploads to quality 82 (run inside Docker)
compress-images quality="82":
    docker exec wp_bc php /var/www/html/wp-content/themes/generatepress-child/bin/compress-images.php /var/www/html/wp-content/uploads {{quality}}

# Regenerate all media thumbnails
regenerate-thumbnails:
    docker exec wp_bc_cli wp media regenerate --yes

# Show available commands
default:
    @just --list
