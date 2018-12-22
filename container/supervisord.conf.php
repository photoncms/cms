[supervisord]
logfile=/var/log/supervisord/supervisord.log    ; supervisord log file
logfile_maxbytes=50MB                           ; maximum size of logfile before rotation
logfile_backups=10                              ; number of backed up logfiles
loglevel=info                                   ; info, debug, warn, trace
pidfile=/var/run/supervisord.pid                ; pidfile location
nodaemon=true                                   ; run supervisord as a daemon
minfds=1024                                     ; number of startup file descriptors
minprocs=200                                    ; number of process descriptors
user=root                                       ; default user
childlogdir=/var/log/supervisord/               ; where child log files will live

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[supervisorctl]
serverurl=unix:///run/supervisor.sock         ; use a unix:// URL  for a unix socket

[program:php-fpm]
command=/usr/local/sbin/php-fpm -F
autostart=true
autorestart=true
priority=9
stdout_events_enabled=true
stderr_events_enabled=true

[program:nginx]
command=/usr/sbin/nginx
autostart=true
autorestart=true
priority=10
stdout_events_enabled=true
stderr_events_enabled=true

[program:queue-listener]
command=/usr/local/bin/php /var/www/artisan queue:listen --timeout=0
autostart=true
autorestart=true
priority=11
stdout_events_enabled=true
stderr_events_enabled=true

[program:cron]
command=/usr/sbin/cron -f
autostart=true
autorestart=true
priority=12