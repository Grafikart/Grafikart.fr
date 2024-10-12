<?php

namespace App\Http\ValueResolver;

use App\Http\ValueResolver\Attribute\MapHydratedEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Récupère une entité (comme MapEntity), l'hydrate et la valide
 * Agit comme une combinaison entre MapEntity & MapRequestPayload
 */
final class EntityHydratorValueResolver implements ValueResolverInterface
{
    private readonly EntityValueResolver $entityValueResolver;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
        ManagerRegistry                      $registry,
        ?ExpressionLanguage                  $expressionLanguage = null,
    )
    {
        $this->entityValueResolver = new EntityValueResolver($registry, $expressionLanguage);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (\is_object($request->attributes->get($argument->getName()))) {
            return [];
        }

        $attribute = $argument->getAttributes(MapHydratedEntity::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!($attribute instanceof MapHydratedEntity)) {
            return [];
        }

        // Agit comme un MapEntity
        $entity = $this->entityValueResolver->resolve($request, $argument)[0] ?? null;
        if ($entity::class !== $argument->getType()) {
            throw new NotFoundHttpException((sprintf('"%s" object not found by "%s".', $argument->getType(), self::class)));
        }

        // Hydrate l'objet avec le contenu de la requête
        $this->serializer->deserialize($request->getContent(), $entity::class, $request->getContentTypeFormat(), [
            'groups' => $attribute->groups,
            AbstractNormalizer::OBJECT_TO_POPULATE => $entity
        ]);
        $violations = $this->validator->validate($entity, groups: $attribute->validationGroups);

        if (\count($violations)) {
            throw HttpException::fromStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, implode("\n", array_map(static fn($e) => $e->getMessage(), iterator_to_array($violations))), new ValidationFailedException($entity, $violations));
        }

        return [
            $entity
        ];
    }
}
