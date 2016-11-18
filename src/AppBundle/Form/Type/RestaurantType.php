<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Media\GalleryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'restaurant.label.name'));
        $builder->add('openingDate', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'label' => 'restaurant.label.openingDate',
        ));
        $builder->add('cuisine', EntityType::class, array(
            'class' => 'AppBundle:Cuisine',
            'choice_label' => 'name',
            'label' => 'restaurant.label.cuisine',
            'placeholder' => 'restaurant.placeholder.cuisine',
            'empty_data'  => null
        ));
        $builder->add('description', TextareaType::class, array(
            'label' => 'restaurant.label.description',
            'attr' => ['placeholder' => 'restaurant.placeholder.description'],
        ));

        $builder->add('street', TextType::class, array(
            'label' => 'restaurant.label.street'
        ));
        $builder->add('postalCode', TextType::class, array(
            'label' => 'restaurant.label.postalCode'
        ));
        $builder->add('city', EntityType::class, array(
            'class' => 'AppBundle:City',
            'choice_label' => 'name',
            'label' => 'restaurant.label.city',
            'placeholder' => 'restaurant.placeholder.city',
            'empty_data'  => null
        ));

        $builder->add('socialInfo', SocialInfoType::class);
        $builder->add('contactInfo', ContactInfoType::class);
        $builder->add('gallery', GalleryType::class, array(
            'label' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Restaurant',
            'intention'  => 'registration',
            'translation_domain' => 'forms',
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_restaurant';
    }
}
