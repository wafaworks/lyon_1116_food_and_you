<?php
namespace AppBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ApplicantRecipeAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('recipe')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        if (!is_numeric($formMapper->getFormBuilder()->getForm()->getName())) {
            $formMapper
                ->add('applicant', 'sonata_type_model_list', array('btn_add' => false, 'btn_delete' => false))
                ;
        }
        $formMapper
            ->add('recipe', 'sonata_type_model_list', array('btn_add' => false, 'btn_delete' => false))
            ->add('rating')
            ->add('selected')
            ->add('winner')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('applicant')
            ->add('recipe')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $query->addSelect('re,a,m');
        $query->leftJoin($query->getRootAliases()[0] . '.recipe', 're');
        $query->leftJoin($query->getRootAliases()[0] . '.applicant', 'a');
        $query->leftJoin('a.event', 'e');
        $query->leftJoin('e.restaurant', 'r');
        $query->leftJoin('a.member', 'm');

        return $query;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('event')
            //->add('applicant.member')
        ;
    }
}
