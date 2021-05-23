<?php
namespace App\Console\Commands;


use Elasticsearch\Client;
use Illuminate\Console\Command;

class CreateIndex extends Command
{
    protected $signature = 'create:index {name}';

    protected $description = 'Creates an index';

    public function handle(Client $client)
    {
        $client->indices()->create([
            'index' => $this->argument('name')
        ]);
    }
}
