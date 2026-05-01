<?php

namespace App\Infrastructure\Mailer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Mail\MailServiceProvider as ServiceProvider;
use InvalidArgumentException;
use Symfony\Component\Mime\Crypto\DkimSigner;

/**
 * Swap Laravel Mailer with a custom Mailer to handle DKIM signing (not supported natively)
 */
class MailerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        parent::register();
        $this->app->singleton(DkimSigner::class, function () {
            $privateKey = config('dkim.private_key');
            $selector = config('dkim.selector');
            $domain = config('dkim.domain');
            if (empty($privateKey)) {
                throw new InvalidArgumentException('No private key set.', 1588115551);
            }
            if (! file_exists($privateKey)) {
                throw new InvalidArgumentException('Private key file does not exist.', 1588115609);
            }

            if (empty($selector)) {
                throw new InvalidArgumentException('No selector set.', 1588115373);
            }
            if (empty($domain)) {
                throw new InvalidArgumentException('No domain set.', 1588115434);
            }

            return new DkimSigner(file_get_contents($privateKey), $domain, $selector, [], config('dkim.passphrase'));
        });
    }

    public function registerIlluminateMailer()
    {
        $this->app->singleton('mail.manager', static function (Application $app) {
            if (config('dkim.enabled', true) && config('dkim.domain') !== null) {
                return new MailManager($app);
            }

            return new \Illuminate\Mail\MailManager($app);
        });

        $this->app->bind('mailer', static function (Application $app) {
            return $app->make('mail.manager')->mailer();
        });
    }
}
