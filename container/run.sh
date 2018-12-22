#!/usr/bin/env bash

printenv | sed 's/^\([a-zA-Z0-9_]*\)=\(.*\)$/export \1="\2"/g' > /root/project_env.sh && chmod +x /root/project_env.sh

chmod -R ug+rwx \
    storage \
    bootstrap/cache \
    app/PhotonCms/Dependencies/DynamicModels \
    app/PhotonCms/Dependencies/Logging \
    app/PhotonCms/Dependencies/ModuleExtensions \
    app/PhotonCms/Dependencies/PhotonMigrations \
    database/seeds

php artisan storage:link

supervisord -c /etc/supervisor/supervisord.conf