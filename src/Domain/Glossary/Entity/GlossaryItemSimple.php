<?php

namespace App\Domain\Glossary\Entity;

class GlossaryItemSimple
{

    /** @var string[] */
    public $synonyms = [];

    public function __construct(public int $id, public string $name, public string $slug, public ?int $synonymId)
    {
    }

    public function addSynonym(GlossaryItemSimple $item): void
    {
        $this->synonyms[] = $item->name;
    }
}
