<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Représente un champs permettant de rentrer des technologies sur le site en suivant le format Nom:version en se basant sur.
 */
class TechnologiesType extends TextType implements DataTransformerInterface
{
    public function __construct(private readonly TechnologyRepository $repository, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'attr' => [
                'is' => 'input-choices',
                'data-remote' => $this->urlGenerator->generate('api_technology_search'),
                'data-create' => '1',
                'data-value' => 'name',
                'data-label' => 'name',
            ],
        ]);
        parent::configureOptions($resolver);
    }

    /**
     * @param string|Technology[] $value
     */
    public function transform($value): ?string
    {
        if (!is_array($value)) {
            return null;
        }

        return implode(',', array_map(function (Technology $technology): ?string {
            if ($technology->getVersion()) {
                return $technology->getName().':'.$technology->getVersion();
            }

            return $technology->getName();
        }, $value));
    }

    /**
     * @param ?string $value
     */
    public function reverseTransform($value): array
    {
        if (empty($value)) {
            return [];
        }

        // On construit un tableau contenant les noms des technos en clef et la version en valeur
        $versions = [];
        $technologies = explode(',', $value);
        foreach ($technologies as $technology) {
            $parts = explode(':', trim($technology));
            if (!empty($parts[0])) {
                $versions[$parts[0]] = $parts[1] ?? null;
            }
        }

        // On trouve les technologies depuis la base de données
        $technologies = $this->repository->findByNames(array_keys($versions));
        $technologiesByName = collect($technologies)->keyBy(fn (Technology $t) => $t->getName() ?? '')->toArray();

        foreach ($versions as $name => $version) {
            // Si la technologie n'existe pas déjà, on la crée
            if (!isset($technologiesByName[$name])) {
                $technologies[] = (new Technology())
                    ->setVersion($version)
                    ->setName($name);
            } else {
                $technologiesByName[$name]->setVersion($version);
            }
        }

        return $technologies;
    }
}
