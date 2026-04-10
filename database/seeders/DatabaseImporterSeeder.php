<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

/**
 * Seed the database using data from the previous site.
 */
class DatabaseImporterSeeder extends DatabaseSeeder
{
    const CHUNK_SIZE = 200;

    public function run(): void
    {
        $this->clean();
        $this->migrateUsers();
        $this->migrateBadges();
        $this->migrateAttachments();
        $this->migrateFormations();
        $this->migrateCourses();
        $this->migrateProgress();
        $this->migrateTechnologies();
        $this->migrateBlog();
        //        $this->migrateComments();
        //        $this->migrateForum();
        $this->migratePremium();
        $this->migrateTransactions();
    }

    private function migrateUsers(): void
    {
        $this->startMigration('users');

        DB::table('old_user')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($users) {
                $data = [];
                foreach ($users as $user) {
                    $emailVerifiedAt = null;
                    if ($user->confirmation_token === null) {
                        $emailVerifiedAt = $user->created_at;
                    }

                    $data[] = [
                        'id' => $user->id,
                        'name' => $user->username,
                        'email' => $user->email,
                        'email_verified_at' => $emailVerifiedAt,
                        'password' => $user->password,
                        'premium_end_at' => $user->premium_end,
                        'country' => $user->country ?? 'FR',
                        'notifications_read_at' => $user->notifications_read_at ?? now(),
                        'discord_id' => $user->discord_id,
                        'github_id' => $user->github_id,
                        'google_id' => $user->google_id,
                        'stripe_id' => $user->stripe_id,
                        'facebook_id' => $user->facebook_id,
                        'theme' => $user->theme,
                        'html5_player' => $user->html5_player,
                        'last_login_at' => $user->last_login_at,
                        'last_login_ip' => $user->last_login_ip,
                        'deleted_at' => $user->delete_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                        'invoice_info' => $user->invoice_info,
                    ];
                }
                if (! empty($data)) {
                    DB::table('users')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migrateAttachments(): void
    {
        $this->startMigration('attachments');

        $attachments = DB::table('old_attachment')->get();
        foreach ($attachments as $attachment) {
            DB::table('attachments')->upsert([
                'id' => $attachment->id,
                'name' => $attachment->file_name,
                'size' => $attachment->file_size,
                'created_at' => $attachment->created_at,
            ], uniqueBy: ['id']);
        }
    }

    private function migrateBadges(): void
    {
        $this->startMigration('badges');

        DB::table('old_badge')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($badges) {
                $data = [];
                foreach ($badges as $badge) {
                    $data[] = [
                        'id' => $badge->id,
                        'name' => $badge->name,
                        'description' => $badge->description,
                        'position' => $badge->position,
                        'action' => $badge->action,
                        'action_count' => $badge->action_count,
                        'theme' => $badge->theme,
                        'image' => $badge->image,
                        'unlockable' => $badge->unlockable,
                        'created_at' => $badge->updated_at,
                        'updated_at' => $badge->updated_at,
                    ];
                }

                if (! empty($data)) {
                    DB::table('badges')->upsert($data, uniqueBy: ['id']);
                }
            });

        DB::table('old_badge_unlock')
            ->select('badge_id', 'owner_id')
            ->selectRaw('MIN(created_at) as created_at')
            ->groupBy('badge_id', 'owner_id')
            ->orderBy('badge_id')
            ->orderBy('owner_id')
            ->chunk(self::CHUNK_SIZE, function ($unlocks) {
                $data = [];

                foreach ($unlocks as $unlock) {
                    $data[] = [
                        'badge_id' => $unlock->badge_id,
                        'user_id' => $unlock->owner_id,
                        'created_at' => $unlock->created_at,
                    ];
                }

                if (! empty($data)) {
                    DB::table('badge_user')->upsert($data, uniqueBy: ['badge_id', 'user_id']);
                }
            });
    }

    private function migrateFormations(): void
    {
        $this->startMigration('formations');

        DB::table('old_formation')
            ->join('old_content', 'old_formation.id', '=', 'old_content.id')
            ->orderBy('old_formation.id')
            ->chunk(self::CHUNK_SIZE, function ($formations) {
                $data = [];
                foreach ($formations as $formation) {
                    $data[] = [
                        'id' => $formation->id,
                        'title' => $formation->title,
                        'slug' => $formation->slug,
                        'content' => $formation->content,
                        'online' => $formation->online,
                        'attachment_id' => $formation->attachment_id,
                        'short' => $formation->short,
                        'chapters' => str_replace('modules', 'ids', $formation->chapters),
                        'youtube_playlist' => $formation->youtube_playlist,
                        'links' => $formation->links,
                        'level' => $formation->level,
                        'deprecated_by_id' => $formation->deprecated_by_id,
                        'force_redirect' => $formation->force_redirect,
                        'created_at' => $formation->created_at,
                        'updated_at' => $formation->updated_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('formations')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migrateCourses(): void
    {
        $this->startMigration('courses');

        DB::table('old_course')
            ->join('old_content', 'old_course.id', '=', 'old_content.id')
            ->orderBy('old_course.id')
            ->chunk(self::CHUNK_SIZE, function ($courses) {
                $data = [];
                foreach ($courses as $course) {
                    $data[] = [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $course->slug,
                        'content' => $course->content,
                        'online' => $course->online,
                        'attachment_id' => $course->attachment_id,
                        'youtube_thumbnail_id' => $course->youtube_thumbnail_id,
                        'deprecated_by_id' => $course->deprecated_by_id,
                        'formation_id' => $course->formation_id,
                        'duration' => $course->duration,
                        'youtube_id' => $course->youtube_id,
                        'video_path' => $course->video_path,
                        'source' => $course->source,
                        'demo' => $course->demo,
                        'premium' => $course->premium,
                        'level' => $course->level,
                        'force_redirect' => $course->force_redirect,
                        'created_at' => $course->created_at,
                        'updated_at' => $course->updated_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('courses')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migrateTechnologies(): void
    {
        $this->startMigration('technologies');

        DB::table('old_technology')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($technologies) {
                $data = [];
                foreach ($technologies as $technology) {
                    $data[] = [
                        'id' => $technology->id,
                        'name' => $technology->name,
                        'slug' => $technology->slug,
                        'content' => $technology->content,
                        'image' => $technology->image,
                        'type' => $technology->type,
                        'created_at' => $technology->updated_at,
                        'updated_at' => $technology->updated_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('technologies')->upsert($data, uniqueBy: ['id']);
                }
            });

        DB::table('old_technology_requirement')
            ->orderBy('technology_source')
            ->chunk(self::CHUNK_SIZE, function ($requirements) {
                $data = [];
                foreach ($requirements as $requirement) {
                    $data[] = [
                        'technology_id' => $requirement->technology_source,
                        'requirement_id' => $requirement->technology_target,
                    ];
                }
                if (! empty($data)) {
                    DB::table('technology_requirement')->upsert($data, uniqueBy: ['technology_id', 'requirement_id']);
                }
            });

        // Link technologies to their content
        $types = ['formation', 'course'];
        foreach ($types as $type) {
            DB::table('old_technology_usage')
                ->join('old_content', 'old_technology_usage.content_id', '=', 'old_content.id')
                ->where('old_content.type', $type)
                ->orderBy('old_technology_usage.content_id')
                ->chunk(self::CHUNK_SIZE, function ($usages) use ($type) {
                    $data = [];
                    foreach ($usages as $usage) {
                        $data[] = [
                            "{$type}_id" => $usage->content_id,
                            'technology_id' => $usage->technology_id,
                            'version' => $usage->version,
                            'primary' => ! $usage->secondary,
                        ];
                    }
                    if (! empty($data)) {
                        DB::table("{$type}_technology")->upsert($data, uniqueBy: ["{$type}_id", 'technology_id']);
                    }
                });
        }
    }

    private function migrateBlog(): void
    {
        $this->startMigration('blog');

        // Migrate blog categories
        DB::table('old_blog_category')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($categories) {
                $data = [];
                foreach ($categories as $category) {
                    $data[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (! empty($data)) {
                    DB::table('blog_categories')->upsert($data, uniqueBy: ['id']);
                }
            });

        // Migrate blog posts (join old_blog_post with old_content)
        DB::table('old_blog_post')
            ->join('old_content', 'old_blog_post.id', '=', 'old_content.id')
            ->orderBy('old_blog_post.id')
            ->whereNotLike('slug', '%dayclic%') // This slug is twice in the old database
            ->chunk(self::CHUNK_SIZE, function ($posts) {
                $data = [];
                foreach ($posts as $post) {
                    $data[] = [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'content' => $post->content,
                        'online' => $post->online,
                        'attachment_id' => $post->attachment_id,
                        'category_id' => $post->category_id,
                        'created_at' => $post->created_at,
                        'updated_at' => $post->updated_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('blog_posts')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migrateComments(): void
    {
        $this->startMigration('comments');

        DB::table('old_comment')
            ->join('old_content', 'old_comment.content_id', '=', 'old_content.id')
            ->select('old_comment.*', 'old_content.type as content_type')
            ->orderBy('old_comment.id')
            ->chunk(self::CHUNK_SIZE, function ($comments) {
                $data = [];
                foreach ($comments as $comment) {
                    $data[] = [
                        'id' => $comment->id,
                        'user_id' => $comment->author_id,
                        'commentable_type' => $comment->content_type,
                        'commentable_id' => $comment->content_id,
                        'email' => $comment->email,
                        'username' => $comment->username,
                        'content' => $comment->content,
                        'ip' => $comment->ip,
                        'created_at' => $comment->created_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('comments')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migrateProgress(): void
    {
        $this->startMigration('progress');

        DB::table('old_progress')
            ->join('old_content', 'old_progress.content_id', '=', 'old_content.id')
            ->select('old_progress.*', 'old_content.type as content_type')
            ->where(function ($query) {
                $query
                    ->where('old_progress.progress', 1000)
                    ->orWhere('old_progress.created_at', '>=', now()->subYear());
            })
            ->orderBy('old_progress.id')
            ->chunk(self::CHUNK_SIZE, function ($progressions) {
                $data = [];
                foreach ($progressions as $progression) {
                    $progressableType = match ($progression->content_type) {
                        'course', 'formation' => $progression->content_type,
                        default => null,
                    };

                    if ($progressableType === null) {
                        continue;
                    }

                    $data[] = [
                        'id' => $progression->id,
                        'user_id' => $progression->author_id,
                        'progressable_type' => $progressableType,
                        'progressable_id' => $progression->content_id,
                        'progress' => $progression->progress,
                        'score' => null,
                        'created_at' => $progression->created_at,
                        'updated_at' => $progression->updated_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('progress')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migratePremium(): void
    {
        $this->startMigration('premium');

        // Migrate plans
        DB::table('old_plan')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($plans) {
                $data = [];
                foreach ($plans as $plan) {
                    $data[] = [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'price' => (int) $plan->price,
                        'duration' => $plan->duration,
                        'stripe_id' => $plan->stripe_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (! empty($data)) {
                    DB::table('plans')->upsert($data, uniqueBy: ['id']);
                }
            });

        // Migrate subscriptions
        DB::table('old_subscription')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($subscriptions) {
                $data = [];
                foreach ($subscriptions as $subscription) {
                    $data[] = [
                        'id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'plan_id' => $subscription->plan_id,
                        'state' => $subscription->state,
                        'next_payment' => $subscription->next_payment,
                        'stripe_id' => $subscription->stripe_id,
                        'created_at' => $subscription->created_at,
                        'updated_at' => $subscription->created_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('subscriptions')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function migrateTransactions(): void
    {
        $this->startMigration('transactions');

        DB::table('old_transaction')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($transactions) {
                $data = [];
                foreach ($transactions as $transaction) {
                    $data[] = [
                        'id' => $transaction->id,
                        'user_id' => $transaction->author_id,
                        'duration' => $transaction->duration,
                        'price' => $this->eurosToCents($transaction->price),
                        'tax' => $this->eurosToCents($transaction->tax),
                        'method' => $transaction->method,
                        'method_id' => $transaction->method_ref,
                        'refunded_at' => $transaction->refunded ? $transaction->created_at : null,
                        'firstname' => $transaction->firstname,
                        'lastname' => $transaction->lastname,
                        'address' => $transaction->address,
                        'city' => $transaction->city,
                        'postal_code' => $transaction->postal_code,
                        'country_code' => $transaction->country_code,
                        'fee' => $this->eurosToCents($transaction->fee),
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->created_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('transactions')->upsert($data, uniqueBy: ['id']);
                }
            });
    }

    private function eurosToCents(float|int|null $amount): int
    {
        return (int) round(((float) $amount) * 100, 0, PHP_ROUND_HALF_UP);
    }

    private function startMigration(string $category): void
    {
        $this->command?->info("Migrating {$category}...");
    }

    private function migrateForum(): void
    {
        $this->startMigration('forum');

        // Migrate forum tags
        DB::table('old_forum_tag')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($tags) {
                $data = [];
                foreach ($tags as $tag) {
                    $data[] = [
                        'id' => $tag->id,
                        'parent_id' => $tag->parent_id,
                        'name' => $tag->name,
                        'color' => $tag->color,
                        'visible' => $tag->visible,
                    ];
                }
                if (! empty($data)) {
                    DB::table('forum_tags')->upsert($data, uniqueBy: ['id']);
                }
            });

        // Migrate forum topics
        DB::table('old_forum_topic')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($topics) {
                $data = [];
                foreach ($topics as $topic) {
                    $data[] = [
                        'id' => $topic->id,
                        'user_id' => $topic->author_id,
                        'name' => $topic->name,
                        'content' => $topic->content,
                        'solved' => $topic->solved,
                        'created_at' => $topic->created_at,
                        'updated_at' => $topic->updated_at,
                        'messages_count' => $topic->message_count,
                    ];
                }
                if (! empty($data)) {
                    DB::table('forum_topics')->upsert($data, uniqueBy: ['id']);
                }
            });

        // Migrate forum messages
        DB::table('old_forum_message')
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($messages) {
                $data = [];
                foreach ($messages as $message) {
                    $data[] = [
                        'id' => $message->id,
                        'topic_id' => $message->topic_id,
                        'user_id' => $message->author_id,
                        'content' => $message->content,
                        'accepted' => $message->accepted,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                    ];
                }
                if (! empty($data)) {
                    DB::table('forum_messages')->upsert($data, uniqueBy: ['id']);
                }
            });

        // Migrate forum tag-topic pivot
        DB::table('old_forum_topic_tag')
            ->orderBy('topic_id')
            ->chunk(self::CHUNK_SIZE, function ($relations) {
                $data = [];
                foreach ($relations as $relation) {
                    $data[] = [
                        'tag_id' => $relation->tag_id,
                        'topic_id' => $relation->topic_id,
                    ];
                }
                if (! empty($data)) {
                    DB::table('forum_tag_topic')->upsert($data, uniqueBy: ['tag_id', 'topic_id']);
                }
            });
    }
}
