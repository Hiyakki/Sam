<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Cabang';

    protected static ?string $navigationGroup = 'Klinik & Cabang';

    protected static ?string $modelLabel = 'Cabang';

    protected static ?string $pluralModelLabel = 'Cabang';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Cabang')->tabs([

                // === Tab 1: Info Utama ===
                Forms\Components\Tabs\Tab::make('Informasi Cabang')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Cabang')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(120),

                    Forms\Components\Textarea::make('address')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->rows(3),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('phone_wa')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->prefix('62')
                            ->tel()
                            ->placeholder('8123456789')
                            ->helperText('Format: 62xxxxxxxxxx. Isi tanpa awalan 62, awalan otomatis ditambahkan.'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(150),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Cabang Aktif')
                            ->helperText('Cabang tidak aktif tidak muncul di form appointment.')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ]),

                    Forms\Components\FileUpload::make('photo')
                        ->label('Foto Cabang')
                        ->image()
                        ->directory('branches')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('800')
                        ->imageResizeTargetHeight('600'),
                ]),

                // === Tab 2: Peta ===
                Forms\Components\Tabs\Tab::make('Lokasi & Peta')->schema([
                    Forms\Components\TextInput::make('maps_link')
                        ->label('Link Google Maps')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://maps.google.com/...'),

                    Forms\Components\Textarea::make('maps_embed_url')
                        ->label('Kode Embed Google Maps')
                        ->rows(4)
                        ->placeholder('<iframe src="..." ...></iframe>')
                        ->helperText('Salin kode embed dari Google Maps > Bagikan > Sematkan peta.'),
                ]),

                // === Tab 3: Layanan ===
                Forms\Components\Tabs\Tab::make('Layanan Tersedia')->schema([
                    Forms\Components\CheckboxList::make('services')
                        ->label('Layanan yang tersedia di cabang ini')
                        ->relationship('services', 'name')
                        ->helperText('Centang layanan yang tersedia di cabang ini. Hanya layanan yang dicentang yang dapat dipilih saat membuat janji.'),
                ]),

                // === Tab 4: Jadwal Mingguan ===
                Forms\Components\Tabs\Tab::make('Jadwal Mingguan')->schema([
                    Forms\Components\Repeater::make('schedules')
                        ->label('Jadwal Buka per Hari')
                        ->relationship('schedules')
                        ->schema([
                            Forms\Components\Grid::make(4)->schema([
                                Forms\Components\Select::make('day_of_week')
                                    ->label('Hari')
                                    ->options([
                                        1 => 'Senin',
                                        2 => 'Selasa',
                                        3 => 'Rabu',
                                        4 => 'Kamis',
                                        5 => 'Jumat',
                                        6 => 'Sabtu',
                                        7 => 'Minggu',
                                    ])
                                    ->required(),

                                Forms\Components\Toggle::make('is_open')
                                    ->label('Buka')
                                    ->default(true)
                                    ->live(),

                                Forms\Components\TimePicker::make('open_time')
                                    ->label('Jam Buka')
                                    ->seconds(false)
                                    ->visible(fn (Forms\Get $get) => $get('is_open')),

                                Forms\Components\TimePicker::make('close_time')
                                    ->label('Jam Tutup')
                                    ->seconds(false)
                                    ->visible(fn (Forms\Get $get) => $get('is_open')),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('slot_duration_minutes')
                                    ->label('Durasi Slot (menit)')
                                    ->numeric()
                                    ->default(60)
                                    ->minValue(15)
                                    ->visible(fn (Forms\Get $get) => $get('is_open')),

                                Forms\Components\TextInput::make('capacity_per_slot')
                                    ->label('Kapasitas per Slot')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->visible(fn (Forms\Get $get) => $get('is_open')),
                            ]),
                        ])
                        ->orderColumn('day_of_week')
                        ->addable(false)
                        ->deletable(false)
                        ->defaultItems(0)
                        ->helperText('Atur jam buka dan kapasitas janji per hari.'),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->address),
                Tables\Columns\TextColumn::make('phone_wa')
                    ->label('WhatsApp'),
                Tables\Columns\TextColumn::make('services_count')
                    ->label('Layanan')
                    ->counts('services'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}
