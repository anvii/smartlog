#!/bin/bash

mkdir -p /var/log/supervisor

cat <<EOF > /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0

; Высокий приоритет - больше worker'ов
[program:consumer-high]
command=php /var/www/artisan smart:consume high
numprocs=5
process_name=%(program_name)s_%(process_num)02d
autostart=true
autorestart=true
priority=100
stdout_logfile=/dev/fd/1
stdout_logfile_maxbytes=0
redirect_stderr=true

; Нормальный приоритет
[program:consumer-normal]
command=php /var/www/artisan smart:consume normal
numprocs=2
process_name=%(program_name)s_%(process_num)02d
autostart=true
autorestart=true
priority=10
stdout_logfile=/dev/fd/1
stdout_logfile_maxbytes=0
redirect_stderr=true
EOF

/usr/bin/supervisord
