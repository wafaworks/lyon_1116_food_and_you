<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'city',
            EntityType::class,
            array(
                'class'   => 'AppBundle:City',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                },
            )
        );
        $builder->add(
            'eventDate',
            TextType::class,
            array(
                'data' => (new \DateTime)->format('Y-m-d'),
                'attr' => array(
                    'autocomplete' => 'off',
                )
            )
        );
        $builder->add(
            'restaurant',
            TextType::class,
            array(
                'required' => false
            )
        );
        $builder->add('participatorType', ChoiceType::class, array(
            'choices' => array(
                'forms.search.participate_as.visitor' => Event::STATUS_RESERVATIONS_OPENED,
                'forms.search.participate_as.cook' => Event::STATUS_APPLICANT_REGISTRATION_OPENED,
            ),
            'placeholder' => 'forms.search.participate_as.placeholder',
            'translation_domain' => 'forms',
            'choices_as_values' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_search';
    }
}
