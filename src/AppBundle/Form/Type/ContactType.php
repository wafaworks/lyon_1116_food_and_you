<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('mail', EmailType::class, array(
            'required'    => false,
            'label'=> 'app.form.contact.label.mail',
            'constraints' => array(
                new Email(),
                new NotBlank(),
            ),
        ));
        $builder->add('subject', TextType::class, array(
            'required'    => false,
            'label'=> 'app.form.contact.label.subject',
            'constraints' => array(
                new NotBlank()
            ),
        ));
        $builder->add('message', TextareaType::class, array(
            'required'    => false,
            'label'=> 'app.form.contact.label.message',
            'constraints' => array(
                new NotBlank()
            ),
        ));
        $builder->add('submit', SubmitType::class, array(
            'label' => 'app.form.contact.submit'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'contact'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_contact';
    }
}
