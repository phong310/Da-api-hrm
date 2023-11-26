<?php

namespace App\Console\Commands;

use Database\Seeders\UpdatePermissionsSeeder;
use Illuminate\Console\Command;

class SeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-permission-seeder {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command update permission seeder by company-id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(UpdatePermissionsSeeder $updatePermissionSeeder)
    {
        $company_id = $this->argument('company_id');
        $updatePermissionSeeder->run($company_id);
    }
}
