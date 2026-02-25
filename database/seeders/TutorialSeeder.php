<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tutorial;
use Illuminate\Database\Seeder;

class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tutorials = [[
            'title' => 'Trust Badges and icons activation and onboarding',
            'description' => 'How to setup Trust Badges and icons on Shopify using Iconito app ?',
            'thumbnail' => 'thumb1.png',
            'link' => 'https://www.youtube.com/embed/g3CbN5FIs-U',
        ], [
            'title' => 'Iconito Activate Theme 2.0 extension',
            'description' => 'How to Activate Theme 2.0 extension on iconito',
            'thumbnail' => 'thumb2.png',
            'link' => 'https://www.youtube.com/embed/iBLguBwm0Po',
        ], [
            'title' => 'Manual Placement on iconito',
            'description' => 'How to use Manual Placement on iconito for BEGINNERS',
            'thumbnail' => 'thumb3.png',
            'link' => 'https://www.youtube.com/embed/3dNbz7J3AUU',
        ],
        ];

        foreach ($tutorials as $tutorial) {
            Tutorial::updateOrCreate([
                'title' => $tutorial['title'],
            ], [
                'description' => $tutorial['description'],
                'thumbnail' => $tutorial['thumbnail'],
                'link' => $tutorial['link'],
            ]);
        }
    }
}
