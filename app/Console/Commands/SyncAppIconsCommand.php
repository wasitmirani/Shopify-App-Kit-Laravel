<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AppIcon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use function config;
use function explode;

class SyncAppIconsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-app-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will sync aws S3 icons to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Icon syncing Started...');

        $types = ['default_icons', '3d_icons'];
        $icon_host = config('app.aws_icon_host');
        foreach ($types as $type) {
            $icons = [];
            $directories = Storage::disk('s3')->directories($type);
            foreach ($directories as $directory) {
                $files = Storage::disk('s3')->allFiles($directory);
                foreach ($files as $file) {
                    $temp = explode('/', $file);
                    $icons[] = [
                        'type' => $type,
                        'category' => $temp[1],
                        'name' => $temp[2],
                        'url' => $icon_host . '/' . $file,
                    ];
                }
            }
            AppIcon::upsert($icons, ['type', 'category', 'name'], ['url']);
        }
        $this->info('Icon syncing Completed.');
    }
}
