<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

use function config;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'STARTER',
                'type' => 'RECURRING',
                'price' => 0.00,
                'interval' => 'EVERY_30_DAYS',
                'capped_amount' => 4.99,
                'terms' => 'Free for the first 5,000 page views',
                'trial_days' => 0,
                'test' => config('shopify-app.has_test_plan'),
                'page_views_threshold' => 5000,
                'max_block_limit' => 1,
                'icon_per_block_limit' => 3,
                'on_install' => false,
                'upload_custom_icons' => false,
                'add_link' => false,
                'trigger_product_tag' => false,
                '3d_icon' => false,
                'live_chat' => true,
                'features' => [
                    [
                        'is_included' => 1,
                        'feature' => 'First 5000 pages views',
                    ], [
                        'is_included' => 1,
                        'feature' => 'One Block of icons',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Max 3 icons per block',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Limited icon library',
                    ], [
                        'is_included' => 0,
                        'feature' => 'Upload your own icons',
                    ], [
                        'is_included' => 0,
                        'feature' => 'Add a link',
                    ], [
                        'is_included' => 0,
                        'feature' => 'Unlimited block of icons',
                    ], [
                        'is_included' => 0,
                        'feature' => 'Unlimited icons per block',
                    ],
                ],
            ], [
                'name' => 'MVP (Recommended)',
                'type' => 'RECURRING',
                'price' => 4.99,
                'interval' => 'EVERY_30_DAYS',
                'capped_amount' => null,
                'terms' => null,
                'trial_days' => 3,
                'test' => config('shopify-app.has_test_plan'),
                'page_views_threshold' => 50000,
                'max_block_limit' => null,
                'icon_per_block_limit' => 1000,
                'on_install' => false,
                'upload_custom_icons' => true,
                'add_link' => true,
                'trigger_product_tag' => false,
                '3d_icon' => true,
                'live_chat' => true,
                'features' => [
                    [
                        'is_included' => 1,
                        'feature' => '50,000 page views per month',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Unlimited block of icons',
                    ], [
                        'is_included' => 1,
                        'feature' => '1000+ icons in library',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Upload your own icons',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Add link',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Live chat',
                    ], [
                        'is_included' => 0,
                        'feature' => 'Trigger with product tag',
                    ],
                ],
            ], [
                'name' => 'VIP',
                'type' => 'RECURRING',
                'price' => 9.99,
                'interval' => 'EVERY_30_DAYS',
                'capped_amount' => null,
                'terms' => null,
                'trial_days' => 3,
                'test' => config('shopify-app.has_test_plan'),
                'page_views_threshold' => null,
                'max_block_limit' => null,
                'icon_per_block_limit' => null,
                'on_install' => false,
                'upload_custom_icons' => true,
                'add_link' => true,
                'trigger_product_tag' => true,
                '3d_icon' => true,
                'live_chat' => true,
                'features' => [
                    [
                        'is_included' => 1,
                        'feature' => 'Unlimited views',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Unlimited features',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Unlimited block of icons',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Add a custom link',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Upload your own icons',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Trigger with product Tag',
                    ], [
                        'is_included' => 1,
                        'feature' => '3D icons',
                    ], [
                        'is_included' => 1,
                        'feature' => 'Live Chat',
                    ],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $_plan = Plan::updateOrCreate([
                'name' => $plan['name'],
            ], [
                'type' => $plan['type'],
                'price' => $plan['price'],
                'interval' => $plan['interval'],
                'capped_amount' => $plan['capped_amount'],
                'terms' => $plan['terms'],
                'trial_days' => $plan['trial_days'],
                'test' => $plan['test'],
                'page_views_threshold' => $plan['page_views_threshold'],
                'max_block_limit' => $plan['max_block_limit'],
                'icon_per_block_limit' => $plan['icon_per_block_limit'],
                'on_install' => $plan['on_install'],
                'upload_custom_icons' => $plan['upload_custom_icons'],
                'add_link' => $plan['add_link'],
                'trigger_product_tag' => $plan['trigger_product_tag'],
                '3d_icon' => $plan['3d_icon'],
                'live_chat' => $plan['live_chat'],
            ]);
            $_plan->plan_features()->delete();
            $_plan->plan_features()->createMany($plan['features']);
        }
    }
}
