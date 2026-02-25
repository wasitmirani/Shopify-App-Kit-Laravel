<?php

declare(strict_types=1);

namespace Deployer;

host('royalapps.staging.iconito.iconito')
    ->set('deploy_path', '/var/www/iconito')
    ->set('remote_user', 'iconito')
    ->set('http_user', 'iconito')
    ->set('cachetool', '/run/php/php8.2-fpm.sock')
;
