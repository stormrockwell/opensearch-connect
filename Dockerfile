# Dockerfile
FROM wordpress

ARG PLUGIN_NAME=wordpres-osc-plugin

# Setup the OS
RUN apt-get -qq update ; apt-get -y install unzip curl sudo subversion mariadb-client \
        && apt-get autoclean \
        && chsh -s /bin/bash www-data

# Install wp-cli
RUN curl https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp-cli.phar \
        && echo "#!/bin/bash" > /usr/local/bin/wp-cli \
        && echo "su www-data -c \"/usr/local/bin/wp-cli.phar --path=/var/www/html \$*\"" >> /usr/local/bin/wp-cli \
        && chmod 755 /usr/local/bin/wp-cli* \
        && echo "*** wp-cli command installed"

# Install composer
# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Create testing environment
# COPY --chmod=755 bin/install-wp-tests.sh /usr/local/bin/
# RUN echo "#!/bin/bash" > /usr/local/bin/install-wp-tests \
#         && echo "su www-data -c \"install-wp-tests.sh \${WORDPRESS_DB_NAME}_test root root \${WORDPRESS_DB_HOST} latest\"" >> /usr/local/bin/install-wp-tests \
#         && chmod ugo+x /usr/local/bin/install-wp-test* \
#         && su www-data -c "/usr/local/bin/install-wp-tests.sh ${WORDPRESS_DB_NAME}_test root root '' latest true" \
#         && echo "*** install-wp-tests installed"