<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Embeddables\Capacity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CapacityType extends AbstractType implements DataMapperInterface
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minimum', TextType::class, array(
                'label' => 'capacity.label.minimum',
                'required' => false,
            ))
            ->add('maximum', TextType::class, array(
                'label' => 'capacity.label.maximum',
                'required' => false,
            ))

            ->setDataMapper($this)
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Embeddables\Capacity',
            'empty_data' => null,
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'app_capacity';
    }

    /**
     * @inheritdoc
     */
    public function mapDataToForms($data, $forms)
    {
        /** @var Capacity $data */
        /** @var FormInterface $forms */
        $forms = iterator_to_array($forms);
        $forms['minimum']->setData($data ? $data->getMinimum() : '');
        $forms['maximum']->setData($data ? $data->getMaximum() : '');
    }

    /**
     * @inheritdoc
     */
    public function mapFormsToData($forms, &$data)
    {
        /** @var FormInterface $forms */
        $forms = iterator_to_array($forms);
        $data = new Capacity(
            $forms['minimum']->getData(),
            $forms['maximum']->getData()
        );
    }
}
