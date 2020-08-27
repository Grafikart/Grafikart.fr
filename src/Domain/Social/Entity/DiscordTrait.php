<?php


namespace App\Domain\Social\Entity;

use Doctrine\ORM\Mapping as ORM;

trait DiscordTrait
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $discordId = null;

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;
        return $this;
    }
}
