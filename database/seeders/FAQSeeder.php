<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [[
            'question' => 'HOW TO INSTALL?',
            'answer' => 'The installation is fully automatic - you only need to choose block position (product page, footer, cart or custom placement), select your icons, add titles, customize colors & fonts and publish. Normally, you will be able to see the effect in a few seconds.',
        ], [
            'question' => 'How many icons am I allowed to use with my plan?',
            'answer' => 'You are allowed to use all the icons you want in the library. You can also import your own if you want.',
        ], [
            'question' => 'Is the app working with all theme on Shopify?',
            'answer' => 'The app was tested with all Shopify Free themes. But for most other themes it should still work as expected due to our automatic installation. If you have any issue, please contact us.',
        ], [
            'question' => 'Do I need to add any code to my theme?',
            'answer' => 'No, you don’t need to add any code to your theme if you’re using automatic placement.',
        ], [
            'question' => 'I want to cancel my subscription, what should I do ?',
            'answer' => 'We are sorry to see you go, but if you wish to cancel your subscription, please just remove the app from your store, and it will be automatically cancelled. You won’t be charged anything after that moment. The billing is handled by Shopify, and this is the way it works.',
        ], [
            'question' => 'Do I need to do anything after removing this app?',
            'answer' => 'No, you don’t need to do anything after removing the app. After being removed, everything will be as if you have never installed the app.',
        ]];

        foreach ($faqs as $faq) {
            FAQ::updateOrCreate([
                'question' => $faq['question'],
            ], [
                'answer' => $faq['answer'],
            ]);
        }
    }
}
