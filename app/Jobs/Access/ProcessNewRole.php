<?php

namespace App\Jobs\Access;

use App\Models\Access\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNewRole implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $roleId;


    public function __construct(string $roleId)
    {
        $this->roleId = $roleId;
    }

    public function handle(): void
    {
        $role = Role::find($this->roleId);

        sleep(2);


        if (!$role) {
            Log::warning("Role dengan ID {$this->roleId} tidak ditemukan.");
            return;
        }

        Log::info("Memproses role baru dengan ID: {$role->id}");
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Gagal memproses role ID {$this->roleId}: " . $exception->getMessage());
    }
}
