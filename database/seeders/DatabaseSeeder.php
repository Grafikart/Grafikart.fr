<?php

namespace Database\Seeders;

use App\Domains\Blog\BlogCategory;
use App\Domains\Course\Models\Technology;
use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    private array $models = [
        User::class,
        BlogCategory::class,
        Plan::class,
        Transaction::class,
        Technology::class,
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Optimize the performance for insertion
        DB::disableQueryLog();
        Schema::disableForeignKeyConstraints();

        // Clean the database
        foreach ($this->models as $modelCls) {
            $model = new $modelCls;
            assert($model instanceof Model);
            DB::table($model->getTable())->truncate();
        }

        // Fill it with fake data
        User::factory()->create([
            'name' => 'Grafikart',
            'email' => 'john@doe.fr',
        ]);
        $users = User::factory(10)->create();
        Plan::factory(3)->create();
        BlogCategory::factory(10)->create();
        Transaction::factory(10)
            ->recycle($users)
            ->create();
        Technology::factory(10)
            ->create();

        // Reset settings
        DB::enableQueryLog();
        Schema::enableForeignKeyConstraints();
    }
}
