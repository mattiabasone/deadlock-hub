#!/usr/bin/env bash

create_artifact() {
  echo "Cleanup old build..."
  rm -rf ${ROOT_DIR}/build/repo
  rm -rf ${ROOT_DIR}/artifact
  mkdir -p ${ROOT_DIR}/artifact

  git clone git@github.com:mattiabasone/deadlock-hub.git ${ROOT_DIR}/build/repo
  cd ${ROOT_DIR}/build/repo || exit 1

  git checkout ${API_VERSION}

  docker run --rm --interactive --tty \
    --volume $PWD:/app \
    --user "$(id -u)":"$(id -g)" \
    composer install --no-ansi --no-dev --no-interaction --no-progress --optimize-autoloader --ignore-platform-reqs --no-scripts

  cd ${ROOT_DIR} || exit 1
  cp -R ./build/repo/bin ./artifact/bin
  cp -R ./build/repo/config ./artifact/config
  cp -R ./build/repo/migrations ./artifact/migrations
  cp -R ./build/repo/public ./artifact/public
  cp -R ./build/repo/src ./artifact/src
  cp -R ./build/repo/vendor ./artifact/vendor
  cp ./build/repo/.env ./artifact/.env
  cp ./build/repo/composer.json ./artifact/composer.json
  cp ./build/repo/composer.lock ./artifact/composer.lock
  cp ./build/repo/symfony.lock ./artifact/symfony.lock

  mkdir -p ${ROOT_DIR}/artifact/var/log
  mkdir -p ${ROOT_DIR}/artifact/var/cache
}
