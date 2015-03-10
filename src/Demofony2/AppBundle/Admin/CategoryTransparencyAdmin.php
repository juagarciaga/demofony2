<?php
namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Pix\SortableBehaviorBundle\Services\PositionHandler;

class CategoryTransparencyAdmin extends Admin
{
    public $last_position = 0;
    private $positionService;

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC', // sort direction
        '_sort_by' => 'position', // field name
    );

    /**
     * @param PositionHandler $positionHandler
     */
    public function setPositionService(PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('name')
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name')
                ->add('image', 'demofony2_admin_image', array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $mapper)
    {
        $this->last_position = $this->positionService->getLastPosition($this->getRoot()->getClass());

        $mapper
            ->addIdentifier('name')
            ->addIdentifier('image', null, array('template' => ':Admin\ListFieldTemplate:image.html.twig'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'move' => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
                    'edit' => array(),
                ),
                'label' => 'Accions'
            ))
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
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
        $collection->remove('export');
    }
}
