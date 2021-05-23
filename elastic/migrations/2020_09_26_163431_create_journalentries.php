<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateJournalentries implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('journalentries');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('journalentries');
    }
}
