<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AnonymizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:anonymize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymise users and clear sensitive data for non-production environments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (config('app.env') === 'production') {
            $this->error('Cannot run this command in production!');

            return Command::FAILURE;
        }

        $this->info('Anonymising users...');
        \App\Models\User::query()
            ->where('id', '!=', 1)
            ->update([
                'name' => \DB::raw("CONCAT('user', id)"),
                'email' => \DB::raw("CONCAT('user', id, '@grafikart.fr')"),
            ]);

        \App\Models\User::query()
            ->update([
                'password' => \Hash::make('0000')
            ]);

        $this->info('Anonymising coupons...');
        \App\Domains\Coupon\Coupon::query()->update(['email' => 'user@grafikart.fr']);

        $this->info('Emptying transactions...');
        \App\Domains\Premium\Models\Transaction::query()->truncate();

        $this->info('Data anonymised successfully!');

        return Command::SUCCESS;
    }
}
