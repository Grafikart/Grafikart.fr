<?php

namespace App\Tests\Domain\Profile;

use App\Domain\Auth\User;
use App\Domain\Password\TokenGeneratorService;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\Entity\EmailVerification;
use App\Domain\Profile\Event\EmailVerificationEvent;
use App\Domain\Profile\Exception\TooManyEmailChangeException;
use App\Domain\Profile\ProfileService;
use App\Domain\Profile\Repository\EmailVerificationRepository;
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

    public function getService(?EmailVerification $formerVerification = null)
    {
        $tokenGenerator = $this->getMockBuilder(TokenGeneratorService::class)->getMock();
        $tokenGenerator->expects($this->any())->method('generate')->willReturn(self::TOKEN);
        $repository = $this->getMockBuilder(EmailVerificationRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('findLastForUser')->willReturn($formerVerification);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        return new ProfileService($tokenGenerator, $repository, $this->dispatcher);
    }

    public function testNothingHappensWhenNotChangingEmail()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $service = $this->getService();

        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->never())->method('persist');
        $this->dispatcher->expects($this->never())->method('dispatch');
        $service->updateProfile($data, $em);
    }

    public function testGenerateEmailChangeVerificationOnChangingEmail()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $service = $this->getService();

        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->once())->method('persist')->with(
            $this->callback(function (EmailVerification $entity) use ($data) {
                return $entity->getEmail() === $data->email
                    && self::TOKEN === $entity->getToken();
            })
        );
        $service->updateProfile($data, $em);
    }

    public function testDispatchEventOnEmailChange()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $service = $this->getService();

        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->once())->method('persist');
        $this->dispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(EmailVerificationEvent::class));
        $service->updateProfile($data, $em);
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

        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->never())->method('persist');
        $this->dispatcher->expects($this->never())->method('dispatch');
        $this->expectException(TooManyEmailChangeException::class);
        $service->updateProfile($data, $em);
    }

    public function testDeletePreviousEmailVerification()
    {
        $user = new User();
        $user->setEmail('john@doe.fr');
        $data = new ProfileUpdateDto($user);
        $data->email = 'john2@doe.fr';
        $oldVerification = (new EmailVerification())->setCreatedAt(new \DateTime('-10 hours'));
        $service = $this->getService($oldVerification);

        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('remove')->with($oldVerification);
        $service->updateProfile($data, $em);
    }
}
