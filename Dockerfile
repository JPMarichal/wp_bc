FROM wordpress:6-apache

# Install Composer + WP-CLI
RUN set -eux; \
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"; \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"; \
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then \
        >&2 echo 'ERROR: Invalid installer checksum'; \
        rm composer-setup.php; \
        exit 1; \
    fi; \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer; \
    rm composer-setup.php; \
    curl -s -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

# Startup script to ensure plugin vendors are installed
COPY bin/composer-install.sh /usr/local/bin/composer-install.sh
RUN chmod +x /usr/local/bin/composer-install.sh

# Hook into the existing entrypoint
RUN { \
    echo '#!/bin/bash'; \
    echo 'set -e'; \
    echo 'composer-install.sh &'; \
    echo 'exec docker-entrypoint.sh "$@"'; \
} > /usr/local/bin/wp-entrypoint.sh && chmod +x /usr/local/bin/wp-entrypoint.sh

ENTRYPOINT ["wp-entrypoint.sh"]
CMD ["apache2-foreground"]