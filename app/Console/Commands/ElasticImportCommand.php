<?php
namespace App\Console\Commands;


use Elasticsearch\Client;
use Illuminate\Console\Command;

class ElasticImportCommand extends \Laravel\Scout\Console\ImportCommand
{
    protected $signature = 'scout:import
    {model : Class name of model to bulk import}
    {--c|chunk= : The number of records to import at a time (Defaults to configuration value: `scout.chunk.searchable`)}';

    protected $description = 'run elastic migrate';

    public function __construct()
    {
        parent::__construct();
    }

    // public function handle(Client $client)
    // {

    // }
}
