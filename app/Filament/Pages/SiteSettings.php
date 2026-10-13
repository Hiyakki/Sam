<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Halaman pengaturan isi situs (Home, About, Kontak, Umum).
 * Disimpan sebagai key-value di tabel site_settings.
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $navigationGroup = 'Website & Konten';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?string $slug = 'pengaturan-situs';

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    /**
     * Nama field form => [key setting, tipe, grup].
     */
    public const FIELDS = [
        'home_hero_title' => ['home.hero_title', 'text', 'home'],
        'home_hero_subtitle' => ['home.hero_subtitle', 'text', 'home'],
        'home_hero_image' => ['home.hero_image', 'json', 'home'],
        'home_cta_text' => ['home.cta_text', 'text', 'home'],
        'home_advantages' => ['home.advantages', 'json', 'home'],

        'about_story' => ['about.story', 'richtext', 'about'],
        'about_vision' => ['about.vision', 'text', 'about'],
        'about_mission' => ['about.mission', 'json', 'about'],
        'about_milestones' => ['about.milestones', 'json', 'about'],
        'about_values' => ['about.values', 'json', 'about'],
        'about_team' => ['about.team', 'json', 'about'],

        'contact_whatsapp' => ['contact.whatsapp', 'text', 'contact'],
        'contact_email' => ['contact.email', 'text', 'contact'],
        'contact_instagram' => ['contact.instagram', 'text', 'contact'],

        'general_site_name' => ['general.site_name', 'text', 'general'],
        'general_privacy_policy' => ['general.privacy_policy', 'richtext', 'general'],
        'general_appointment_max_days' => ['general.appointment_max_days', 'text', 'general'],
        'general_appointment_reschedule_min_hours' => ['general.appointment_reschedule_min_hours', 'text', 'general'],
        'general_slot_buffer_minutes' => ['general.slot_buffer_minutes', 'text', 'general'],
        'general_gallery' => ['general.gallery', 'json', 'general'],

        // Pengaturan SEO
        'seo_home_title' => ['seo.home_title', 'text', 'seo'],
        'seo_home_description' => ['seo.home_description', 'text', 'seo'],
        'seo_about_title' => ['seo.about_title', 'text', 'seo'],
        'seo_about_description' => ['seo.about_description', 'text', 'seo'],
        'seo_catalog_title' => ['seo.catalog_title', 'text', 'seo'],
        'seo_catalog_description' => ['seo.catalog_description', 'text', 'seo'],
        'seo_faq_title' => ['seo.faq_title', 'text', 'seo'],
        'seo_faq_description' => ['seo.faq_description', 'text', 'seo'],
        'seo_testimonials_title' => ['seo.testimonials_title', 'text', 'seo'],
        'seo_testimonials_description' => ['seo.testimonials_description', 'text', 'seo'],
        'seo_appointment_title' => ['seo.appointment_title', 'text', 'seo'],
        'seo_appointment_description' => ['seo.appointment_description', 'text', 'seo'],
        'seo_privacy_title' => ['seo.privacy_title', 'text', 'seo'],
        'seo_privacy_description' => ['seo.privacy_description', 'text', 'seo'],
        'seo_default_og_image' => ['seo.default_og_image', 'text', 'seo'],
    ];

    public function mount(): void
    {
        $settings = SiteSetting::allCached();
        $state = [];

        foreach (self::FIELDS as $field => [$key]) {
            $state[$field] = $settings[$key] ?? null;
        }

        // Misi disimpan sebagai array string; repeater butuh array objek.
        $state['about_mission'] = collect($state['about_mission'] ?? [])
            ->map(fn ($item) => ['text' => is_array($item) ? ($item['text'] ?? '') : $item])
            ->all();

        $this->form->fill($state);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Tabs::make('Pengaturan')->tabs([
                    Forms\Components\Tabs\Tab::make('Home')
                        ->icon('heroicon-m-home')
                        ->schema([
                            Forms\Components\TextInput::make('home_hero_title')
                                ->label('Judul Hero')
                                ->required()
                                ->maxLength(150),
                            Forms\Components\Textarea::make('home_hero_subtitle')
                                ->label('Subjudul Hero')
                                ->rows(3)
                                ->maxLength(400),
                            Forms\Components\FileUpload::make('home_hero_image')
                                ->label('Gambar Hero (Slider)')
                                ->image()
                                ->multiple()
                                ->maxFiles(10)
                                ->disk('public')
                                ->directory('settings')
                                ->imageResizeMode('cover')
                                ->imageResizeTargetWidth('1600')
                                ->maxSize(4096),
                            Forms\Components\TextInput::make('home_cta_text')
                                ->label('Teks Tombol Utama')
                                ->maxLength(50),
                            Forms\Components\Repeater::make('home_advantages')
                                ->label('Keunggulan SAM')
                                ->schema([
                                    Forms\Components\Select::make('icon')
                                        ->label('Ikon')
                                        ->options([
                                            'star' => 'Bintang',
                                            'users' => 'Tim',
                                            'heart' => 'Hati',
                                            'shield' => 'Perisai',
                                            'clock' => 'Jam',
                                            'check' => 'Centang',
                                        ])
                                        ->default('star'),
                                    Forms\Components\TextInput::make('title')->label('Judul')->required()->maxLength(80),
                                    Forms\Components\Textarea::make('desc')->label('Deskripsi')->rows(2)->maxLength(250),
                                ])
                                ->columns(3)
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state) => $state['title'] ?? null)
                                ->addActionLabel('Tambah keunggulan'),
                        ]),

                    Forms\Components\Tabs\Tab::make('About')
                        ->icon('heroicon-m-information-circle')
                        ->schema([
                            Forms\Components\RichEditor::make('about_story')
                                ->label('Cerita SAM')
                                ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo']),
                            Forms\Components\Textarea::make('about_vision')
                                ->label('Visi')
                                ->rows(3),
                            Forms\Components\Repeater::make('about_mission')
                                ->label('Misi')
                                ->simple(Forms\Components\TextInput::make('text')->required()->maxLength(300))
                                ->reorderable()
                                ->addActionLabel('Tambah misi'),
                            Forms\Components\Repeater::make('about_milestones')
                                ->label('Tonggak Perjalanan (Milestones)')
                                ->schema([
                                    Forms\Components\TextInput::make('year')->label('Tahun')->required()->maxLength(10),
                                    Forms\Components\TextInput::make('title')->label('Judul Peristiwa')->required()->maxLength(100),
                                    Forms\Components\Textarea::make('desc')->label('Deskripsi')->rows(2)->maxLength(250),
                                ])
                                ->columns(3)
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state) => ($state['year'] ?? '').' - '.($state['title'] ?? ''))
                                ->addActionLabel('Tambah tonggak perjalanan'),
                            Forms\Components\Repeater::make('about_values')
                                ->label('Nilai & Prinsip Layanan')
                                ->schema([
                                    Forms\Components\TextInput::make('title')->label('Nilai')->required()->maxLength(80),
                                    Forms\Components\Textarea::make('desc')->label('Deskripsi')->rows(2)->maxLength(250),
                                ])
                                ->columns(2)
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state) => $state['title'] ?? null)
                                ->addActionLabel('Tambah nilai layanan'),
                            Forms\Components\Repeater::make('about_team')
                                ->label('Tim')
                                ->schema([
                                    Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(100),
                                    Forms\Components\TextInput::make('role')->label('Jabatan')->maxLength(100),
                                    Forms\Components\FileUpload::make('photo')
                                        ->label('Foto')
                                        ->image()
                                        ->avatar()
                                        ->disk('public')
                                        ->directory('team')
                                        ->imageResizeMode('cover')
                                        ->imageCropAspectRatio('1:1')
                                        ->imageResizeTargetWidth('400')
                                        ->imageResizeTargetHeight('400')
                                        ->maxSize(2048),
                                ])
                                ->columns(3)
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state) => $state['name'] ?? null)
                                ->defaultItems(0)
                                ->addActionLabel('Tambah anggota tim'),
                        ]),

                    Forms\Components\Tabs\Tab::make('Kontak')
                        ->icon('heroicon-m-phone')
                        ->schema([
                            Forms\Components\TextInput::make('contact_whatsapp')
                                ->label('Nomor WhatsApp Utama')
                                ->required()
                                ->tel()
                                ->regex('/^62\d{8,13}$/')
                                ->validationMessages(['regex' => 'Gunakan format 62xxxxxxxxxx (tanpa + atau 0 di depan).'])
                                ->helperText('Format: 62xxxxxxxxxx. Dipakai tombol "Chat WhatsApp" di website.'),
                            Forms\Components\TextInput::make('contact_email')
                                ->label('Email')
                                ->email()
                                ->maxLength(150),
                            Forms\Components\TextInput::make('contact_instagram')
                                ->label('Instagram')
                                ->prefix('@')
                                ->maxLength(60)
                                ->formatStateUsing(fn (?string $state) => ltrim((string) $state, '@'))
                                ->dehydrateStateUsing(fn (?string $state) => $state ? '@'.ltrim($state, '@') : null),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Umum')
                        ->icon('heroicon-m-adjustments-horizontal')
                        ->schema([
                            Forms\Components\TextInput::make('general_site_name')
                                ->label('Nama Situs')
                                ->required()
                                ->maxLength(100),
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('general_appointment_max_days')
                                    ->label('Batas pemesanan (hari ke depan)')
                                    ->numeric()->integer()->minValue(1)->maxValue(365)->required(),
                                Forms\Components\TextInput::make('general_appointment_reschedule_min_hours')
                                    ->label('Minimal ubah/batal janji (jam sebelum)')
                                    ->numeric()->integer()->minValue(0)->maxValue(168)->required(),
                                Forms\Components\TextInput::make('general_slot_buffer_minutes')
                                    ->label('Jeda slot hari ini (menit)')
                                    ->numeric()->integer()->minValue(0)->maxValue(480)->required(),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Galeri')
                        ->icon('heroicon-m-photo')
                        ->schema([
                            Forms\Components\Repeater::make('general_gallery')
                                ->label('Galeri Foto Workshop & Pelayanan')
                                ->schema([
                                    Forms\Components\FileUpload::make('image')
                                        ->label('Foto')
                                        ->image()
                                        ->required()
                                        ->disk('public')
                                        ->directory('gallery')
                                        ->imageResizeMode('cover')
                                        ->imageResizeTargetWidth('800')
                                        ->maxSize(2048),
                                    Forms\Components\TextInput::make('title')
                                        ->label('Judul Foto')
                                        ->required()
                                        ->maxLength(100),
                                    Forms\Components\TextInput::make('alt')
                                        ->label('Teks Alternatif (SEO/Aksesibilitas)')
                                        ->required()
                                        ->maxLength(150),
                                ])
                                ->columns(3)
                                ->reorderable()
                                ->collapsible()
                                ->maxItems(10)
                                ->itemLabel(fn (array $state) => $state['title'] ?? null)
                                ->addActionLabel('Tambah Foto Galeri'),
                        ]),

                    Forms\Components\Tabs\Tab::make('Kebijakan Privasi')
                        ->icon('heroicon-m-shield-check')
                        ->schema([
                            Forms\Components\RichEditor::make('general_privacy_policy')
                                ->label('Isi Kebijakan Privasi')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo']),
                        ]),

                    Forms\Components\Tabs\Tab::make('SEO & Metadata')
                        ->icon('heroicon-m-magnifying-glass')
                        ->schema([
                            Forms\Components\Section::make('Meta Tag Beranda (Home)')
                                ->description('Judul dan ringkasan pencarian untuk halaman utama.')
                                ->schema([
                                    Forms\Components\TextInput::make('seo_home_title')
                                        ->label('Meta Title Home')
                                        ->placeholder('Kaki Palsu, Tangan Palsu & Orthosis | SAM Orthotic Prosthetic')
                                        ->maxLength(70)
                                        ->helperText('Rekomendasi 50–60 karakter.'),
                                    Forms\Components\Textarea::make('seo_home_description')
                                        ->label('Meta Description Home')
                                        ->placeholder('Layanan pembuatan kaki palsu, tangan palsu, dan penyangga tubuh (orthosis) presisi kustom sejak 2014.')
                                        ->rows(2)
                                        ->maxLength(170)
                                        ->helperText('Rekomendasi 120–160 karakter tanpa klaim berlebihan.'),
                                ])->columns(1),

                            Forms\Components\Section::make('Meta Tag Halaman Statis Lainnya')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('seo_about_title')->label('Title About Us')->maxLength(70),
                                        Forms\Components\Textarea::make('seo_about_description')->label('Description About Us')->rows(2)->maxLength(170),

                                        Forms\Components\TextInput::make('seo_catalog_title')->label('Title Katalog')->maxLength(70),
                                        Forms\Components\Textarea::make('seo_catalog_description')->label('Description Katalog')->rows(2)->maxLength(170),

                                        Forms\Components\TextInput::make('seo_faq_title')->label('Title FAQ')->maxLength(70),
                                        Forms\Components\Textarea::make('seo_faq_description')->label('Description FAQ')->rows(2)->maxLength(170),

                                        Forms\Components\TextInput::make('seo_testimonials_title')->label('Title Testimoni')->maxLength(70),
                                        Forms\Components\Textarea::make('seo_testimonials_description')->label('Description Testimoni')->rows(2)->maxLength(170),

                                        Forms\Components\TextInput::make('seo_appointment_title')->label('Title Form Janji Temu')->maxLength(70),
                                        Forms\Components\Textarea::make('seo_appointment_description')->label('Description Form Janji Temu')->rows(2)->maxLength(170),

                                        Forms\Components\TextInput::make('seo_privacy_title')->label('Title Kebijakan Privasi')->maxLength(70),
                                        Forms\Components\Textarea::make('seo_privacy_description')->label('Description Kebijakan Privasi')->rows(2)->maxLength(170),
                                    ]),
                                ])->collapsible(),

                            Forms\Components\Section::make('Default Open Graph Image')
                                ->description('Gambar default ketika link dibagikan ke WhatsApp, Facebook, atau Twitter jika halaman tidak memiliki gambar khusus (1200x630 px).')
                                ->schema([
                                    Forms\Components\FileUpload::make('seo_default_og_image')
                                        ->label('Default OG Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('seo')
                                        ->imageResizeMode('contain')
                                        ->imageResizeTargetWidth('1200')
                                        ->imageResizeTargetHeight('630')
                                        ->maxSize(3072)
                                        ->helperText('Rekomendasi ukuran: 1200 x 630 pixel.'),
                                ]),
                        ]),
                ])->persistTabInQueryString(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $state['about_mission'] = collect($state['about_mission'] ?? [])
            ->pluck('text')
            ->filter()
            ->values()
            ->all();

        foreach (self::FIELDS as $field => [$key, $type, $group]) {
            $value = $state[$field] ?? null;

            if ($type === 'json') {
                SiteSetting::set($key, array_values($value ?? []), 'json', $group);
            } else {
                SiteSetting::set($key, (string) ($value ?? ''), $type, $group);
            }
        }

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }
}
