<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Branch;
use App\Services\SlotService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Janji Temu';

    protected static ?string $navigationGroup = 'Appointment';

    protected static ?string $modelLabel = 'Janji Temu';

    protected static ?string $pluralModelLabel = 'Janji Temu';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'code';

    public const STATUSES = [
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'no_show' => 'Tidak Hadir',
    ];

    public const STATUS_COLORS = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show' => 'gray',
    ];

    public static function getNavigationBadge(): ?string
    {
        $count = Appointment::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Tindak Lanjut Admin')->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(self::STATUSES)
                    ->required()
                    ->live(),

                Forms\Components\Select::make('cancelled_by')
                    ->label('Dibatalkan oleh')
                    ->options(['patient' => 'Pasien', 'admin' => 'Admin'])
                    ->visible(fn (Forms\Get $get) => $get('status') === 'cancelled')
                    ->required(fn (Forms\Get $get) => $get('status') === 'cancelled'),

                Forms\Components\Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->rows(4)
                    ->maxLength(2000)
                    ->helperText('Hanya terlihat oleh admin.'),
            ]),

            Forms\Components\Section::make('Detail Kunjungan (Jadwal & Lokasi)')->schema([
                Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Kantor Cabang')
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->label('Layanan')
                    ->required(),
                Forms\Components\DatePicker::make('appointment_date')
                    ->label('Tanggal Kunjungan')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Waktu Mulai')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Waktu Selesai')
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Data Pasien')->schema([
                Forms\Components\TextInput::make('patient_name')
                    ->label('Nama Pasien')
                    ->required()
                    ->maxLength(120),
                Forms\Components\TextInput::make('patient_age')
                    ->label('Usia')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('patient_phone')
                    ->label('Telepon / WA')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('patient_email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(150),
                Forms\Components\Textarea::make('complaint')
                    ->label('Keluhan')
                    ->required()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Detail Janji')->schema([
                Infolists\Components\TextEntry::make('code')->label('Kode')->copyable(),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => self::STATUS_COLORS[$state] ?? 'gray'),
                Infolists\Components\TextEntry::make('branch.name')->label('Kantor'),
                Infolists\Components\TextEntry::make('service.name')->label('Layanan'),
                Infolists\Components\TextEntry::make('appointment_date')->label('Tanggal')->date('l, d F Y'),
                Infolists\Components\TextEntry::make('start_time')
                    ->label('Jam')
                    ->formatStateUsing(fn (Appointment $record) => substr((string) $record->start_time, 0, 5).' – '.substr((string) $record->end_time, 0, 5)),
            ])->columns(3),

            Infolists\Components\Section::make('Data Pasien')->schema([
                Infolists\Components\TextEntry::make('patient_name')->label('Nama'),
                Infolists\Components\TextEntry::make('patient_age')->label('Usia')->suffix(' tahun'),
                Infolists\Components\TextEntry::make('patient_phone')->label('Telepon/WA'),
                Infolists\Components\TextEntry::make('patient_email')->label('Email'),
                Infolists\Components\TextEntry::make('complaint')->label('Keluhan')->columnSpanFull(),
                Infolists\Components\TextEntry::make('prescription_path')
                    ->label('Resep Dokter')
                    ->formatStateUsing(fn (?string $state) => $state ? 'Unduh resep' : null)
                    ->placeholder('Tidak ada')
                    ->url(fn (Appointment $record) => $record->prescription_path
                        ? route('filament.kelola-sam.prescription.download', $record)
                        : null)
                    ->openUrlInNewTab(),
            ])->columns(2),

            Infolists\Components\Section::make('Catatan')->schema([
                Infolists\Components\TextEntry::make('admin_notes')->label('Catatan Admin')->placeholder('-'),
                Infolists\Components\TextEntry::make('cancelled_by')
                    ->label('Dibatalkan oleh')
                    ->formatStateUsing(fn (?string $state) => $state === 'patient' ? 'Pasien' : 'Admin')
                    ->visible(fn (Appointment $record) => $record->status === 'cancelled'),
                Infolists\Components\TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('appointment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam')
                    ->formatStateUsing(fn (Appointment $record) => substr((string) $record->start_time, 0, 5)),
                Tables\Columns\TextColumn::make('patient_name')
                    ->label('Pasien')
                    ->description(fn (Appointment $record) => $record->patient_phone)
                    ->searchable(['patient_name', 'patient_phone']),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Kantor')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => self::STATUS_COLORS[$state] ?? 'gray'),
                Tables\Columns\IconColumn::make('prescription_path')
                    ->label('Resep')
                    ->boolean()
                    ->getStateUsing(fn (Appointment $record) => filled($record->prescription_path))
                    ->toggleable(),
            ])
            ->defaultSort(fn (Builder $query) => $query->orderBy('appointment_date')->orderBy('start_time'))
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Kantor')
                    ->relationship('branch', 'name'),
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->relationship('service', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::STATUSES)
                    ->multiple(),
                Tables\Filters\Filter::make('appointment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('appointment_date', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('appointment_date', '<=', $d))),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    self::statusAction('confirm', 'Konfirmasi', 'confirmed', 'heroicon-m-check-circle', 'success', ['pending']),
                    self::rescheduleAction(),
                    self::statusAction('complete', 'Selesai', 'completed', 'heroicon-m-check-badge', 'primary', ['confirmed']),
                    self::statusAction('no_show', 'Tidak Hadir', 'no_show', 'heroicon-m-user-minus', 'gray', ['confirmed']),
                    self::cancelAction(),
                ])->label('Aksi Janji')->icon('heroicon-m-cog-6-tooth')->button()->size('sm'),

                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('template')
                            ->label('Pilihan Template')
                            ->options([
                                'confirm' => 'Template Konfirmasi Jadwal',
                                'reminder' => 'Template Pengingat Kehadiran',
                                'reschedule' => 'Template Penyesuaian Jadwal',
                                'cancel' => 'Template Pembatalan',
                            ])
                            ->default('confirm')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Appointment $record) {
                                $set('message', self::generateTemplateMessage($record, $state));
                            }),
                        Forms\Components\Textarea::make('message')
                            ->label('Isi Pesan WhatsApp (Dapat Diedit)')
                            ->rows(5)
                            ->required()
                            ->default(fn (Appointment $record) => self::generateTemplateMessage($record, 'confirm')),
                    ])
                    ->modalHeading(fn (Appointment $record) => "Kirim WhatsApp ke {$record->patient_name}")
                    ->modalSubmitActionLabel('Buka WhatsApp')
                    ->action(function (Appointment $record, array $data) {
                        $encoded = rawurlencode($data['message']);
                        $url = 'https://wa.me/'.$record->patient_phone.'?text='.$encoded;

                        return redirect()->away($url);
                    }),

                Tables\Actions\Action::make('prescription')
                    ->label('Resep')
                    ->icon('heroicon-m-document-arrow-down')
                    ->visible(fn (Appointment $record) => filled($record->prescription_path))
                    ->url(fn (Appointment $record) => route('filament.kelola-sam.prescription.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->label('Tindak lanjut'),
            ]);
    }

    /**
     * Aksi ubah jadwal janji temu oleh admin dengan validasi SlotService dan DB transaction locking.
     */
    public static function rescheduleAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('reschedule')
            ->label('Ubah Jadwal')
            ->icon('heroicon-m-calendar-days')
            ->color('warning')
            ->visible(fn (Appointment $record) => in_array($record->status, ['pending', 'confirmed'], true))
            ->form([
                Forms\Components\DatePicker::make('appointment_date')
                    ->label('Tanggal Kunjungan Baru')
                    ->required()
                    ->minDate(now('Asia/Jakarta')->toDateString())
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('start_time', null)),
                Forms\Components\Select::make('start_time')
                    ->label('Slot Jam Baru')
                    ->required()
                    ->options(function (Forms\Get $get, Appointment $record) {
                        $date = $get('appointment_date');
                        if (! $date) {
                            return [];
                        }
                        $branch = $record->branch;
                        if (! $branch) {
                            return [];
                        }
                        $slotService = app(SlotService::class);
                        if (! $slotService->isDateBookable($branch, $date)) {
                            return [];
                        }
                        $slots = $slotService->slotsFor($branch, $date);
                        $options = [];
                        foreach ($slots as $slot) {
                            if ($slot['available']) {
                                $options[$slot['start']] = $slot['label'];
                            }
                        }

                        return $options;
                    })
                    ->helperText(function (Forms\Get $get, Appointment $record) {
                        $date = $get('appointment_date');
                        if (! $date) {
                            return 'Pilih tanggal terlebih dahulu untuk melihat slot yang tersedia.';
                        }
                        $branch = $record->branch;
                        if (! $branch) {
                            return null;
                        }
                        $slotService = app(SlotService::class);
                        if (! $slotService->isDateBookable($branch, $date)) {
                            return 'Cabang tutup atau hari libur pada tanggal ini.';
                        }

                        return null;
                    }),
            ])
            ->modalHeading(fn (Appointment $record) => "Ubah Jadwal: {$record->code}")
            ->modalDescription('Pilih tanggal dan slot waktu baru yang masih tersedia.')
            ->modalSubmitActionLabel('Simpan Perubahan Jadwal')
            ->action(function (Appointment $record, array $data) {
                $slotService = app(SlotService::class);

                $result = DB::transaction(function () use ($record, $data, $slotService) {
                    $appointment = Appointment::where('id', $record->id)->lockForUpdate()->first();
                    $branch = Branch::where('id', $appointment->branch_id)->lockForUpdate()->first();

                    if (! $branch || ! $branch->is_active) {
                        return ['success' => false, 'message' => 'Cabang tidak aktif atau tidak ditemukan.'];
                    }

                    $targetDate = Carbon::parse($data['appointment_date'], 'Asia/Jakarta')->startOfDay();
                    if (! $slotService->isDateBookable($branch, $targetDate)) {
                        return ['success' => false, 'message' => 'Tanggal yang dipilih tidak dapat dipesan (tutup atau hari libur).'];
                    }

                    $startTime = substr((string) $data['start_time'], 0, 5);
                    $slots = collect($slotService->slotsFor($branch, $targetDate));
                    $matchingSlot = $slots->firstWhere('start', $startTime);

                    if (! $matchingSlot) {
                        return ['success' => false, 'message' => 'Slot waktu yang dipilih tidak valid.'];
                    }

                    // Kunci janji temu lain pada slot tersebut untuk memastikan kapasitas tidak terlampaui
                    $activeAppointmentsCount = Appointment::where('branch_id', $branch->id)
                        ->where('id', '!=', $appointment->id)
                        ->whereDate('appointment_date', $targetDate->format('Y-m-d'))
                        ->where(function ($q) use ($startTime) {
                            $q->where('start_time', $startTime)
                                ->orWhere('start_time', "{$startTime}:00");
                        })
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->lockForUpdate()
                        ->count();

                    if ($activeAppointmentsCount >= $matchingSlot['capacity']) {
                        return ['success' => false, 'message' => 'Slot waktu tersebut sudah penuh.'];
                    }

                    $oldDateFormatted = $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y') : '-';
                    $oldTimeFormatted = substr((string) $appointment->start_time, 0, 5);
                    $newDateString = $targetDate->format('Y-m-d');
                    $newEndTime = strlen($matchingSlot['end']) === 5 ? "{$matchingSlot['end']}:00" : $matchingSlot['end'];

                    $timestamp = now('Asia/Jakarta')->format('d/m/Y H:i');
                    $logText = "[{$timestamp}] Jadwal diubah oleh Admin dari {$oldDateFormatted} {$oldTimeFormatted} ke {$targetDate->format('d/m/Y')} {$startTime} WIB.";
                    $updatedNotes = trim($appointment->admin_notes ? $appointment->admin_notes."\n".$logText : $logText);

                    $appointment->update([
                        'appointment_date' => $newDateString,
                        'start_time' => "{$startTime}:00",
                        'end_time' => $newEndTime,
                        'admin_notes' => $updatedNotes,
                    ]);

                    return [
                        'success' => true,
                        'appointment' => $appointment,
                        'dateDisplay' => $targetDate->translatedFormat('l, d F Y'),
                        'timeDisplay' => $startTime,
                        'branchName' => $branch->name,
                    ];
                });

                if (! $result['success']) {
                    Notification::make()
                        ->title('Gagal Mengubah Jadwal')
                        ->body($result['message'])
                        ->danger()
                        ->send();

                    return;
                }

                $appt = $result['appointment'];
                $waMessage = "Halo {$appt->patient_name}, jadwal janji temu Anda di {$result['branchName']} (Kode: {$appt->code}) telah disesuaikan menjadi {$result['dateDisplay']} pukul {$result['timeDisplay']} WIB. Silakan konfirmasi jika waktu ini sesuai bagi Anda. Terima kasih!";
                $waUrl = 'https://wa.me/'.$appt->patient_phone.'?text='.rawurlencode($waMessage);

                Notification::make()
                    ->title('Jadwal Berhasil Diubah')
                    ->body("Jadwal pasien {$appt->patient_name} berhasil dialihkan ke {$result['dateDisplay']} {$result['timeDisplay']} WIB. Email notifikasi otomatis dikirim.")
                    ->success()
                    ->actions([
                        NotificationAction::make('send_wa_reschedule')
                            ->label('Kirim WA Penyesuaian Jadwal')
                            ->url($waUrl)
                            ->openUrlInNewTab(),
                    ])
                    ->send();
            });
    }

    /**
     * Aksi pembatalan janji temu oleh admin dengan alasan opsional dan pembebasan slot otomatis.
     */
    public static function cancelAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('cancel')
            ->label('Batalkan')
            ->icon('heroicon-m-x-circle')
            ->color('danger')
            ->visible(fn (Appointment $record) => in_array($record->status, ['pending', 'confirmed'], true))
            ->requiresConfirmation()
            ->modalHeading(fn (Appointment $record) => "Batalkan Janji Temu: {$record->code}")
            ->modalDescription('Slot akan otomatis dibebaskan dan email pembatalan dikirim ke pasien.')
            ->form([
                Forms\Components\Textarea::make('cancellation_reason')
                    ->label('Alasan Pembatalan (Opsional)')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Contoh: Pasien berhalangan / Dokter berhalangan hadir'),
            ])
            ->modalSubmitActionLabel('Ya, Batalkan Janji')
            ->action(function (Appointment $record, array $data) {
                $reason = trim($data['cancellation_reason'] ?? '');
                $timestamp = now('Asia/Jakarta')->format('d/m/Y H:i');
                $logText = "[{$timestamp}] Dibatalkan oleh Admin".($reason !== '' ? ": {$reason}" : '.');
                $updatedNotes = trim($record->admin_notes ? $record->admin_notes."\n".$logText : $logText);

                $record->update([
                    'status' => 'cancelled',
                    'cancelled_by' => 'admin',
                    'admin_notes' => $updatedNotes,
                ]);

                $branchName = $record->branch?->name ?? 'SAM Orthotic Prosthetic';
                $waMessage = "Halo {$record->patient_name}, janji temu Anda di {$branchName} (Kode: {$record->code}) telah dibatalkan oleh pihak admin.".($reason !== '' ? " Alasan: {$reason}." : '').' Silakan hubungi kami kembali jika ingin membuat janji temu baru. Terima kasih.';
                $waUrl = 'https://wa.me/'.$record->patient_phone.'?text='.rawurlencode($waMessage);

                Notification::make()
                    ->title('Janji Temu Dibatalkan')
                    ->body('Slot janji temu telah otomatis dikosongkan. Email pembatalan telah dikirim ke pasien.')
                    ->warning()
                    ->actions([
                        NotificationAction::make('send_wa_cancel')
                            ->label('Kirim WA Pembatalan')
                            ->url($waUrl)
                            ->openUrlInNewTab(),
                    ])
                    ->send();
            });
    }

    /**
     * Aksi cepat ubah status dari tabel.
     */
    protected static function statusAction(string $name, string $label, string $status, string $icon, string $color, array $allowedFrom): Tables\Actions\Action
    {
        return Tables\Actions\Action::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->modalHeading("{$label} janji ini?")
            ->visible(fn (Appointment $record) => in_array($record->status, $allowedFrom, true))
            ->action(function (Appointment $record) use ($status, $label) {
                $record->update([
                    'status' => $status,
                    'cancelled_by' => $status === 'cancelled' ? 'admin' : $record->cancelled_by,
                ]);

                Notification::make()->title("Status diubah: {$label}")->success()->send();
            });
    }

    /**
     * Template pesan WhatsApp dinamis yang dapat dipilih dan diedit oleh admin.
     */
    public static function generateTemplateMessage(Appointment $record, string $type = 'confirm'): string
    {
        $date = $record->appointment_date?->translatedFormat('l, d F Y') ?? '-';
        $time = substr((string) $record->start_time, 0, 5);
        $branch = $record->branch?->name ?? 'SAM Orthotic Prosthetic';

        return match ($type) {
            'reminder' => "Halo {$record->patient_name}, kami mengingatkan kembali jadwal janji temu Anda di {$branch} pada {$date} pukul {$time} WIB. Mohon hadir 10 menit lebih awal. Terima kasih!",
            'reschedule' => "Halo {$record->patient_name}, mengenai janji temu Anda di {$branch} (Kode: {$record->code}), jadwal kunjungan Anda disesuaikan menjadi {$date} pukul {$time} WIB. Mohon konfirmasi kesediaan waktu Anda. Terima kasih!",
            'cancel' => "Halo {$record->patient_name}, janji temu Anda di {$branch} (Kode: {$record->code}) telah dibatalkan. Silakan hubungi kami jika Anda memerlukan jadwal konsultasi pengganti. Terima kasih!",
            default => "Halo {$record->patient_name}, janji temu Anda di {$branch} pada {$date} pukul {$time} WIB telah kami konfirmasi. Silakan datang sesuai jadwal.",
        };
    }

    /**
     * Link wa.me ke pasien dengan pesan template sesuai status.
     */
    public static function whatsAppUrl(Appointment $record): string
    {
        $message = self::generateTemplateMessage($record, $record->status === 'confirmed' ? 'reminder' : 'confirm');

        return 'https://wa.me/'.$record->patient_phone.'?text='.rawurlencode($message);
    }

    public static function canCreate(): bool
    {
        // Janji dibuat oleh pasien lewat form publik.
        return false;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'patient_name', 'patient_phone'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
