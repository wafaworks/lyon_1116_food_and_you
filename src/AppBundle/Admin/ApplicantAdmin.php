<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Applicant;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ApplicantAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('restaurant');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'general',
                array(
                    'class' => 'col-md-4',
                )
            )
            ->add('member', 'sonata_type_model_list', array('btn_add' => false, 'btn_delete' => false))
            ->add('appliedAt', 'sonata_type_datetime_picker')
            ->add(
                'status',
                'choice',
                array(
                    'required' => true,
                    'choices' => Applicant::getStatuses(),
                    'translation_domain' => 'SonataAdminApplicant',
                )
            )
            ->end();

        if (!is_numeric($formMapper->getFormBuilder()->getForm()->getName())) {
            $formMapper
                ->with('cat_recipes')
                ->add(
                    'recipes',
                    'sonata_type_collection',
                    array(
                        'label' => false,
                        'type_options' => array(
                            // Prevents the "Delete" option from being displayed
                            'delete' => false,

                        ),
                        'btn_add' => false,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',

                    )
                )
                ->end();
        }
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('event')
            ->add('member.fullName')
            ->add('nrRecipes')
            ->add('nrVotes')
            ->add('status', 'trans', array('catalogue' => 'SonataAdminApplicant'));
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $query->addSelect('e,m,r,re');
        $query->leftJoin($query->getRootAliases()[0] . '.event', 'e');
        $query->leftJoin($query->getRootAliases()[0] . '.member', 'm');
        $query->leftJoin($query->getRootAliases()[0] . '.recipes', 're');
        $query->leftJoin('e.restaurant', 'r');

        return $query;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('event.id')
            ->add('member')
            ->add(
                'status',
                null,
                array(),
                'choice',
                array(
                    'choices' => Applicant::getStatuses(),
                    'translation_domain' => 'SonataAdminApplicant',
                )
            );
    }
}
