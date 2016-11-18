<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Recipe;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class RecipeAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'general',
                array(
                    'class' => 'col-md-6',
                )
            )
            ->add('name')
            ->add(
                'type',
                'choice',
                array(
                    'required' => true,
                    'choices' => Recipe::getTypes(),
                    'translation_domain' => 'SonataAdminRecipe',
                )
            )
            ->add('member', 'sonata_type_model_list', array('btn_add' => false, 'btn_delete' => false))
            ->add('photo', 'sonata_type_model_list', array(), array('link_parameters' => array('context' => 'recipe')))
            ->end()
            ->with(
                'description',
                array(
                    'class' => 'col-md-6',
                )
            )
            ->add('publicDescription')
            ->add('privateDescription')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('member.fullName')
            ->add('type', 'trans', array('catalogue' => 'SonataAdminRecipe'))
        ;
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function createQuery($context = 'list')
//    {
//        /** @var QueryBuilder $query */
//        $query = parent::createQuery($context);
//
//        $query->addSelect('e,m,r');
//        $query->leftJoin($query->getRootAliases()[0] . '.event', 'e');
//        $query->leftJoin($query->getRootAliases()[0] . '.member', 'm');
//        $query->leftJoin('e.restaurant', 'r');
//
//        return $query;
//    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('member')
            ->add('name')
            ->add(
                'type',
                null,
                array(),
                'choice',
                array(
                    'choices' => Recipe::getTypes(),
                    'translation_domain' => 'SonataAdminRecipe',
                )
            )
        ;
    }
}
