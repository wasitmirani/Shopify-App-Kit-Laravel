<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AppIcon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use function config;
use function file_get_contents;
use function info;
use function str_ends_with;

class CacheIconsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will cache all svg icons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Icon caching started.');
        AppIcon::where('type', 'default_icons')->chunk(100, static function ($icons) {
            info('Chunk of 100');
            foreach ($icons as $icon) {
                if (str_ends_with(@$icon['url'], 'svg')) {
                    $path = config('app.cloudfront_icon_host') . '/' . $icon['type'] . '/' . $icon['category'] . '/' . $icon['name'];
                    info('Path', [$path]);

                    Cache::rememberForever($path, static fn () => file_get_contents($path));
                }
            }
        });
        $this->info('Icon caching completed.');
    }
}
