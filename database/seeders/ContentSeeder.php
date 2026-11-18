<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\ContentPage;
use App\Models\GalleryItem;
use App\Models\HomepageSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        ContentPage::firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Delight Afriq',
                'excerpt' => 'African heritage, contemporary elegance and premium craftsmanship.',
                'body' => "Delight Afriq celebrates the intersection of African tradition and modern luxury fashion, creating pieces that honor cultural heritage while embracing contemporary design.\n\nOur services include bespoke design, ready-to-wear pieces, statement accessories and personal consultation. Every piece is created for women and men who value beauty, confidence and detail.\n\nFrom discovery to delivery, your journey with us is personal. Browse the collections, inquire through WhatsApp, and let our team help you create a look that is entirely your own.",
                'meta_title' => 'About Delight Afriq | African Heritage, Contemporary Elegance',
                'meta_description' => 'Learn about Delight Afriq, a fashion brand focused on bespoke elegance, ready-to-wear confidence, and premium African-inspired style.',
                'is_published' => true,
                'published_at' => now(),
            ],
        );

        ContentPage::firstOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Delight Afriq',
                'excerpt' => 'Fast inquiries, direct support and easy bookings.',
                'body' => "Reach out for custom orders, style consultations, or any questions about our collections. The fastest way to inquire is WhatsApp.\n\nYou can also call us for fittings, appointments, urgent inquiries and style consultations, or follow our work on Instagram for the latest styles and inspiration.\n\nVisit us at Silverbird Gallery, Abuja, Nigeria.",
                'meta_title' => 'Contact Delight Afriq | Start Your Inquiry',
                'meta_description' => 'Contact Delight Afriq through WhatsApp, Instagram, or a quick inquiry for custom orders, styling consultations, and fashion questions.',
                'is_published' => true,
                'published_at' => now(),
            ],
        );

        $banners = [
            [
                'title' => 'Statement pieces, made for you',
                'subtitle' => 'Bespoke elegance and ready-to-wear confidence from Abuja to everywhere.',
                'image_path' => 'media/images/Custom Statement Gown.png',
                'sort_order' => 1,
            ],
            [
                'title' => 'Modern heritage, everyday',
                'subtitle' => 'Ankara-inspired finishing touches for complete looks.',
                'image_path' => 'media/images/Modern Heritage Set.png',
                'sort_order' => 2,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(
                ['title' => $banner['title']],
                array_merge($banner, [
                    'cta_label' => 'Explore the collection',
                    'cta_url' => route('catalogue.index'),
                    'is_active' => true,
                ]),
            );
        }

        $sections = [
            [
                'key' => 'heritage',
                'title' => 'Rooted in heritage, styled for today',
                'body' => 'Our collections draw on African texture, colour and craft while keeping every silhouette wearable and contemporary. Each design is made to move with you, from everyday moments to milestone celebrations.',
                'sort_order' => 1,
            ],
            [
                'key' => 'craft',
                'title' => 'Craft that carries the story',
                'body' => 'From fabric selection to final fitting, our team focuses on detail. We offer bespoke design, ready-to-wear pieces, statement accessories and personal consultation so every client finds a look that feels unmistakably theirs.',
                'sort_order' => 2,
            ],
            [
                'key' => 'concierge',
                'title' => 'A personal styling concierge',
                'body' => 'Need help with sizing, a custom order or delivery? Message us directly on WhatsApp and our team will guide you through availability, fittings and next steps.',
                'sort_order' => 3,
            ],
        ];

        foreach ($sections as $section) {
            HomepageSection::firstOrCreate(
                ['key' => $section['key']],
                array_merge($section, ['is_active' => true]),
            );
        }

        $gallery = [
            ['title' => 'Custom statement gown', 'image_path' => 'media/images/Custom Statement Gown.png', 'category' => 'Bespoke', 'sort_order' => 1],
            ['title' => 'Modern heritage set', 'image_path' => 'media/images/Modern Heritage Set.png', 'category' => 'Ready-to-wear', 'sort_order' => 2],
            ['title' => 'Ankara bag', 'image_path' => 'media/images/Ankara bag.png', 'category' => 'Accessories', 'sort_order' => 3],
            ['title' => 'Ankara shoe pair', 'image_path' => 'media/images/Ankara shoe pair.png', 'category' => 'Accessories', 'sort_order' => 4],
            ['title' => 'Men ankara', 'image_path' => 'media/images/men ankara.png', 'category' => 'Ready-to-wear', 'sort_order' => 5],
            ['title' => 'Delight studio', 'image_path' => 'media/images/Delight CEO.jpeg', 'category' => 'Studio', 'sort_order' => 6],
        ];

        foreach ($gallery as $item) {
            GalleryItem::firstOrCreate(
                ['title' => $item['title']],
                array_merge($item, ['is_active' => true]),
            );
        }
    }
}
