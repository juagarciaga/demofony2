<?php

namespace Demofony2\AppBundle\Controller\Admin;

use Demofony2\AppBundle\Report\ExcelResponseBuilder;
use Demofony2\AppBundle\Report\Generator;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProcessParticipationController extends Controller
{
    public function showPublicPageAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $url = $this->generateUrl('demofony2_front_participation_discussions_edit', array('id' => $object->getId(), 'discussion' => $object->getTitleSlug()));

        return new RedirectResponse($url);
    }

    public function showResultsExcelAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $generator = $this->get('app.csv.generator');
        $data = $generator->generateProcessParticipationData($object);
        $responseBuilder = new ExcelResponseBuilder();
        $response = $responseBuilder->buildResponse($data);

        return $response;
    }
}
