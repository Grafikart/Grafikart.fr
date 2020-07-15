<?php

namespace App\Domain\Contact;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ContactService
{
    private ContactRequestRepository $repository;
    private EntityManagerInterface $em;
    private MailerInterface $mailer;

    public function __construct(
        ContactRequestRepository $repository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function send(ContactData $data, Request $request): void
    {
        $contactRequest = (new ContactRequest())->setRawIp($request->getClientIp());
        $lastRequest = $this->repository->findLastRequestForIp($contactRequest->getIp());
        if ($lastRequest && $lastRequest->getCreatedAt() > new \DateTime('- 1 hour')) {
            throw new TooManyContactException();
        }
        if (null !== $lastRequest) {
            $lastRequest->setCreatedAt(new \DateTime());
        } else {
            $this->em->persist($contactRequest);
        }
        $this->em->flush();
        $message = (new Email())
            ->text($data->content)
            ->subject('Grafikart :: Contact')
            ->from('noreply@grafikart.fr')
            ->replyTo(new Address($data->email, $data->name))
            ->to('contact@grafikart.fr');
        $this->mailer->send($message);
    }
}
