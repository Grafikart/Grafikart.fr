<?php

namespace App\Domain\Notification\Entity;

use Doctrine\ORM\Mapping as ORM;

trait Notifiable
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $notificationsReadAt = null;

    public function getNotificationsReadAt(): ?\DateTimeInterface
    {
        return $this->notificationsReadAt;
    }

    public function setNotificationsReadAt(?\DateTimeInterface $notificationsReadAt): void
    {
        $this->notificationsReadAt = $notificationsReadAt;
    }
}
