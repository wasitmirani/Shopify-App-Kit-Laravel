<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Integration;
use Illuminate\Database\Seeder;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [[
            'logo' => 'cartx_logo.png',
            'title' => 'Cart X ‑ Post Purchase Upsell',
            'description' => 'One Click Upsell, Reconverting Post Purchase, Popup, In-Cart',
            'rate' => 5.0,
            'review_count' => 119,
            'plan_availability_text' => 'Free Plan available',
            'link' => 'https://apps.shopify.com/cart-x?medium=iconito',
        ], [
            'logo' => 'pixelio-logo.png',
            'title' => 'Pixelio Multi Facebook Pixels',
            'description' => 'Track & sync multiple Facebook pixel, Add & manage backups',
            'rate' => 5.0,
            'review_count' => 312,
            'plan_availability_text' => '7-day free trial',
            'link' => 'https://apps.shopify.com/pixelio-multi-fbpixel?medium=iconito',
        ], [
            'logo' => 'EasySell.png',
            'title' => 'EasySell ‑ COD Form & Upsells',
            'description' => 'Add a simple COD order form and boost your conversion rate and AOV with Upsells and quantity offers.',
            'rate' => 5.0,
            'review_count' => 297,
            'plan_availability_text' => 'Free',
            'link' => 'https://apps.shopify.com/easy-order-form?from=RoyalApps&utm_campaign=crossPromote&utm_medium=in-app&utm_source=Iconito',
        ], [
            'logo' => 'VS-logo.jpg',
            'title' => 'VS Ali Reviews Product Reviews',
            'description' => 'The best spread Shopify reviews app. Build your store the most effective marketing material! Boost conversion rate, organic traffic, and buyer engagement.',
            'rate' => 5.0,
            'review_count' => 537,
            'plan_availability_text' => 'Free',
            'link' => 'https://apps.shopify.com/sealapps-product-review?from=Iconitohome&utm_campaign=crossPromote&utm_medium=Home&utm_source=IconitoTrustbadgesicons',
        ], [
            'logo' => 'countdown-timer-logo.png',
            'title' => 'Essential Countdown Timer Bar',
            'description' => 'Drive Sales by using Urgency. Countdown timer bar is one of the best ways to motivate buyers to act.',
            'rate' => 5.0,
            'review_count' => 235,
            'plan_availability_text' => '7-day free trial',
            'link' => 'https://apps.shopify.com/essential-countdown-timer?surface_detail=Iconito&surface_type=Promotion',
        ], [
            'logo' => 'parcel_panel-logo.png',
            'title' => 'Parcel Panel Order Tracking',
            'description' => 'Auto sync, tracking & update, branded tracking page, shipping notifications, upsell, 24/7 live chat help',
            'rate' => 5.0,
            'review_count' => 890,
            'plan_availability_text' => '7-day free trial',
            'link' => 'https://apps.shopify.com/parcelpanel?from=Iconito&utm_source=Iconito&utm_medium=integrationpage&utm_campaign=crossPromote',
        ]];

        foreach ($integrations as $integration) {
            Integration::updateOrCreate([
                'title' => $integration['title'],
            ], [
                'logo' => $integration['logo'],
                'description' => $integration['description'],
                'rate' => $integration['rate'],
                'review_count' => $integration['review_count'],
                'plan_availability_text' => $integration['plan_availability_text'],
                'link' => $integration['link'],
            ]);
        }
    }
}
