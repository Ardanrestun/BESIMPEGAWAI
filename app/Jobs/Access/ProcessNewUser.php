<?php

namespace App\Jobs\Access;


use App\Models\Access\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Contoh logging

class ProcessNewUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Lakukan tugas-tugas yang ingin Anda jalankan untuk user baru di sini
        Log::info('Memproses user baru dengan ID: ' . $this->user->id);

        // Contoh: Kirim email selamat datang
        // \Mail::to($this->user->email)->send(new \App\Mail\WelcomeEmail($this->user));

        // Contoh: Sinkronisasi data ke layanan eksternal
        // \App\Services\ExternalApiService::syncUser($this->user);

        // Contoh lain: Membuat catatan log khusus
        Log::channel('user_activity')->info('Pengguna baru dibuat: ' . $this->user->name . ' (' . $this->user->email . ')');
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // Lakukan tindakan jika job gagal, misalnya log error
        Log::error('Gagal memproses user dengan ID: ' . $this->user->id . ' - ' . $exception->getMessage());
    }
}
