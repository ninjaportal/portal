<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;
use NinjaPortal\Portal\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'payments',
                'ar' => [
                    'name' => 'المدفوعات',
                    'short_description' => 'واجهات برمجة تطبيقات لمعالجة وتسوية المدفوعات الرقمية.',
                    'description' => 'استخدم هذا الكتالوج لرمز البطاقات، التقاط المدفوعات، وإدارة المبالغ المستردة عبر القنوات المختلفة.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Payments',
                ],
                'en' => [
                    'name' => 'Payments',
                    'short_description' => 'APIs for processing and reconciling digital payments.',
                    'description' => 'Use this catalog to tokenize cards, capture payments, and manage refunds across channels.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Payments',
                ],
            ],
            [
                'slug' => 'analytics',
                'ar' => [
                    'name' => 'التحليلات',
                    'short_description' => 'واجهات تقارير ورؤى جاهزة.',
                    'description' => 'قدِّم لوحات معلومات ومؤشرات استخدام ومراقبة لاتفاقيات مستوى الخدمة عبر واجهات برمجة تحليلية جاهزة.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Analytics',
                ],
                'en' => [
                    'name' => 'Analytics',
                    'short_description' => 'Insights and reporting endpoints.',
                    'description' => 'Deliver dashboards, usage metrics, and SLA monitoring through ready-made analytics APIs.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Analytics',
                ],
            ],
            [
                'slug' => 'compliance',
                'ar' => [
                    'name' => 'الامتثال',
                    'short_description' => 'تأكد من أتمتة الفحوصات التنظيمية.',
                    'description' => 'أتمت عمليات التحقق من اعرف عميلك، وفحص المعاملات، وجمع أدلة التدقيق بسهولة.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Compliance',
                ],
                'en' => [
                    'name' => 'Compliance',
                    'short_description' => 'Ensure regulatory checks are automated.',
                    'description' => 'Automate KYC verification, transaction screening, and audit evidence collection.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Compliance',
                ],
            ],
        ];

        foreach ($categories as $data) {
            $slug = $data['slug'];

            $category = new Category;
            $category->fill($data);
            $category->save();
        }
    }
}
