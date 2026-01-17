<?php

namespace Database\Seeders;

use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use App\Domains\Course\Course;
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
        Post::class,
        Plan::class,
        Transaction::class,
        Technology::class,
        Course::class,
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
        DB::table('course_technology')->truncate();

        // Fill it with fake data
        User::factory()->create([
            'name' => 'Grafikart',
            'email' => 'john@doe.fr',
        ]);
        $users = User::factory(10)->create();
        Plan::factory(3)->create();
        $categories = BlogCategory::factory(10)->create();
        Post::factory(10)
            ->recycle($categories)
            ->create();
        Transaction::factory(10)
            ->recycle($users)
            ->create();
        $technologies = Technology::factory(10)
            ->create();
        Course::factory(10)
            ->withTechnologies(3)
            ->recycle($technologies)
            ->create();

        // Reset settings
        DB::enableQueryLog();
        Schema::enableForeignKeyConstraints();
    }
}
