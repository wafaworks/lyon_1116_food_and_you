<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Member;
use AppBundle\Entity\Recipe;
use AppBundle\Model\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('biography', TextareaType::class, array(
            'label' => 'form.member.biography',
            'required' => true,
        ));

        $builder->add('profession', TextType::class, array(
            'label' => 'form.member.profession',
            'required' => true,
        ));
        $builder->add('signature', TextareaType::class, array(
            'label' => 'form.member.signature',
            'required' => true,
        ));
        $builder->add('phone', TextType::class, array(
            'label' => 'form.member.phone',
            'required' => true,
        ));
        $builder->add('uploaded_image', FileType::class, array(
            'label' => false,
            'required' => false,
        ));
        $builder->add(
            'cookWith',
            CollectionType::class,
            array(
                'entry_type'   => EntityHiddenType::class,
                'entry_options'  => array(
                    'class'     => Member::class,
                    'required'  => false
                ),
                'label' => false,
            )
        );

        $builder->add(
            'recipes',
            CollectionType::class,
            array(
                'entry_type'   => EntityHiddenType::class,
                'entry_options'  => array(
                    'class'     => Recipe::class,
                    'required'  => true,
                    'attr'      => array('class' => 'applicant-recipe')
                ),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label' => false,
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Application::class,
            'translation_domain' => 'forms',
        ));
    }
}
