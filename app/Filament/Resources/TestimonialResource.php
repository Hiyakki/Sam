<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Testimoni';

    protected static ?string $navigationGroup = 'Website & Konten';

    protected static ?string $modelLabel = 'Testimoni';

    protected static ?string $pluralModelLabel = 'Testimoni';

    protected static ?int $navigationSort = 2;

    public const CONSENT_MESSAGE = 'Testimoni tidak bisa diaktifkan sebelum izin tampil dikonfirmasi.';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Isi Testimoni')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Gunakan nama samaran/inisial bila pasien tidak ingin namanya dipublikasikan.'),

                Forms\Components\TextInput::make('role_or_info')
                    ->label('Keterangan singkat')
                    ->maxLength(150)
                    ->placeholder('Contoh: Pengguna kaki palsu'),

                Forms\Components\Select::make('product_id')
                    ->label('Produk terkait (opsional)')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('content')
                    ->label('Isi Testimoni')
                    ->required()
                    ->rows(5)
                    ->maxLength(2000),

                Forms\Components\Select::make('rating')
                    ->label('Rating (opsional)')
                    ->options([5 => '★★★★★ (5)', 4 => '★★★★ (4)', 3 => '★★★ (3)', 2 => '★★ (2)', 1 => '★ (1)']),

                Forms\Components\FileUpload::make('photo')
                    ->label('Foto (opsional)')
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('testimonials')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeTargetHeight('400')
                    ->maxSize(2048)
                    ->helperText('Hanya unggah foto bila ada persetujuan tertulis dari pasien.'),
            ])->columns(2),

            Forms\Components\Section::make('Izin & Publikasi')->schema([
                Forms\Components\Checkbox::make('consent_confirmed')
                    ->label('Izin tampil sudah dikonfirmasi')
                    ->helperText('Centang hanya bila pasien sudah memberi izin testimoninya ditampilkan di website.')
                    ->live(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Tampilkan di website')
                    ->default(false)
                    ->rules([
                        fn (Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if ($value && ! $get('consent_confirmed')) {
                                $fail(self::CONSENT_MESSAGE);
                            }
                        },
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->description(fn (Testimonial $record) => $record->role_or_info),
                Tables\Columns\TextColumn::make('content')
                    ->label('Isi')
                    ->limit(60)
                    ->tooltip(fn (Testimonial $record) => $record->content)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (?int $state) => $state ? str_repeat('★', $state) : null)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('consent_confirmed')
                    ->label('Izin')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->disabled(fn (Testimonial $record) => ! $record->consent_confirmed)
                    ->tooltip(fn (Testimonial $record) => $record->consent_confirmed ? null : self::CONSENT_MESSAGE),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TernaryFilter::make('consent_confirmed')->label('Izin Tampil'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make()->label('Hapus permanen'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-m-eye')
                        ->action(function (Collection $records) {
                            $allowed = $records->where('consent_confirmed', true);
                            $allowed->each->update(['is_active' => true]);
                            $skipped = $records->count() - $allowed->count();

                            Notification::make()
                                ->title("{$allowed->count()} testimoni diaktifkan")
                                ->body($skipped > 0 ? "{$skipped} dilewati karena izin tampil belum dikonfirmasi." : null)
                                ->{$skipped > 0 ? 'warning' : 'success'}()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-m-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Hapus permanen'),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
