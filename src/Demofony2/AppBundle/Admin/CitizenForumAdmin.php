<?php

namespace Demofony2\AppBundle\Admin;

class CitizenForumAdmin extends ProcessParticipationAdmin
{
    public function prePersist($object)
    {
        foreach ($object->getDocuments() as $document) {
            $document->setCitizenForum($object);
        }

        foreach ($object->getInstitutionalDocuments() as $document) {
            $document->setCitizenForumInstitutionalDocument($object);
        }

        foreach ($object->getProposalAnswers() as $pa) {
            $pa->setCitizenForum($object);
        }

        foreach ($object->getPages() as $p) {
            $p->setCitizenForum($object);
        }
    }

    public function preUpdate($object)
    {
        $this->prePersist($object);
    }
}
