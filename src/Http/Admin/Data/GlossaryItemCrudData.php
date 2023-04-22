<?php

declare(strict_types=1);

namespace App\Http\Admin\Data;

use App\Domain\Glossary\Entity\GlossaryItem;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property GlossaryItem $entity
 */
final class GlossaryItemCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;
    public ?string $slug = null;
    public ?GlossaryItem $synonym = null;
    public ?string $content = null;
    public ?\DateTimeInterface $createdAt;

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name ?? '')
            ->setSlug($this->slug ?? (new Slugify())->slugify($this->name ?? ''))
            ->setContent($this->content ?? '')
            ->setCreatedAt($this->createdAt ?? new \DateTimeImmutable())
            ->setSynonym($this->synonym)
            ->setUpdatedAt(new \DateTimeImmutable());
    }
}
