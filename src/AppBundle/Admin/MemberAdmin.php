<?php
namespace AppBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MemberAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('firstName')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Member')
                ->with('Member', array('class' => 'col-md-8'))->end()
                ->with('Member Info', array('class' => 'col-md-4'))->end()
            ->end()
            ->tab('Authentication')
                ->with('Authentication', array('class' => 'col-md-12'))->end()
            ->end();

        $formMapper
            ->tab('Member')
                ->with('Member')
                    ->add('firstName', 'text', array('required' => true))
                    ->add('lastName', 'text', array('required' => true))
                    ->add('birthDate', 'sonata_type_date_picker', array('required' => true))
                    ->add('photo', 'sonata_type_model_list', array('required' => true))
                    ->add('biography', 'textarea', array('required' => true))
                    ->add('signature', 'textarea', array('required' => true))
                ->end()
                ->with('Member Info')
                    ->add('tableCode', 'text', array('required' => true))
                    ->add('rating', 'text', array('required' => true))
                    ->add('level', 'text', array('required' => true))
                ->end()
            ->end()
            ->tab('Authentication')
                ->with('Authentication')
                    ->add('authentication', 'sonata_type_admin', array('delete' => false, 'label' => false))
                ->end()
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('fullName')
            ->add('authentication.email')
            ->add('phone')
            ->add('nrApplications')
            ->add('participations')
            ->add('nrRecipes')
            ->add('nrReservations')
            ->add('rating', 'decimal')
            ->add('profile', 'string', array('template' => ':admin:link_member_profile.html.twig'))
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('firstName')
            ->add('lastName')
            ->add('rating', 'doctrine_orm_number')
            ->add('participations', 'doctrine_orm_number')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $query->addSelect('a');
        $query->leftJoin($query->getRootAliases()[0] . '.authentication', 'a');

        return $query;
    }
}
