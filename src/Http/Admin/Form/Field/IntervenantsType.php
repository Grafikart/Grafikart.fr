<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Auth\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IntervenantsType extends AbstractType implements DataTransformerInterface
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $url;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $url)
    {
        $this->em = $em;
        $this->url = $url;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $choices = [];
        $collection = $form->getData();
        if ($collection instanceof Collection) {
            $choices = $collection->map(function (User $user) {
                return new ChoiceView($user, (string) $user->getId(), $user->getUsername());
            })->toArray();
        }
        $view->vars['choice_translation_domain'] = false;
        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = false;
        $view->vars['multiple'] = true;
        $view->vars['preferred_choices'] = [];
        $view->vars['value'] = $this->transform($collection);
        $view->vars['choices'] = $choices;
        $view->vars['full_name'] .= '[]';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'multiple' => true,
            'attr' => [
                'multiple' => true,
                'is' => 'select-choices',
                'data-remote' => $this->url->generate('admin_user_autocomplete'),
                'data-value' => 'id',
                'data-label' => 'username',
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'choice';
    }

    /**
     * @param PersistentCollection $collection
     */
    public function transform($collection): array
    {
        return $collection->map(function (User $user) {
            return (string) $user->getId();
        })->toArray();
    }

    /**
     * @param array $ids
     */
    public function reverseTransform($ids): ArrayCollection
    {
        if (empty($ids)) {
            return new ArrayCollection([]);
        }
        return new ArrayCollection($this->em->getRepository(User::class)->findBy(['id' => $ids]));
    }
}
