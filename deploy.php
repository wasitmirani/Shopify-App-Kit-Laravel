<?php

declare(strict_types=1);

namespace Deployer;


require 'recipe/laravel.php';

require 'vendor/deployer/recipes/recipe/cachetool.php';

require 'vendor/deployer/recipes/recipe/rsync.php';

require __DIR__ . '/deploy/hosts.php';

require __DIR__ . '/deploy/tasks/server.php';

require __DIR__ . '/deploy/tasks/app.php';

require __DIR__ . '/deploy/parameters.php';

/* Parameters */
set('git_tty', true);

add('copy_dirs', ['vendor']);

/* Execution */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'deploy:clear_paths',
    'deploy:publish',
    'deploy:permissions',
    'deploy:post_deployment',
    'cachetool:clear:opcache',
    // Cleanup and finish the deploy
    'deploy:unlock',
    //    'cleanup',
])->desc('Deploy your project');

// after successful deploy
// after('deploy', 'success');

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

after('rollback', 'cachetool:clear:opcache');
