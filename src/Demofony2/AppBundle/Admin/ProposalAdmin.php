<?php
namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Demofony2\AppBundle\Enum\ProposalStateEnum;
use Pix\SortableBehaviorBundle\Services\PositionHandler;

class ProposalAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id', // field name
    );

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('title')
            ->add('state', 'doctrine_orm_choice', array('choices' => ProposalStateEnum::toArray()))
//            ->add('finishAt', 'doctrine_orm_datetime_range', array(), null,  array('widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm', 'attr' => array('class' => 'datepicker')))

            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                array(
                    'class' => 'col-md-6',
                    'description' => 'General Information',
                )
            )
            ->add('title')
            ->add('description', 'ckeditor')
            ->end()
            ->with(
                'Controls',
                array(
                    'class' => 'col-md-6',
                    'description' => ''
                )
            )
                ->add('categories', 'sonata_type_model', array('multiple' => true, 'by_reference' => false))
                ->add('commentsModerated','checkbox', array('required' => false))
                ->add(
                    'finishAt',
                    'sonata_type_datetime_picker',
                    array('widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm')
                )
                ->add('state', 'choice', array('choices' => ProposalStateEnum::getTranslations()))
            ->end()
            ->with(
                'Proposal Answers',
                array(
                    'class' => 'col-md-12',
                    'description' => 'Proposal Answers',
                )
            )
            ->add(
                'proposalAnswers',
                'sonata_type_collection',
                array(
                    'type_options' => array(
                        // Prevents the "Delete" option from being displayed
                        'delete' => true,
                        'delete_options' => array(
                            // You may otherwise choose to put the field but hide it
                            'type' => 'checkbox',
                            // In that case, you need to fill in the options as well
                            'type_options' => array(
                                'mapped' => false,
                                'required' => false,
                            ),
                        ),
                    ),
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                )
            )

            ->end()

            ->with(
                'Archivos',
                array(
                    'class' => 'col-md-12',
                    'description' => '',
                )
            )

            ->add('documents', 'sonata_type_collection', array(
                'cascade_validation' => true,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
            ))
            ->add('images', 'sonata_type_collection', array(
                'cascade_validation' => true,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
            ))

            ->end()

            ->with(
                'Institutional Answer',
                array(
                    'class' => 'col-md-12',
                    'description' => 'Institutional Answers',
                )
            )
            ->add('institutionalAnswer', 'sonata_type_admin', array('delete' => false, 'btn_add' => false))
            ->end()



        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->addIdentifier('title')
            ->add('finishAt')
            ->add('state')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                ),
                'label' => 'Accions',
            ))
        ;
        ;
    }

    /**
     * Configure route collection
     *
     * @param RouteCollection $collection collection
     *
     * @return mixed
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    public function prePersist($object)
    {
//        foreach ($object->getDocuments() as $document) {
//            $document->setProposal($object);
//        }
//
//        foreach ($object->getImages() as $image) {
//            $image->setProposal($object);
//        }
//
//        foreach ($object->getProposalAnswers() as $pa) {
//            $pa->setProposal($object);
//        }
    }

    public function preUpdate($object)
    {
        $this->prePersist($object);
    }
}
