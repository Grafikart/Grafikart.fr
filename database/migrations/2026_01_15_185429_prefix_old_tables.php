<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables to exclude from renaming (Laravel system tables).
     *
     * @var array<string>
     */
    private array $excludedTables = [
        'migrations',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = $this->getTablesToRename();

        foreach ($tables as $table) {
            Schema::rename($table, 'old_'.$table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = Schema::getTables();

        foreach ($tables as $table) {
            $name = $table['name'];

            if (str_starts_with($name, 'old_')) {
                $originalName = substr($name, 4);
                Schema::rename($name, $originalName);
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getTablesToRename(): array
    {
        $tables = Schema::getTables();

        return collect($tables)
            ->pluck('name')
            ->reject(fn (string $name) => in_array($name, $this->excludedTables))
            ->reject(fn (string $name) => str_starts_with($name, 'old_'))
            ->values()
            ->all();
    }
};
