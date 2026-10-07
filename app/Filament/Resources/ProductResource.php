<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?int $navigationSort = 1;

    /**
     * Opsi kategori dikelompokkan per induk: "Orthosis" => [id => "Upper Limb Orthosis", ...].
     */
    public static function categoryOptions(): array
    {
        return ProductCategory::query()
            ->roots()
            ->with('children')
            ->get()
            ->mapWithKeys(fn (ProductCategory $root) => $root->children->isEmpty()
                ? [$root->id => $root->name]
                : [$root->name => $root->children->pluck('name', 'id')->all()])
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Informasi Produk')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Produk')
                        ->required()
                        ->maxLength(150)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create'
                            ? $set('slug', Str::slug((string) $state))
                            : null),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->alphaDash()
                        ->unique(ignoreRecord: true)
                        ->maxLength(170),

                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->options(fn () => self::categoryOptions())
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('function')
                        ->label('Fungsi')
                        ->maxLength(255)
                        ->placeholder('Contoh: Fiksasi dan Koreksi'),

                    Forms\Components\Textarea::make('indication')
                        ->label('Indikasi')
                        ->rows(3),

                    Forms\Components\RichEditor::make('description')
                        ->label('Deskripsi')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'undo', 'redo']),
                ]),

                Forms\Components\Section::make('Galeri Foto Tambahan')->schema([
                    Forms\Components\Repeater::make('images')
                        ->label('')
                        ->relationship('images')
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->label('Foto')
                                ->image()
                                ->disk('public')
                                ->directory('products/gallery')
                                ->imageResizeMode('contain')
                                ->imageResizeTargetWidth('1200')
                                ->imageResizeTargetHeight('1200')
                                ->maxSize(4096)
                                ->required(),
                        ])
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->grid(3)
                        ->defaultItems(0)
                        ->addActionLabel('Tambah foto'),
                ])->collapsible(),
            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Foto Utama')->schema([
                    Forms\Components\FileUpload::make('photo')
                        ->label('')
                        ->image()
                        ->disk('public')
                        ->directory('products')
                        ->imageEditor()
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1200')
                        ->imageResizeTargetHeight('1200')
                        ->maxSize(4096)
                        ->helperText('JPG/PNG/WebP, maks 4 MB. Otomatis diperkecil ke 1200px.'),
                ]),

                Forms\Components\Section::make('Status')->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Tampil di katalog')
                        ->default(true),

                    Forms\Components\TextInput::make('price')
                        ->label('Harga (opsional)')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->helperText('Hanya catatan internal. Harga tidak ditampilkan di website.'),
                ]),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(null),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record) => $record->function),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn () => self::categoryOptions()),
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-m-eye')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-m-eye-slash')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
