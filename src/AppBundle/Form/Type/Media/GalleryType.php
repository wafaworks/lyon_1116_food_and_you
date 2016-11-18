<?php

namespace AppBundle\Form\Type\Media;

use AppBundle\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'gallery_has_medias',
            CollectionType::class,
            array(
                'entry_type'   => GalleryHasMediaType::class,
                'entry_options'  => array(
                    'required'  => false,
                    'label' => false,
                    'attr'      => array('class' => 'row')
                ),
                'label' => false,

            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Gallery::class,
            'translation_domain' => 'forms',
        ));
    }
}
