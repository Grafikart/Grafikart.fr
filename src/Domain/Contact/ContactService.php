<?php

namespace App\Domain\Contact;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ContactService
{
    public function __construct(
        private readonly ContactRequestRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function send(ContactData $data, Request $request): void
    {
        $contactRequest = (new ContactRequest())->setRawIp($request->getClientIp());
        $lastRequest = $this->repository->findLastRequestForIp($contactRequest->getIp());
        if ($lastRequest && $lastRequest->getCreatedAt() > new \DateTimeImmutable('- 1 hour')) {
            throw new TooManyContactException();
        }
        if (null !== $lastRequest) {
            $lastRequest->setCreatedAt(new \DateTimeImmutable());
        } else {
            $this->em->persist($contactRequest);
        }
        $this->em->flush();
        $message = (new Email())
            ->text($data->content)
            ->subject("Grafikart::Contact : {$data->name}")
            ->from('noreply@grafikart.fr')
            ->replyTo(new Address($data->email, $data->name))
            ->to('contact@grafikart.fr');
        $this->mailer->send($message);
    }
}
