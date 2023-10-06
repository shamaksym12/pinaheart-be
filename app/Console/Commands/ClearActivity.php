<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Activity;

class ClearActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old user activity';

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
        Activity::where('created_at', '<=', now()->subMonths(2))->delete();
    }
}
