<?php

namespace App\Infrastructure\Social\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SocialLoggableTrait
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $discordId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $githubId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $googleId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $facebookId = null;

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    public function setGithubId(?string $githubId): self
    {
        $this->githubId = $githubId;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function useOauth(): bool
    {
        return null !== $this->googleId || null !== $this->facebookId || null !== $this->githubId;
    }
}
