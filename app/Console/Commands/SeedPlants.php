<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedPlants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plants:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed plants in available places';

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
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
