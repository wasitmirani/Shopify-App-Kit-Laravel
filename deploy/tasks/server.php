<?php

declare(strict_types=1);

namespace Deployer;

desc('Upload appropriate .env.local file to the server');
task('server:upload_env', static function () {
    $stage = 'dev';
    if (input()->hasArgument('stage')) {
        $stage = input()->getArgument('stage');
    }

    $path = get('deploy_path');
    $localFile = 'deploy/files/.env.local.' . $stage;

    if (!file_exists($localFile)) {
        writeln('<info>.env.local file for selected stage does not exist. Skipping...</info>');

        return;
    }

    if (!test('[ -d ' . $path . '/shared/ ]')) {
        run('mkdir -p ' . $path . '/shared/');
    }

    upload($localFile, $path . '/shared/.env.local');
});

desc('List crontab configuration');
task('crontab:list', static function () {
    $result = run('crontab -l');

    write($result);
});

desc('Deploy permissions');
task('deploy:permissions', static function () {
    run('sudo setfacl -R -b {{release_or_current_path}}');
    run('sudo chown -Rh www-data:www-data {{release_or_current_path}}');
    run('sudo find {{release_or_current_path}} -type d -print0 | xargs -0 sudo chmod ug+rwx');
    run('sudo find {{release_or_current_path}} -type d -print0 | xargs -0 sudo chmod g+s');
    run('sudo find {{release_or_current_path}} -type f -print0 | xargs -0 sudo chmod ug+rw');
});
