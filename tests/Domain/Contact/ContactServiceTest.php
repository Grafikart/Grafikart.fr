<?php

namespace App\Tests\Domain\Contact;

use App\Domain\Contact\ContactData;
use App\Domain\Contact\ContactRequest;
use App\Domain\Contact\ContactRequestRepository;
use App\Domain\Contact\ContactService;
use App\Domain\Contact\TooManyContactException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;

class ContactServiceTest extends TestCase
{
    /**
     * @var MockObject|ContactRequestRepository
     */
    private \PHPUnit\Framework\MockObject\MockObject $repository;

    /**
     * @var MockObject|EntityManagerInterface
     */
    private \PHPUnit\Framework\MockObject\MockObject $em;

    private ContactService $service;

    /**
     * @var MockObject|Mailer
     */
    private MockObject $mailer;

    public function setUp(): void
    {
        $this->repository = $this
            ->getMockBuilder(ContactRequestRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->em = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
        $this->mailer = $this
            ->getMockBuilder(MailerInterface::class)
            ->getMock();
        $this->service = new ContactService($this->repository, $this->em, $this->mailer);
    }

    private function getMockRequest(string $ip = '127.0.0.1'): Request
    {
        /** @var Request|MockObject $request */
        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->any())
            ->method('getClientIp')
            ->willReturn($ip);

        return $request;
    }

    private function fakeData(): ContactData
    {
        $data = new ContactData();
        $data->email = 'john@doe.fr';
        $data->content = 'Fake content';
        $data->name = 'John';

        return $data;
    }

    public function testThrowExceptionIfSpam(): void
    {
        $contactRequest = new ContactRequest();
        $this->repository->expects($this->once())
            ->method('findLastRequestForIp')
            ->with('127.0.0.0')
            ->willReturn($contactRequest);
        $this->expectException(TooManyContactException::class);
        $this->service->send($this->fakeData(), $this->getMockRequest());
    }

    public function testUpdateCreatedAtIfARequestWasAlreadyDone(): void
    {
        $contactRequest = new ContactRequest();
        $date = new \DateTime('-1 day');
        $contactRequest->setCreatedAt($date);
        $this->repository->expects($this->once())
            ->method('findLastRequestForIp')
            ->willReturn($contactRequest);
        $this->em->expects($this->once())->method('flush');
        $this->service->send($this->fakeData(), $this->getMockRequest());
        $this->assertNotEquals($date, $contactRequest->getCreatedAt());
    }

    public function testRecordContactRequest(): void
    {
        $this->repository->expects($this->once())
            ->method('findLastRequestForIp')
            ->willReturn(null);
        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(ContactRequest::class));
        $this->em->expects($this->once())->method('flush');
        $this->service->send($this->fakeData(), $this->getMockRequest());
    }

    public function testSendEmail(): void
    {
        $this->repository->expects($this->once())
            ->method('findLastRequestForIp')
            ->willReturn(null);
        $this->mailer->expects($this->once())->method('send');
        $this->service->send($this->fakeData(), $this->getMockRequest());
    }
}
