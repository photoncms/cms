date.timezone=UTC
display_errors=On
log_errors=On
memory_limit=1024M
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time=120

<?php if (getenv('INSTALL_XDEBUG') == 'true'): ?>
xdebug.remote_enable=1
xdebug.remote_port=9000
<?php if (empty(getenv('XDEBUG_REMOTE_HOST'))): ?>
xdebug.remote_connect_back=1
<?php else: ?>
xdebug.remote_connect_back=0
xdebug.remote_host=<?php echo getenv('XDEBUG_REMOTE_HOST'); ?>

<?php endif; ?>
xdebug.remote_autostart=1
xdebug.var_display_max_depth=10
xdebug.remote_log="/var/www/storage/logs/xdebug.log"
<?php endif; ?>
