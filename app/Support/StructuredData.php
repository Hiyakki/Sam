<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class StructuredData
{
    protected array $settings;

    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? SiteSetting::allCached();
    }

    public static function make(?array $settings = null): self
    {
        return new self($settings);
    }

    /**
     * Skema Organization / MedicalBusiness untuk Home dan About.
     */
    public function organization(): array
    {
        $siteName = $this->settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';
        $siteIg = $this->settings['contact.instagram'] ?? null;
        $sameAs = [];

        if (filled($siteIg)) {
            $sameAs[] = 'https://instagram.com/'.ltrim($siteIg, '@');
        }

        $schema = [
            '@type' => 'MedicalBusiness',
            '@id' => url('/').'#organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => url('images/logo-sam.png'),
            'image' => url('images/logo-sam.png'),
            'description' => 'Pusat pembuatan dan perancangan alat bantu gerak tubuh ortotik prostetik kustom presisi tinggi sejak 2014.',
            'foundingDate' => '2014',
            'medicalSpecialty' => ['Orthotics', 'Prosthetics'],
        ];

        $wa = $this->settings['contact.whatsapp'] ?? null;
        if (filled($wa)) {
            $cleanWa = preg_replace('/[^0-9]/', '', $wa) ?? '';
            $schema['telephone'] = str_starts_with($cleanWa, '0') ? ('+62'.substr($cleanWa, 1)) : ('+'.$cleanWa);
        }

        $email = $this->settings['contact.email'] ?? null;
        if (filled($email)) {
            $schema['email'] = $email;
        }

        if (! empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        return $schema;
    }

    /**
     * Skema WebSite dengan potentialAction SearchAction (Home).
     */
    public function webSite(): array
    {
        $siteName = $this->settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';

        return [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => $siteName,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/katalog').'?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Skema LocalBusiness per cabang aktif.
     */
    public function localBusiness(Branch $branch): array
    {
        $siteName = $this->settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';

        $schema = [
            '@type' => 'LocalBusiness',
            '@id' => url('/cabang/'.$branch->slug).'#clinic',
            'name' => $siteName.' - Cabang '.$branch->name,
            'url' => url('/cabang/'.$branch->slug),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $branch->address,
                'addressCountry' => 'ID',
            ],
        ];

        if (filled($branch->phone_wa)) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $branch->phone_wa) ?? '';
            $schema['telephone'] = str_starts_with($cleanPhone, '0') ? ('+62'.substr($cleanPhone, 1)) : ('+'.$cleanPhone);
        }

        if (filled($branch->photo)) {
            $schema['image'] = url('storage/'.ltrim($branch->photo, '/'));
        } else {
            $schema['image'] = url('images/logo-sam.png');
        }

        if (filled($branch->maps_link)) {
            $schema['hasMap'] = $branch->maps_link;
        }

        if (isset($branch->latitude, $branch->longitude) && $branch->latitude && $branch->longitude) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $branch->latitude,
                'longitude' => (float) $branch->longitude,
            ];
        }

        // Opening hours specification dari schedules
        $openingHours = $this->formatOpeningHours($branch);
        if (! empty($openingHours)) {
            $schema['openingHoursSpecification'] = $openingHours;
        }

        return $schema;
    }

    /**
     * Format openingHoursSpecification dari schedules (hari tutup dilewati).
     */
    protected function formatOpeningHours(Branch $branch): array
    {
        $schedules = $branch->relationLoaded('schedules')
            ? $branch->schedules
            : $branch->schedules()->get();

        if ($schedules->isEmpty()) {
            return [];
        }

        $dayMap = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            0 => 'Sunday',
        ];

        $specs = [];

        foreach ($schedules as $schedule) {
            if (! $schedule->is_open || empty($schedule->open_time) || empty($schedule->close_time)) {
                continue;
            }

            $dayUrl = $dayMap[$schedule->day_of_week] ?? null;
            if (! $dayUrl) {
                continue;
            }

            $specs[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $dayUrl,
                'opens' => substr((string) $schedule->open_time, 0, 5),
                'closes' => substr((string) $schedule->close_time, 0, 5),
            ];
        }

        return $specs;
    }

    /**
     * Skema BreadcrumbList.
     *
     * @param  array<int, array{name: string, url: string|null}>  $items
     */
    public function breadcrumbs(array $items): ?array
    {
        if (empty($items)) {
            return null;
        }

        $itemList = [];
        $position = 1;

        foreach ($items as $item) {
            $element = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $item['name'],
            ];

            if (! empty($item['url'])) {
                $element['item'] = $item['url'];
            }

            $itemList[] = $element;
            $position++;
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }

    /**
     * Skema FAQPage.
     *
     * @param  Collection<int, Faq>|array<int, Faq>  $faqs
     */
    public function faqPage($faqs): array
    {
        $entities = [];

        foreach ($faqs as $faq) {
            $entities[] = [
                '@type' => 'Question',
                'name' => trim((string) $faq->question),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) $faq->answer)),
                ],
            ];
        }

        return [
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    /**
     * Skema Product untuk detail produk.
     */
    public function product(Product $product): array
    {
        $siteName = $this->settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';

        $images = [];
        if (filled($product->photo)) {
            $images[] = url('storage/'.ltrim($product->photo, '/'));
        }

        if ($product->relationLoaded('images')) {
            foreach ($product->images as $img) {
                if (filled($img->path)) {
                    $images[] = url('storage/'.ltrim($img->path, '/'));
                }
            }
        }

        if (empty($images)) {
            $images[] = url('images/logo-sam.png');
        }

        $desc = filled($product->description)
            ? strip_tags((string) $product->description)
            : ($product->function ? ($product->function.'. '.($product->indication ?? '')) : 'Alat bantu gerak kustom.');

        $schema = [
            '@type' => 'Product',
            '@id' => url('/katalog/'.$product->slug).'#product',
            'name' => $product->name,
            'url' => url('/katalog/'.$product->slug),
            'description' => Seo::cleanSnippet($desc, 300, 50),
            'image' => array_values(array_unique($images)),
            'brand' => [
                '@type' => 'Brand',
                'name' => $siteName,
            ],
        ];

        if ($product->category) {
            $schema['category'] = $product->category->name;
        }

        // Catatan ketat: JANGAN membuat offers/price bila harga tidak ditampilkan ke publik
        // Sesuai panduan produk SAM, harga di website tidak ditampilkan ke publik secara default.

        return $schema;
    }

    /**
     * Render satu atau lebih array skema menjadi tag script JSON-LD valid.
     *
     * @param  array<int, array|null>|array  $schemas
     */
    public function render(array $schemas): string
    {
        $filtered = array_values(array_filter($schemas));

        if (empty($filtered)) {
            return '';
        }

        $payload = [
            '@context' => 'https://schema.org',
            '@graph' => $filtered,
        ];

        $json = json_encode(
            $payload,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $json = str_replace('"@type":"MedicalBusiness"', '"@type": "MedicalBusiness"', $json);

        return '<script type="application/ld+json">'.PHP_EOL.$json.PHP_EOL.'</script>';
    }
}
