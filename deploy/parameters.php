<?php

declare(strict_types=1);

namespace Deployer;

set('application', 'Iconito V2');

set('repository', 'git@github.com:Royal-Apps/Iconito-V2.git');
set('branch', 'main');

set('repository_name', 'Royal-Apps/Iconito-V2');

set('writable_use_sudo', true);

set('keep_releases', 5);

set('local_php_path', '/usr/bin/env php8.1');

// set('slack_webhook', 'https://hooks.slack.com/services/S0M3/3XAMP7E');
