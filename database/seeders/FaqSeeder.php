<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $about = FaqCategory::create([
            'name' => 'About the Platform',
            'order' => 0,
        ]);

        FaqItem::create([
            'faq_category_id' => $about->id,
            'question' => 'What is Check This Out?',
            'answer' => 'Check This Out is an entertainment platform for movies, series, anime, and games.',
            'order' => 0,
        ]);

        FaqItem::create([
            'faq_category_id' => $about->id,
            'question' => 'Where do the title details come from?',
            'answer' => 'Title information is gathered from supported APIs and curated by the community.',
            'order' => 1,
        ]);

        FaqItem::create([
            'faq_category_id' => $about->id,
            'question' => 'Can I submit new titles?',
            'answer' => 'Yes. Registered users can submit titles for review before they are published.',
            'order' => 2,
        ]);

        $account = FaqCategory::create([
            'name' => 'Account & Profile',
            'order' => 1,
        ]);

        FaqItem::create([
            'faq_category_id' => $account->id,
            'question' => 'How do I update my profile?',
            'answer' => 'Open your profile settings to change your username, bio, birthday, and avatar.',
            'order' => 0,
        ]);

        FaqItem::create([
            'faq_category_id' => $account->id,
            'question' => 'Can I manage notification preferences?',
            'answer' => 'Yes. You can control notifications from your account settings.',
            'order' => 1,
        ]);
    }
}
