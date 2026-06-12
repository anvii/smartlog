#!/bin/bash

case "$1" in
    producer|consumer|queue)
        docker exec -ti -u andrew -w /var/www smart_${1}_1 bash
    ;;
    broker)
        docker exec -ti -w /opt/kafka/bin smart_${1}_1 bash
    ;;
    *)
        echo "Use: $0 producer|consumer|queue|broker"
    ;;
esac

