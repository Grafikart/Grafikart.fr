#!/bin/bash
SITE_DIR=$(dirname $(dirname $(dirname "$(readlink -f "$0")")))
LOG_DIR=${SITE_DIR}/var/log
goaccess ${LOG_DIR}/access.log -o ${SITE_DIR}/public/report.html --config-file=${SITE_DIR}/tools/goaccess/goaccess.conf > ${LOG_DIR}/goaccess.log 2>&1
# goaccess ${LOG_DIR}/access.log -o ${SITE_DIR}/public/report.html --config-file=${SITE_DIR}/tools/goaccess/goaccess.conf --real-time-html
