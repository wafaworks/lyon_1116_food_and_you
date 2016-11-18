<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAuthenticationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('email');
        $builder->remove('current_password');
        $builder->remove('username');
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'second_options' => array('label' => 'form.member.password'),
            'first_options' => array('label' => 'form.member.password.repeat'),
            'required' => false
        ));
        $builder->add('member', new EditMemberType, array());
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
        ));
    }
}
