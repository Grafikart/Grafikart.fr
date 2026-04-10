<?php

namespace Database\Seeders;

use App\Domains\Attachment\Attachment;
use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use App\Domains\Comment\Comment;
use App\Domains\Coupon\Coupon;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use App\Domains\Course\Technology;
use App\Domains\Forum\Topic;
use App\Domains\Forum\TopicMessage;
use App\Domains\Forum\TopicTag;
use App\Domains\History\Progress;
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
        Comment::class,
        Plan::class,
        Transaction::class,
        Technology::class,
        Course::class,
        Formation::class,
        Path::class,
        PathNode::class,
        Coupon::class,
        Attachment::class,
        TopicMessage::class,
        TopicTag::class,
        Topic::class,
        Progress::class,
    ];

    protected function clean()
    {
        // Optimize the performance for insertion
        DB::disableQueryLog();
        Schema::disableForeignKeyConstraints();

        DB::table('course_technology')->truncate();
        DB::table('formation_technology')->truncate();
        DB::table('path_node_links')->truncate();
        DB::table('forum_tag_topic')->truncate();

        // Clean the database
        foreach ($this->models as $modelCls) {
            $model = new $modelCls;
            assert($model instanceof Model);
            DB::table($model->getTable())->truncate();
        }
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->clean();
        // Fill it with fake data
        User::factory()->create([
            'name' => 'Grafikart',
            'email' => 'john@doe.fr',
        ]);
        $users = User::factory(10)->create();
        Coupon::factory(10)->create();
        Plan::factory(3)->create();
        $categories = BlogCategory::factory(10)->create();
        $posts = Post::factory(10)
            ->recycle($categories)
            ->create();
        Comment::factory(10)
            ->recycle($users)
            ->recycle($posts)
            ->create();
        Transaction::factory(10)
            ->recycle($users)
            ->create();
        $technologies = Technology::factory(10)->create()->all();
        Course::factory(10)
            ->withTechnologies(3, $technologies)
            ->create();
        Formation::factory(10)
            ->withTechnologies(3, $technologies)
            ->create();
        $paths = Path::factory(3)->create();
        $paths->each(function (Path $path) {
            $nodes = PathNode::factory(rand(5, 10))->create(['path_id' => $path->id]);
            $nodes->skip(1)->each(function (PathNode $node) use ($nodes) {
                $node->parents()->attach(
                    $nodes->where('id', '<', $node->id)->random()
                );
            });
        });

        // Reset settings
        DB::enableQueryLog();
        Schema::enableForeignKeyConstraints();
    }
}
