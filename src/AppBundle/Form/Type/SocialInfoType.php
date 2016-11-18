<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Embeddables\SocialInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialInfoType extends AbstractType implements DataMapperInterface
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', TextType::class, array('label' => 'restaurant.label.site',
                'attr' => ['placeholder' => 'restaurant.placeholder.site'],
                'required' => false,
            ))
            ->add('tripAdvisor', TextType::class, array(
                'label' => 'restaurant.label.tripAdvisor',
                'attr' => ['placeholder' => 'restaurant.placeholder.tripAdvisor'],
                'required' => false,
            ))
            ->add('facebook', TextType::class, array('label' => 'restaurant.label.facebook',
                'attr' => ['placeholder' => 'restaurant.placeholder.facebook'],
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
            'data_class' => 'AppBundle\Entity\Embeddables\SocialInfo',
            'empty_data' => null,
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'app_social_info';
    }

    /**
     * @inheritdoc
     */
    public function mapDataToForms($data, $forms)
    {
        /** @var SocialInfo $data */
        /** @var FormInterface $forms */
        $forms = iterator_to_array($forms);
        $forms['site']->setData($data ? $data->getSite() : '');
        $forms['tripAdvisor']->setData($data ? $data->getTripAdvisor() : '');
        $forms['facebook']->setData($data ? $data->getFacebook() : '');
    }

    /**
     * @inheritdoc
     */
    public function mapFormsToData($forms, &$data)
    {
        /** @var FormInterface $forms */
        $forms = iterator_to_array($forms);
        $data = new SocialInfo(
            $forms['site']->getData(),
            $forms['tripAdvisor']->getData(),
            $forms['facebook']->getData()
        );
    }
}
