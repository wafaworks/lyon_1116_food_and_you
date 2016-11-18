<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Restaurant;
use AppBundle\Form\Type\ContactInfoType;
use AppBundle\Form\Type\SocialInfoType;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class RestaurantAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                array(
                    'class' => 'col-md-8',
                )
            )
            ->add('name')
            ->add(
                'status',
                'choice',
                array(
                    'required' => true,
                    'choices' => Restaurant::getStatuses(),
                    'translation_domain' => 'SonataAdminRestaurant',
                )
            )
            ->add('cuisine')
            ->add('owner', 'sonata_type_model_list', array('required' => true))
            ->add('gallery', 'sonata_type_model_list', array('required' => true))
            ->add('openingDate', 'sonata_type_date_picker', array('required' => true, 'dp_language' => 'fr'))
            ->add(
                'description',
                null,
                array(
                    'attr' => array(
                        'style' => 'height: 350px',
                    ),
                )
            )
            ->end()
            ->with(
                'Address info',
                array(
                    'class' => 'col-md-4',
                )
            )
            ->add('street')
            ->add('postalCode')
            ->add('city')
            ->end()
            ->with(
                'Social info',
                array(
                    'class' => 'col-md-4',
                )
            )
            ->add(
                'socialInfo',
                SocialInfoType::class,
                array(
                    'label' => false,
                    'translation_domain' => 'forms',
                )
            )
            ->end()
            ->with(
                'Contact info',
                array(
                    'class' => 'col-md-4',
                )
            )
            ->add(
                'contactInfo',
                ContactInfoType::class,
                array(
                    'label' => false,
                    'translation_domain' => 'forms',
                )
            )
            ->end();
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('owner.fullName')
            ->add('nrEvents')
            ->add('status', 'trans', array('catalogue' => 'SonataAdminRestaurant'))
            ->add('bo', 'string', array('template' => ':admin:link_backoffice.html.twig'))
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(

                        'validate' => array(
                            'template' => ':admin:list_action_validate.html.twig',
                        ),
                        'reject' => array(
                            'template' => ':admin:list_action_reject.html.twig',
                        ),
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $query->addSelect('e,m');
        $query->leftJoin($query->getRootAliases()[0] . '.events', 'e');
        $query->leftJoin($query->getRootAliases()[0] . '.owner', 'm');

        return $query;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('validate', $this->getRouterIdParameter() . '/validate');
        $collection->add('reject', $this->getRouterIdParameter() . '/reject');
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add(
                'status',
                null,
                array(),
                'choice',
                array(
                    'choices' => Restaurant::getStatuses(),
                    'translation_domain' => 'SonataAdminRestaurant',
                )
            );
    }
}
