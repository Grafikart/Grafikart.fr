<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailer;

use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Mail\SentMessage;
use Symfony\Component\Mime\Crypto\DkimSigner;

class Mailer extends \Illuminate\Mail\Mailer
{
    /**
     * Send a new message using a view.
     *
     * @param  MailableContract|string|array  $view
     * @param  \Closure|string|null  $callback
     * @return SentMessage|null
     */
    public function send($view, array $data = [], $callback = null)
    {
        if ($view instanceof MailableContract) {
            return $this->sendMailable($view);
        }

        $data['mailer'] = $this->name;

        // Once we have retrieved the view content for the e-mail we will set the body
        // of this message using the HTML type, which will provide a simple wrapper
        // to creating view based emails that are able to receive arrays of data.
        [$view, $plain, $raw] = $this->parseView($view);

        $data['message'] = $message = $this->createMessage();

        $this->addContent($message, $view, $plain, $raw, $data);

        if (! is_null($callback)) {
            $callback($message);
        }

        // If a global "to" address has been set, we will set that address on the mail
        // message. This is primarily useful during local development in which each
        // message should be delivered into a single mail address for inspection.
        if (isset($this->to['address'])) {
            $this->setGlobalToAndRemoveCcAndBcc($message);
        }

        // Next we will determine if the message should be sent. We give the developer
        // one final chance to stop this message and then we will send it to all of
        // its recipients. We will then fire the sent event for the sent message.
        $symfonyMessage = $message->getSymfonyMessage();

        // PATCH START
        $mailers = config('dkim.mailers', ['smtp', 'sendmail', 'log', 'mail']);
        if (in_array(strtolower(config('mail.default')), $mailers, true)) {
            $signer = app(DkimSigner::class);
            $signedEmail = $signer->sign($message->getSymfonyMessage());
            $symfonyMessage->setHeaders($signedEmail->getHeaders());
        }
        // PATCH END

        if ($this->shouldSendMessage($symfonyMessage, $data)) {
            $symfonySentMessage = $this->sendSymfonyMessage($symfonyMessage);

            if ($symfonySentMessage) {
                $sentMessage = new SentMessage($symfonySentMessage);

                $this->dispatchSentEvent($sentMessage, $data);

                return $sentMessage;
            }
        }

        return null;
    }
}
