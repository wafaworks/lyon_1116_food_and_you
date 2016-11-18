<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Recipe;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Name', TextType::class, array(
            'constraints'   => new Length(array('min' => 3)),
            'required'      => true
        ));
        $builder->add('Photo', MediaType::class, array(
            'required' => true,
            'provider' => 'sonata.media.provider.image',
            'context'  => 'recipe'
        ));
        $builder->add('Type', ChoiceType::class, array(
            'choices'   => array(
                Recipe::TYPE_ENTRY => Recipe::TYPE_ENTRY,
                Recipe::TYPE_MAIN => Recipe::TYPE_MAIN,
                Recipe::TYPE_DESSERT => Recipe::TYPE_DESSERT
            ),
            'required' => true,
        ));
        $builder->add("PublicDescription", TextType::class, array(
            'required'  => true
        ));
        $builder->add("PrivateDescription", TextType::class, array(
            'required'  => true
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recipe',
            'intention'  => 'registration',
            'csrf_protection' => false,
            'allow_extra_fields'    => true
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_recipe';
    }
}
