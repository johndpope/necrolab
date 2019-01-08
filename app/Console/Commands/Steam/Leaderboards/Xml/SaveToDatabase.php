<?php

namespace App\Console\Commands\Steam\Leaderboards\Xml;

use DateTime;
use Illuminate\Console\Command;
use App\Components\DataManagers\Steam\Leaderboards\Xml as XmlManager;
use App\Jobs\Leaderboards\SaveToDatabase as SaveToDatabaseJob;

class SaveToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:xml:database:save {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves Steam leaderboard XML data to the database for the specified date. Defaults to today's date when none is specified.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $data_manager = new XmlManager(new DateTime($this->option('date')));
    
        SaveToDatabaseJob::dispatch($data_manager)->onConnection('sync');
    }
}
