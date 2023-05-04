<?php

namespace App\Domain\Glossary\Entity;

/**
 * @property string[] $synonyms
 */
class GlossaryItemSimple
{
    public function __construct(public int $id, public string $name, public string $slug, public ?array $synonyms = [])
    {
    }
}
