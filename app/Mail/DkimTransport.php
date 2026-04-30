<?php

namespace App\Mail;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;

final class DkimTransport implements TransportInterface
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        private readonly TransportInterface $inner,
        private readonly DkimSigner $signer,
        private readonly array $options = [],
    ) {}

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if ($message instanceof Message) {
            $message = $this->signer->sign($message, $this->options);
        }

        return $this->inner->send($message, $envelope);
    }

    public function __toString(): string
    {
        return 'dkim+'.$this->inner;
    }
}
