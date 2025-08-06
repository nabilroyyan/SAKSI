<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa; // Ganti dengan model Anda jika berbeda
use Carbon\Carbon;

class PruneSoftDeletes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:prune-soft-deletes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted records from the database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to prune soft-deleted records...');

        // Menghitung jumlah data yang akan dihapus
        $count = Siswa::onlyTrashed()->count();

        if ($count === 0) {
            $this->info('No soft-deleted records to prune. All clean!');
            return 0;
        }

        // Minta konfirmasi dari pengguna sebelum menghapus
        if ($this->confirm("You are about to permanently delete {$count} record(s). Are you sure you want to continue?")) {
            
            // Hapus data secara permanen
            Siswa::onlyTrashed()->forceDelete();

            $this->info("Successfully deleted {$count} soft-deleted record(s).");
        } else {
            $this->info('Operation cancelled by user.');
        }
        
        return 0;
    }
}