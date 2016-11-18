<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, array(
            'constraints' => new Length(array('min' => 3)),
        ));
        $builder->add('lastName');
        $builder->add('birthDate', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
        ));
        $builder->add('uploaded_image', FileType::class, array(
            'label' => false,
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Member',
            'intention' => 'registration',
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_member';
    }
}
