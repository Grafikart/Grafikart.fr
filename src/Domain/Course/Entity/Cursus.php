<?php

namespace App\Domain\Course\Entity;

use App\Domain\Application\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Domain\Course\Repository\CursusRepository::class)]
class Cursus extends Content
{
    use ChapterableTrait;

    #[ORM\JoinTable(name: 'cursus_modules')]
    #[ORM\ManyToMany(targetEntity: Content::class)]
    private Collection $modules;

    #[ORM\ManyToOne(targetEntity: CursusCategory::class)]
    private ?CursusCategory $category = null;

    public function __construct()
    {
        parent::__construct();
        $this->modules = new ArrayCollection();
    }

    public function getModulesById(): array
    {
        $modules = $this->getModules();
        $modulesById = [];
        foreach ($modules as $module) {
            $modulesById[$module->getId()] = $module;
        }

        return $modulesById;
    }

    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Content $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
        }

        return $this;
    }

    public function removeModule(Content $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
        }

        return $this;
    }

    public function getCategory(): ?CursusCategory
    {
        return $this->category;
    }

    public function setCategory(CursusCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
