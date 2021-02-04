<?php

namespace App\Infrastructure\Orm;

/**
 * Interface représentant un repository qui accepte une méthode de nettoyage pour supprimer des données périmées.
 */
interface CleanableRepositoryInterface
{
    public function clean(): int;
}
