<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('biography', TextareaType::class, array(
            'label' => 'form.member.biography',
            'required' => false,
        ));
        $builder->add('profession', TextType::class, array(
            'label' => 'form.member.profession',
            'required' => false,
        ));
        $builder->add('signature', TextareaType::class, array(
            'label' => 'form.member.signature',
            'required' => false,
        ));
        $builder->add('uploaded_image', FileType::class, array(
            'label' => false,
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "AppBundle\Entity\Member",
            'translation_domain' => 'forms',
        ));
    }
}
