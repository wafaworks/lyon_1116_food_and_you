<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'dates',
            HiddenType::class,
            array(
                'error_bubbling' => false,
                'attr' => array(
                    'autocomplete' => 'off'
                )
            )
        );
        $builder->add('placesRange', HiddenType::class, array(
            'label' => 'form.new_event.placesRange',
            'error_bubbling' => false,
        ));
        $builder->add('price', ChoiceType::class, array(
            'expanded' => true,
            'choices' => Event::getPrices(),
            'attr'=> ['class' => 'gray-input'],
            'label' => 'form.new_event.price',
            'label_attr'  => array(
                'class' => 'radio-inline'
            )
        ));
        $builder->add('hour', TimeType::class, array(
            'widget'=>'choice',
            'label' => 'form.new_event.hour',
            'input' => 'string',
            'invalid_message' => 'form.new_event.hour.invalid'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Model\NewEvent',
            'translation_domain' => 'restaurant_owner_admin_event_new',
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_new_event_type';
    }
}
