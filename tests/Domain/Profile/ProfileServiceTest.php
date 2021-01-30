<?php

namespace App\Tests\Domain\Profile;

use App\Domain\Auth\User;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\Entity\EmailVerification;
use App\Domain\Profile\Event\EmailVerificationEvent;
use App\Domain\Profile\Exception\TooManyEmailChangeException;
use App\Domain\Profile\ProfileService;
use App\Domain\Profile\Repository\EmailVerificationRepository;
use App\Infrastructure\Security\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfileServiceTest extends TestCase
{
    const TOKEN = 'hello';

    /**
     * @var EventDispatcherInterface|MockObject
     */
    private EventDispatcherInterface $dispatcher;

    /**
     * @var EntityManagerInterface|MockObject
     */
    private EntityManagerInterface $em;

    public function getService(?EmailVerification $formerVerification = null)
    {
        $tokenGenerator = $this->getMockBuilder(TokenGeneratorService::class)->getMock();
        $tokenGenerator->expects($this->any())->method('generate')->willReturn(self::TOKEN);
        $repository = $this->getMockBuilder(EmailVerificationRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('findLastForUser')->willReturn($formerVerification);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        return new ProfileService($tokenGenerator, $repository, $this->dispatcher, $this->em);
    }

    public function testNothingHappensWhenNotChangingEmail()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $service = $this->getService();

        $this->em->expects($this->never())->method('persist');
        $this->dispatcher->expects($this->never())->method('dispatch');
        $service->updateProfile($data);
    }

    public function testGenerateEmailChangeVerificationOnChangingEmail()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $service = $this->getService();

        $this->em->expects($this->once())->method('persist')->with(
            $this->callback(function (EmailVerification $entity) use ($data) {
                return $entity->getEmail() === $data->email
                    && self::TOKEN === $entity->getToken();
            })
        );
        $service->updateProfile($data);
    }

    public function testDispatchEventOnEmailChange()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $service = $this->getService();

        $this->em->expects($this->once())->method('persist');
        $this->dispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(EmailVerificationEvent::class));
        $service->updateProfile($data);
    }

    public function testThrowExceptionIfEmailAlreadyChangeLastHour()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $service = $this->getService(
            (new EmailVerification())->setCreatedAt(new \DateTime('-10 minutes'))
        );

        $this->em->expects($this->never())->method('persist');
        $this->dispatcher->expects($this->never())->method('dispatch');
        $this->expectException(TooManyEmailChangeException::class);
        $service->updateProfile($data);
    }

    public function testDeletePreviousEmailVerification()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $oldVerification = (new EmailVerification())->setCreatedAt(new \DateTime('-10 hours'));
        $service = $this->getService($oldVerification);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('remove')->with($oldVerification);
        $service->updateProfile($data);
    }
}
