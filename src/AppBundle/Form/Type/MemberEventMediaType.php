<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ApplicantRecipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberEventMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('media', FileType::class, array());
        $builder->add('type', TextType::class, array());
        $builder->add('applicantRecipe', EntityHiddenType::class, array(
            'class'    => ApplicantRecipe::class,
            'required' => false,
        ));
        $builder->add('friendEmail', TextType::class, array('mapped' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}
