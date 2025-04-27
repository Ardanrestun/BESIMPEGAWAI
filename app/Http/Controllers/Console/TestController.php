<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Jobs\Access\ProcessNewRole;
use App\Models\Access\Role;


class TestController extends Controller
{
    public function batchTest()
    {
        $roles = [];

        for ($i = 1; $i <= 20; $i++) {
            $roles[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Role ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Role::insert($roles);

        $jobs = collect($roles)->map(function ($role) {
            return new ProcessNewRole($role['id']);
        });

        Bus::batch($jobs)
            ->then(function (Batch $batch) {
                Log::info("Semua job selesai di-batch ID: " . $batch->id);
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::error("Ada job gagal: " . $e->getMessage());
            })
            ->finally(function (Batch $batch) {
                Log::info("Batch selesai (success/gagal) dengan ID: " . $batch->id);
            })
            ->dispatch();

        return response()->json(['message' => 'Batch dispatched!']);
    }
}
