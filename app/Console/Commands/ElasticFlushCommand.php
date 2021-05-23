<?php
namespace App\Console\Commands;


use Elasticsearch\Client;
use Illuminate\Console\Command;

class ElasticFlushCommand extends \Laravel\Scout\Console\FlushCommand
{
    protected $signature = 'scout:flush {model}';


    protected $description = 'run elastic migrate';

    public function __construct()
    {
        parent::__construct();
    }

    // public function handle(Client $client)
    // {

    // }
}
