<?php

namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProcessParticipationPageAdmin
 *
 * @category Admin
 * @package  Demofony2\AppBundle\Admin
 */
class ProcessParticipationPageAdmin extends Admin
{
    protected $translationDomain = 'admin';
    protected $baseRoutePattern = 'no-view/participation-process-page';
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'createdAt', // field name
    );

    /**
     * Configure edit view
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('title', 'text', array('label' => 'title'))
                ->add('description', 'ckeditor', array('label' => 'description'))
                ->add('published', 'checkbox', array('label' => 'published', 'required' => false))
                ->add('position', null, array('label' => 'position', 'required' => false))
        ;
    }

    /**
     * Set default options
     *
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'translation_domain' => 'admin',
            )
        );
    }
}
