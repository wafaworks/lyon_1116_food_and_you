<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Event;
use AppBundle\Entity\Reservation;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class ReservationAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('member')
            ->add('event')
            ->add('tableOwner')
            ->add('status')
            ->add('places')
            ->add('transaction.id')
            ->add('transaction.formattedAmount')
            ->add('transaction.created')
            ->add('transaction.updated')
            ->add('transaction.merchant_id')
            ->add('transaction.merchant_country')
            ->add('transaction.payment_means')
            ->add('transaction.response_code')
            ->add('transaction.payment_certificate')
            ->add('transaction.authorisation_id')
            ->add('transaction.currency_code')
            ->add('transaction.card_number')
            ->add('transaction.card_validity')
            ->add('transaction.cvv_flag')
            ->add('transaction.cvv_response_code')
            ->add('transaction.bank_response_code')
            ->add('transaction.capture_mode')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('member', 'sonata_type_model_list', array('required' => true))
                ->add('event', 'sonata_type_model_list', array('required' => true))
                ->add('tableOwner', 'sonata_type_model_list', array('required' => true))
                ->add('status',
                    'choice',
                    array(
                        'required' => true,
                        'choices' => Reservation::getStatuses(),
                        'translation_domain' => 'SonataAdminReservation',
                    )
                )
                ->add('places', null, array('required' => true))
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('transaction.id')
            ->add('transaction.formattedAmount')
            ->add('member')
            ->add('event')
            ->add('event.status', 'trans', array('catalogue' => 'SonataAdminEvent'))
            ->add('status', 'trans', array('catalogue' => 'SonataAdminReservation'))
            ->add('places')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'status',
                null,
                array(),
                'choice',
                array(
                    'choices' => Reservation::getStatuses(),
                    'translation_domain' => 'SonataAdminReservation',
                )
            )
            ->add(
                'event.status',
                null,
                array(),
                'choice',
                array(
                    'choices' => Event::getStatuses(),
                    'translation_domain' => 'SonataAdminEvent',
                )
            )
            ->add('member')
            ->add('event.startDate',
                'doctrine_orm_date_range',
                ['label' => 'Event date (d/m/y)'],
                null,
                array('widget' => 'single_text', 'format' => 'd/M/y')
            )
            ->add('transaction.created',
                'doctrine_orm_date_range',
                ['label' => 'Transaction date (d/m/y)'],
                null,
                array('widget' => 'single_text', 'format' => 'd/M/y')
            )
        ;
    }

    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        unset($actions['delete']);

        if (
            $this->hasRoute('edit') && $this->isGranted('EDIT')
        ) {
            $actions['refunded'] = array(
                'label' => $this->trans('action_refunded', array(), 'SonataAdminBundle'),
                'ask_confirmation' => true
            );
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $query->addSelect('t');
        $query->leftJoin($query->getRootAliases()[0] . '.transaction', 't');

        return $query;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('create');
    }

    public function getExportFields()
    {
        return [
            'id',
            'transaction.id',
            'invoiceNumber',
            'status',
            'event.restaurant.name',
            'event.startDate',
            'event.price',
            'places',
            'transaction.formattedAmount',
        ];
    }
}
