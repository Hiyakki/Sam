<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Services\AppointmentExportService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function getTabs(): array
    {
        return [
            'today' => Tab::make('Hari Ini')
                ->icon('heroicon-m-sun')
                ->badge(fn () => Appointment::whereDate('appointment_date', today())->active()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('appointment_date', today())),

            'upcoming' => Tab::make('Mendatang')
                ->icon('heroicon-m-calendar')
                ->badge(fn () => Appointment::whereDate('appointment_date', '>', today())->active()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereDate('appointment_date', '>', today())
                    ->whereIn('status', ['pending', 'confirmed'])),

            'pending' => Tab::make('Menunggu Konfirmasi')
                ->icon('heroicon-m-clock')
                ->badge(fn () => Appointment::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'all' => Tab::make('Semua'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Ekspor Data Janji Temu')
                ->modalDescription('Unduh data janji temu dalam format Excel (.xlsx) atau CSV dengan filter kustom.')
                ->modalSubmitActionLabel('Unduh Berkas')
                ->form([
                    Radio::make('period_by')
                        ->label('Periode berdasarkan')
                        ->options([
                            'appointment_date' => 'Tanggal janji',
                            'created_at' => 'Tanggal dibuat',
                        ])
                        ->default('appointment_date')
                        ->live()
                        ->required(),

                    Select::make('date_range')
                        ->label('Rentang waktu')
                        ->options([
                            'all' => 'Semua waktu',
                            'today' => 'Hari ini',
                            'this_week' => 'Minggu ini',
                            'this_month' => 'Bulan ini',
                            'last_month' => 'Bulan lalu',
                            'last_3_months' => '3 bulan terakhir',
                            'custom' => 'Kustom',
                        ])
                        ->default('all')
                        ->live()
                        ->required(),

                    Grid::make(2)
                        ->schema([
                            DatePicker::make('date_from')
                                ->label('Dari tanggal')
                                ->live()
                                ->required(fn (Get $get) => $get('date_range') === 'custom'),

                            DatePicker::make('date_to')
                                ->label('Sampai tanggal')
                                ->live()
                                ->required(fn (Get $get) => $get('date_range') === 'custom')
                                ->afterOrEqual('date_from')
                                ->validationMessages([
                                    'after_or_equal' => 'Sampai tanggal harus sama dengan atau setelah dari tanggal.',
                                ]),
                        ])
                        ->visible(fn (Get $get) => $get('date_range') === 'custom'),

                    CheckboxList::make('statuses')
                        ->label('Status')
                        ->options([
                            'pending' => 'Menunggu',
                            'confirmed' => 'Dikonfirmasi',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            'no_show' => 'Tidak Hadir',
                        ])
                        ->default(['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])
                        ->columns(2)
                        ->bulkToggleable()
                        ->live()
                        ->required()
                        ->minItems(1)
                        ->validationMessages([
                            'required' => 'Minimal satu status wajib dipilih.',
                            'min' => 'Minimal satu status wajib dipilih.',
                        ]),

                    Select::make('branch_ids')
                        ->label('Kantor')
                        ->placeholder('Semua Kantor')
                        ->multiple()
                        ->options(Branch::pluck('name', 'id'))
                        ->live()
                        ->searchable(),

                    Select::make('service_ids')
                        ->label('Layanan')
                        ->placeholder('Semua Layanan')
                        ->multiple()
                        ->options(Service::pluck('name', 'id'))
                        ->live()
                        ->searchable(),

                    Toggle::make('include_complaints')
                        ->label('Sertakan keluhan pasien')
                        ->helperText('Data kesehatan/keluhan pasien bersifat rahasia. Aktifkan hanya jika diperlukan untuk keperluan klinis.')
                        ->default(false),

                    Radio::make('format')
                        ->label('Format berkas')
                        ->options([
                            'xlsx' => 'Excel (.xlsx, default)',
                            'csv' => 'CSV (.csv)',
                        ])
                        ->default('xlsx')
                        ->required(),

                    Placeholder::make('estimated_count')
                        ->label('Perkiraan Jumlah Baris')
                        ->content(function (Get $get, AppointmentExportService $service) {
                            $count = $service->countForFilters([
                                'period_by' => $get('period_by') ?? 'appointment_date',
                                'date_range' => $get('date_range') ?? 'all',
                                'date_from' => $get('date_from'),
                                'date_to' => $get('date_to'),
                                'statuses' => $get('statuses') ?? [],
                                'branch_ids' => $get('branch_ids') ?? [],
                                'service_ids' => $get('service_ids') ?? [],
                            ]);

                            return $count > 0
                                ? "{$count} baris data ditemukan"
                                : '0 baris (Tidak ada data pada kriteria ini)';
                        }),
                ])
                ->action(function (array $data, AppointmentExportService $exportService) {
                    $count = $exportService->countForFilters($data);
                    if ($count === 0) {
                        Notification::make()
                            ->title('Tidak ada data pada filter ini')
                            ->warning()
                            ->send();

                        return null;
                    }

                    return $exportService->export($data, $data['format'] ?? 'xlsx');
                }),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'today';
    }
}
