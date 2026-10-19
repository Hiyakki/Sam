<?php

namespace Tests\Feature;

use App\Filament\Resources\AppointmentResource;
use App\Filament\Resources\AppointmentResource\Pages\ListAppointments;
use App\Mail\AppointmentStatusChangedPatientMail;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAppointmentSimplifiedTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Branch $branch;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->branch = Branch::create([
            'name' => 'SAM Bogor',
            'slug' => 'sam-bogor',
            'address' => 'Jl. Bogor 1',
            'phone_wa' => '0811111111',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Konsultasi',
            'slug' => 'konsultasi',
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function makeAppointment(array $overrides = []): Appointment
    {
        return Appointment::create(array_merge([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-20',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => 'pending',
            'patient_name' => 'Rayna',
            'patient_email' => 'rayna@test.com',
            'patient_phone' => '6285747031075',
            'patient_age' => 37,
            'complaint' => 'Kaki sakit',
        ], $overrides));
    }

    public function test_clicking_status_changes_it_and_notifies_patient(): void
    {
        Mail::fake();
        $appointment = $this->makeAppointment();

        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class, ['activeTab' => 'all'])
            ->callTableAction('changeStatus', $appointment, ['status' => 'confirmed'])
            ->assertHasNoTableActionErrors();

        $this->assertSame('confirmed', $appointment->refresh()->status);
        $this->assertNull($appointment->cancelled_by);
        Mail::assertQueued(AppointmentStatusChangedPatientMail::class);
    }

    public function test_cancelling_via_status_records_admin_and_reason(): void
    {
        Mail::fake();
        $appointment = $this->makeAppointment(['status' => 'confirmed']);

        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class, ['activeTab' => 'all'])
            ->callTableAction('changeStatus', $appointment, [
                'status' => 'cancelled',
                'cancellation_reason' => 'Pasien berhalangan',
            ]);

        $appointment->refresh();
        $this->assertSame('cancelled', $appointment->status);
        $this->assertSame('admin', $appointment->cancelled_by);
        $this->assertStringContainsString('Pasien berhalangan', $appointment->admin_notes);
    }

    public function test_patient_modal_opens_whatsapp_and_reschedule_actions(): void
    {
        $appointment = $this->makeAppointment(['status' => 'confirmed']);

        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class, ['activeTab' => 'all'])
            ->mountTableAction('patientDetail', $appointment)
            ->assertSee('Kaki sakit')
            ->call('mountTableAction', 'reschedule')
            ->assertSet('mountedTableActions', ['patientDetail', 'reschedule'])
            ->call('unmountTableAction')
            ->call('mountTableAction', 'whatsapp')
            ->assertSet('mountedTableActions', ['patientDetail', 'whatsapp'])
            ->assertSet('mountedTableActionsData.1.template', 'reminder');
    }

    public function test_patient_data_cannot_be_edited_by_admin(): void
    {
        $appointment = $this->makeAppointment();

        $this->assertFalse(AppointmentResource::canEdit($appointment));
        $this->assertArrayNotHasKey('edit', AppointmentResource::getPages());

        $this->actingAs($this->admin)
            ->get("/kelola-sam/appointments/{$appointment->id}/edit")
            ->assertNotFound();
    }

    public function test_latest_appointment_date_is_listed_first(): void
    {
        $older = $this->makeAppointment(['appointment_date' => '2026-10-08', 'patient_name' => 'Lama']);
        $newer = $this->makeAppointment(['appointment_date' => '2026-10-20', 'patient_name' => 'Baru']);
        $newerLaterSlot = $this->makeAppointment(['appointment_date' => '2026-10-20', 'start_time' => '11:00:00', 'patient_name' => 'Baru Siang']);

        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class, ['activeTab' => 'all'])
            ->assertCanSeeTableRecords([$newerLaterSlot, $newer, $older], inOrder: true);
    }

    public function test_deleted_appointment_is_hidden_from_table_and_export(): void
    {
        $kept = $this->makeAppointment(['patient_name' => 'Tetap']);
        $deleted = $this->makeAppointment(['patient_name' => 'Dihapus']);

        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class, ['activeTab' => 'all'])
            ->callTableAction('delete', $deleted)
            ->assertCanSeeTableRecords([$kept])
            ->assertCanNotSeeTableRecords([$deleted]);

        $this->assertSoftDeleted($deleted);

        $exportQuery = app(AppointmentExportService::class)->queryForFilters(['date_range' => 'all']);
        $this->assertSame([$kept->id], $exportQuery->pluck('id')->all());
    }
}
