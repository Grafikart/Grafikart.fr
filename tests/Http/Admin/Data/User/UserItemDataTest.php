<?php

namespace App\Tests\Http\Admin\Data\User;

use App\Domain\Auth\User;
use App\Http\Admin\Data\User\UserItemData;
use App\Tests\DTOTestCase;

class UserItemDataTest extends DTOTestCase
{
    public function testMapping(): void
    {
        $user = (new User())
            ->setId(1)
            ->setUsername('john_doe')
            ->setEmail('john@example.com')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-15'))
            ->setPremiumEnd(new \DateTimeImmutable('+1 year'))
            ->setBannedAt(new \DateTimeImmutable('2024-06-01'))
            ->setLastLoginIp('192.168.1.1');

        $data = $this->transform($user, UserItemData::class);
        assert($data instanceof UserItemData);

        $this->assertEquals(1, $data->id);
        $this->assertEquals('john_doe', $data->username);
        $this->assertEquals('john@example.com', $data->email);
        $this->assertEquals(new \DateTimeImmutable('2024-01-15'), $data->createdAt);
        $this->assertTrue($data->isPremium);
        $this->assertTrue($data->isBanned);
        $this->assertEquals('192.168.1.1', $data->lastLoginIp);
    }

    public function testMappingNotBanned(): void
    {
        $user = (new User())
            ->setId(2)
            ->setUsername('jane_doe')
            ->setEmail('jane@example.com')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-15'))
            ->setLastLoginIp(null);

        $data = $this->transform($user, UserItemData::class);
        assert($data instanceof UserItemData);

        $this->assertFalse($data->isPremium);
        $this->assertFalse($data->isBanned);
        $this->assertNull($data->lastLoginIp);
    }
}
