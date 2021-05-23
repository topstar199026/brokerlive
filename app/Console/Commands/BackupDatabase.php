<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sys = env('SYSTEM', 'LINUX');
        if($sys == 'LINUX')
        {
            $filename = "db.gz";

            $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . storage_path() . "/app/backup/" . $filename;

            $returnVar = NULL;
            $output  = NULL;

            exec($command, $output, $returnVar);
        }
        else
        {
            //$filename = "backup-" . Carbon::now()->format('Y-m-d') . ".gz";
            $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";

            //$command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . storage_path() . "/app/backup/" . $filename;
            $command = "mysqldump --user=root --password= brokerlive_2 > D:/" . $filename;

            $returnVar = NULL;
            $output  = NULL;

            exec($command, $output, $returnVar);
        }

        return 0;
    }
}
