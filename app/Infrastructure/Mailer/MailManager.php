<?php

namespace App\Infrastructure\Mailer;

class MailManager extends \Illuminate\Mail\MailManager
{
    /**
     * Resolve the given mailer.
     */
    public function build($config): Mailer
    {
        // Resolve our own mailer instead of the Laravel one
        $mailer = new Mailer(
            $config['name'] ?? 'ondemand',
            $this->app['view'],
            $this->createSymfonyTransport($config),
            $this->app['events']
        );

        if ($this->app->bound('queue')) {
            $mailer->setQueue($this->app['queue']);
        }

        return $mailer;
    }
}
