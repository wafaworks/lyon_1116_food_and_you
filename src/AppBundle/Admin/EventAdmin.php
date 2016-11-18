<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Event;
use AppBundle\Form\Type\CapacityType;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EventAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('restaurant')
        ;
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
            ->add('restaurant', 'sonata_type_model_list', array('btn_add' => false, 'btn_delete' => false))
            ->add('startDate', 'sonata_type_datetime_picker')
            ->add('applicationEndDate', 'sonata_type_datetime_picker')
            ->add(
                'price',
                'choice',
                array(
                    'required' => true,
                    'choices' => Event::getPrices(),
                    'translation_domain' => 'SonataAdminEvent',
                )
            )
            ->add(
                'status',
                'choice',
                array(
                    'required' => true,
                    'choices' => Event::getStatuses(),
                    'translation_domain' => 'SonataAdminEvent',
                )
            )
            ->end()
            ->with(
                'Capacity',
                array(
                    'class' => 'col-md-4',
                )
            )
            ->add('capacity', CapacityType::class, array('label' => false))
            ->end()
//            ->with(
//                'Applicants',
//                array(
//                    'class' => 'col-xs-12',
//                )
//            )
//            ->add('applicants', 'sonata_type_collection', array(
//
//                'label' => false,
//                'type_options' => array(
//                    // Prevents the "Delete" option from being displayed
//                    'delete' => false,
//                )
//            ), array(
//                'edit' => 'inline',
//                'inline' => 'table',
//                'sortable' => 'position',
//
//            ))
//            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('startDate')
            ->add('restaurant.name')
            ->add('capacity')
            ->add('price')
            ->add('confirmedReservations',null, array('label' => 'Nr Reservations'))
            ->add('nrApplicants')
            ->add('eveningRating')
            ->add('restaurantRating')
            ->add('status', 'trans', array('catalogue' => 'SonataAdminEvent'))
            ->add('applicants', 'string', array('template' => ':admin:link_applicants.html.twig'));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $query->addSelect('r,a');
        $query->leftJoin($query->getRootAliases()[0] . '.reservations', 'r');
        $query->leftJoin($query->getRootAliases()[0] . '.applicants', 'a');

        return $query;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'startDate',
                'doctrine_orm_date_range',
                ['label' => 'Start date (d/m/y)'],
                null,
                array('widget' => 'single_text', 'format' => 'd/M/y')
            )
            ->add('restaurant')
            ->add(
                'price',
                null,
                array(),
                'choice',
                array(
                    'choices' => Event::getPrices(),
                    'translation_domain' => 'SonataAdminEvent',
                )
            )
            ->add(
                'status',
                null,
                array(),
                'choice',
                array(
                    'choices' => Event::getStatuses(),
                    'translation_domain' => 'SonataAdminEvent',
                )
            )
        ;
    }
}
