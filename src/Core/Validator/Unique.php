<?php


namespace App\Core\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Contrainte pour vérifier l'unicité d'un enregistrement
 *
 * Pour fonctionner on part du principe que l'objet et l'entité aura une méthode "getId()"
 *
 * @Annotation
 */
class Unique extends Constraint
{

    public string $message = 'Cette valeur est déjà utilisée';

    public ?string $entityClass = null;

    public ?string $field = null;

    public function getRequiredOptions()
    {
        return ['entityClass', 'field'];
    }
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}
