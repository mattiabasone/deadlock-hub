#!/usr/bin/env bash

ROOT_DIR=$(pwd)
source ${ROOT_DIR}/common.sh
source ${ROOT_DIR}/variables.sh

create_artifact

docker build . -f Dockerfile-web --target web -t ${CONTAINER_REPOSITORY}:${WEB_CONTAINER_VERSION}  || exit 1
docker build . -f Dockerfile-web --target web -t ${CONTAINER_REPOSITORY}:web-latest  || exit 1
docker push ${CONTAINER_REPOSITORY}:${WEB_CONTAINER_VERSION}
docker push ${CONTAINER_REPOSITORY}:web-latest

docker build . -f Dockerfile-worker --target worker -t ${CONTAINER_REPOSITORY}:${WORKER_CONTAINER_VERSION} || exit 1
docker build . -f Dockerfile-worker --target worker -t ${CONTAINER_REPOSITORY}:worker-latest || exit 1
docker push ${CONTAINER_REPOSITORY}:${WORKER_CONTAINER_VERSION}
docker push ${CONTAINER_REPOSITORY}:worker-latest

rm -rf ${ROOT_DIR}/build/repo
rm -rf ${ROOT_DIR}/build/artifact