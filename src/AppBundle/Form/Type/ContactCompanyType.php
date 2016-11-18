<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, array(
            'required' => false
        ));

        $builder->add('lastName', TextType::class, array(
            'required' => false
        ));

        $builder->add('company', TextType::class, array(
            'required' => false
        ));

        $builder->add('email', EmailType::class, array(
            'required' => false
        ));

        $builder->add('phone', TextType::class, array(
            'required' => false
        ));

        $builder->add('capacity', IntegerType::class, array(
            'required' => false
        ));

        $builder->add('notice', TextareaType::class, array(
            'required' => false
        ));

        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'contact-company'
        ));
    }
}
