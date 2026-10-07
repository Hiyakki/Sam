<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchClosureResource\Pages;
use App\Models\BranchClosure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchClosureResource extends Resource
{
    protected static ?string $model = BranchClosure::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Hari Libur';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'Hari Libur';

    protected static ?string $pluralModelLabel = 'Hari Libur';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name')
                    ->placeholder('Semua cabang')
                    ->helperText('Kosongkan bila libur berlaku untuk semua cabang.')
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->native(false)
                    ->displayFormat('d M Y'),

                Forms\Components\TextInput::make('reason')
                    ->label('Keterangan')
                    ->maxLength(150)
                    ->placeholder('Contoh: Idul Fitri'),

                Forms\Components\Toggle::make('is_closed')
                    ->label('Tutup seharian')
                    ->helperText('Matikan untuk mengatur jam buka khusus pada tanggal ini.')
                    ->default(true)
                    ->live(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TimePicker::make('open_time')
                        ->label('Jam Buka Khusus')
                        ->seconds(false)
                        ->required(fn (Forms\Get $get) => ! $get('is_closed')),

                    Forms\Components\TimePicker::make('close_time')
                        ->label('Jam Tutup Khusus')
                        ->seconds(false)
                        ->after('open_time')
                        ->required(fn (Forms\Get $get) => ! $get('is_closed')),
                ])->visible(fn (Forms\Get $get) => ! $get('is_closed')),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->placeholder('Semua cabang'),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Keterangan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('is_closed')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (BranchClosure $record): string => $record->is_closed
                        ? 'Tutup'
                        : 'Jam khusus '.substr((string) $record->open_time, 0, 5).'–'.substr((string) $record->close_time, 0, 5))
                    ->color(fn (BranchClosure $record): string => $record->is_closed ? 'danger' : 'warning'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Hanya yang akan datang')
                    ->query(fn ($query) => $query->whereDate('date', '>=', today()))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchClosures::route('/'),
            'create' => Pages\CreateBranchClosure::route('/create'),
            'edit' => Pages\EditBranchClosure::route('/{record}/edit'),
        ];
    }
}
