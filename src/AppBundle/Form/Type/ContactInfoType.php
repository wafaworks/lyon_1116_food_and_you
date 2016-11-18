<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Embeddables\ContactInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactInfoType extends AbstractType implements DataMapperInterface
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class, array('label' => 'restaurant.label.phone'))
            ->add('mobilePhone', TextType::class, array('label' => 'restaurant.label.mobilePhone'))
            ->add('email', EmailType::class, array('label' => 'restaurant.label.email'))

            ->setDataMapper($this)
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Embeddables\ContactInfo',
            'empty_data' => null,
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'app_contact_info';
    }

    /**
     * @inheritdoc
     */
    public function mapDataToForms($data, $forms)
    {
        /** @var ContactInfo $data */
        /** @var FormInterface $forms */
        $forms = iterator_to_array($forms);
        $forms['phone']->setData($data ? $data->getPhone() : '');
        $forms['mobilePhone']->setData($data ? $data->getMobilePhone() : '');
        $forms['email']->setData($data ? $data->getEmail() : '');
    }

    /**
     * @inheritdoc
     */
    public function mapFormsToData($forms, &$data)
    {
        /** @var FormInterface $forms */
        $forms = iterator_to_array($forms);
        $data = new ContactInfo(
            $forms['phone']->getData(),
            $forms['mobilePhone']->getData(),
            $forms['email']->getData()
        );
    }
}
