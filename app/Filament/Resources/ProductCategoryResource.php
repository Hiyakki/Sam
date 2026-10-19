<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'Kategori Produk';

    protected static ?string $pluralModelLabel = 'Kategori Produk';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Kategori')->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Induk Kategori')
                    ->placeholder('— Kategori utama —')
                    ->helperText('Kosongkan untuk kategori utama (Orthosis, Prosthesis, Komponen). Maksimal 2 level.')
                    ->relationship(
                        'parent',
                        'name',
                        fn (Builder $query, ?ProductCategory $record) => $query
                            ->whereNull('parent_id')
                            ->when($record, fn ($q) => $q->whereKeyNot($record->getKey())),
                    )
                    ->disabled(fn (?ProductCategory $record) => $record?->children()->exists())
                    ->hint(fn (?ProductCategory $record) => $record?->children()->exists()
                        ? 'Kategori ini punya sub-kategori, jadi tetap kategori utama.'
                        : null)
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create'
                        ? $set('slug', Str::slug((string) $state))
                        : null),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->maxLength(120),

                Forms\Components\Textarea::make('intro_text')
                    ->label('Deskripsi Pengantar Kategori (Intro Content)')
                    ->placeholder('Tulis deskripsi mendalam mengenai kategori alat bantu ini (fungsi klinis, jenis-jenis, manfaat mobilitas). Rekomendasi minimal ~100 kata agar halaman katalog informatif dan bernilai tinggi.')
                    ->rows(4)
                    ->helperText('Konten pengantar ini tampil di atas daftar produk kategori untuk mencegah thin content.'),
            ]),

            Forms\Components\Section::make('Pengaturan SEO & Pratinjau Google')
                ->description('Optimasi meta tag untuk mesin pencari. Kosongkan untuk menggunakan formulasi otomatis.')
                ->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta Title')
                        ->placeholder(fn (Forms\Get $get) => ($get('name') ?: 'Nama Kategori').' | Katalog | SAM Orthotic Prosthetic')
                        ->maxLength(70)
                        ->live(debounce: 400)
                        ->helperText('Rekomendasi: 50–60 karakter.'),

                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->placeholder('Deskripsi ringkas kategori untuk cuplikan pencarian Google (120–160 karakter).')
                        ->rows(3)
                        ->maxLength(170)
                        ->live(debounce: 400)
                        ->helperText('Rekomendasi: 120–160 karakter.'),

                    Forms\Components\ViewField::make('google_preview')
                        ->label('Pratinjau Hasil Pencarian Google')
                        ->view('filament.components.google-preview')
                        ->viewData(fn (Forms\Get $get, ?ProductCategory $record) => [
                            'title' => $get('meta_title') ?: (($get('name') ?: 'Kategori').' | Katalog | SAM Orthotic Prosthetic'),
                            'url' => url('/katalog?kategori='.($get('slug') ?: 'slug-kategori')),
                            'description' => $get('meta_description') ?: strip_tags((string) $get('intro_text')),
                        ]),
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->formatStateUsing(fn (ProductCategory $record) => $record->parent_id ? '— '.$record->name : $record->name)
                    ->weight(fn (ProductCategory $record) => $record->parent_id ? null : 'bold'),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Induk')
                    ->placeholder('Kategori utama'),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Produk')
                    ->counts('products'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Induk')
                    ->options(fn () => ProductCategory::roots()->pluck('name', 'id')->all()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, ProductCategory $record) {
                        if ($record->children()->exists() || $record->products()->withTrashed()->exists()) {
                            Notification::make()
                                ->title('Kategori tidak bisa dihapus')
                                ->body('Pindahkan dulu sub-kategori dan produk di dalamnya.')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
