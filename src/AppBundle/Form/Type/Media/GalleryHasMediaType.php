<?php

namespace AppBundle\Form\Type\Media;

use AppBundle\Entity\GalleryHasMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryHasMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'media',
                MediaType::class,
                array(
                    'label' => false,
                    'attr' => [
                        'class' => 'col-sm-4',
                    ],
                )
            )
            ->add('position', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GalleryHasMedia::class,
            'translation_domain' => 'forms',
        ));
    }
}
