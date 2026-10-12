<?php

namespace Tests\Feature;

use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Tests\TestCase;

class BackupConfigurationTest extends TestCase
{
    public function test_backup_configuration_is_correct(): void
    {
        $backupConfig = config('backup.backup');
        $cleanupConfig = config('backup.cleanup');

        $this->assertNotNull($backupConfig, 'Konfigurasi backup harus terpasang.');

        // Backup files source harus include storage/app/private
        $includePaths = $backupConfig['source']['files']['include'];
        $this->assertContains(storage_path('app/private'), $includePaths);

        // Retensi hari harus 14 hari
        $this->assertEquals(14, $cleanupConfig['default_strategy']['keep_all_backups_for_days']);
        $this->assertEquals(14, $cleanupConfig['default_strategy']['keep_daily_backups_for_days']);

        // Notifikasi harus terkonfigurasi untuk kegagalan
        $notifications = config('backup.notifications.notifications');
        $this->assertArrayHasKey(BackupHasFailedNotification::class, $notifications);
        $this->assertArrayHasKey(CleanupHasFailedNotification::class, $notifications);
    }
}
