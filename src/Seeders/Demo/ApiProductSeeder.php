<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;
use NinjaPortal\Portal\Models\ApiProduct;
use NinjaPortal\Portal\Models\Audience;
use NinjaPortal\Portal\Models\Category;

class ApiProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'slug' => 'payments-suite',
                'swagger_url' => 'https://specs.ninjaportal.test/payments-suite.yaml',
                'apigee_product_id' => 'payments-suite',
                'visibility' => ApiProduct::$VISIBILITY['public'],
                'categories' => ['payments'],
                'audiences' => ['Retail Developers', 'Partner Integrators'],
                'ar' => [
                    'name' => 'حزمة المدفوعات',
                    'short_description' => 'واجهات برمجة موحدة لقبول المدفوعات والتقاطها وإرجاعها.',
                    'description' => 'قدّم تدفقات بطاقات وأي سي إتش والمحافظ الرقمية من خلال منتج واحد سهل للمطورين.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Payments+Suite',
                ],
                'en' => [
                    'name' => 'Payments Suite',
                    'short_description' => 'Unified APIs for accepting, capturing, and refunding payments.',
                    'description' => 'Expose card, ACH, and digital wallet payment flows through a single developer-friendly product.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Payments+Suite',
                ],
            ],
            [
                'slug' => 'usage-analytics',
                'swagger_url' => 'https://specs.ninjaportal.test/usage-analytics.yaml',
                'apigee_product_id' => 'usage-analytics',
                'visibility' => ApiProduct::$VISIBILITY['public'],
                'categories' => ['analytics'],
                'audiences' => ['Retail Developers', 'Internal Teams'],
                'ar' => [
                    'name' => 'تحليلات الاستخدام',
                    'short_description' => 'اجمع مقاييس الأداء والاعتماد لواجهاتك البرمجية.',
                    'description' => 'وفّر لوحات معلومات وقنوات تنبيه باستخدام نقاط نهاية تحليلية ووصلات ويب جاهزة للأحداث.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Usage+Analytics',
                ],
                'en' => [
                    'name' => 'Usage Analytics',
                    'short_description' => 'Collect performance and adoption metrics for your APIs.',
                    'description' => 'Ship dashboards and alerting pipelines using pre-built analytics endpoints and event webhooks.',
                    'thumbnail' => 'https://placehold.co/600x400?text=Usage+Analytics',
                ],
            ],
            [
                'slug' => 'kyc-compliance',
                'swagger_url' => 'https://specs.ninjaportal.test/kyc-compliance.yaml',
                'apigee_product_id' => 'kyc-compliance',
                'visibility' => ApiProduct::$VISIBILITY['private'],
                'categories' => ['compliance', 'analytics'],
                'audiences' => ['Partner Integrators'],

                'ar' => [
                    'name' => 'امتثال اعرف عميلك',
                    'short_description' => 'تحقق من العملاء وافحص المعاملات تلقائيًا.',
                    'description' => 'ادمج فحوصات الإعداد، وفحص قوائم العقوبات، وتوليد ملفات التدقيق ضمن منتج امتثال جاهز.',
                    'thumbnail' => 'https://placehold.co/600x400?text=KYC+Compliance',
                ],
                'en' => [
                    'name' => 'KYC Compliance',
                    'short_description' => 'Verify customers and screen transactions automatically.',
                    'description' => 'Integrate onboarding checks, sanctions screening, and audit exports with a hardened compliance product.',
                    'thumbnail' => 'https://placehold.co/600x400?text=KYC+Compliance',
                ],
            ],
        ];

        foreach ($products as $data) {
            $categories = $data['categories'] ?? [];
            $audiences = $data['audiences'] ?? [];
            $slug = $data['slug'];

            // Remove categories and audiences from data before filling
            unset($data['categories'], $data['audiences']);

            $apiProduct = ApiProduct::firstOrNew(['slug' => $slug]);
            $apiProduct->fill($data);
            $apiProduct->save();

            if (! empty($categories)) {
                $categoryIds = Category::whereIn('slug', $categories)->pluck('id')->all();
                $apiProduct->categories()->sync($categoryIds);
            }

            if (! empty($audiences)) {
                $audienceIds = Audience::whereIn('name', $audiences)->pluck('id')->all();
                $apiProduct->audiences()->sync($audienceIds);
            }
        }
    }
}
