<?php

declare(strict_types=1);

namespace Deployer;

// put your application specific tasks here
desc('Post Deployment Commands');
task('deploy:post_deployment', static function () {
    cd('/var/www/iconito/current');
    run('npm install && npm run build');
});
