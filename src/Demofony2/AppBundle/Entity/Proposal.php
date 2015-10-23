<?php

/**
 * Demofony2.
 *
 * @author: Marc Morales Valldepérez <marcmorales83@gmail.com>
 *
 * Date: 13/11/14
 * Time: 16:52
 */
namespace Demofony2\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Demofony2\AppBundle\Enum\ProposalStateEnum;
use JMS\Serializer\Annotation as Serializer;
use Demofony2\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proposal.
 *
 * @ORM\Table(name="demofony2_proposal")
 * @ORM\Entity(repositoryClass="Demofony2\AppBundle\Repository\ProposalRepository")
 * @Gedmo\SoftDeleteable(fieldName="removedAt")
 */
class Proposal extends ParticipationBaseAbstract
{
    const DEBATE = ProposalStateEnum::DEBATE;
    const CLOSED = ProposalStateEnum::CLOSED;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Document", mappedBy="proposal", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     * @Assert\Valid
     **/
    protected $documents;

    /**
     * @ORM\ManyToOne(targetEntity="Demofony2\UserBundle\Entity\User", inversedBy="proposals")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $author;

    /**
     * @ORM\ManyToMany(targetEntity="Demofony2\AppBundle\Entity\Category", inversedBy="proposals")
     * @ORM\JoinTable(name="demofony2_proposals_category")
     * @Serializer\Groups({"detail"})
     **/
    protected $categories;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Comment", mappedBy="proposal", fetch="LAZY", cascade={"persist", "remove"}, orphanRemoval=true)
     **/
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\ProposalAnswer", mappedBy="proposal", cascade={"persist", "remove"} , orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     * @Serializer\Groups({"detail"})
     **/
    protected $proposalAnswers;

    /**
     * @var int
     * @ORM\Column(name="max_votes", type="integer")
     * @Serializer\Groups({"detail"})
     */
    protected $maxVotes = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable = true)
     * @Serializer\Groups({"detail"})
     */
    protected $state = ProposalStateEnum::DEBATE;

    /**
     * @var bool
     *
     * @ORM\Column(name="moderated", type="boolean")
     */
    protected $moderated;

    /**
     * @var bool
     *
     * @ORM\Column(name="user_draft", type="boolean")
     */
    protected $userDraft;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Document", mappedBy="proposalInstitutionalDocument", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     * @Assert\Valid
     **/
    protected $institutionalDocuments;

    public function __construct()
    {
        parent::__construct();
        $this->published = true;
        $this->moderated = false;
        $this->userDraft = true;
    }

    /**
     * @return string
     */
    public function getStateName()
    {
        return ProposalStateEnum::getTranslations()[$this->getState()];
    }

    /**
     * Add ProposalAnswers.
     *
     * @param ProposalAnswer $proposalAnswer
     *
     * @return Proposal
     */
    public function addProposalAnswer(ProposalAnswer $proposalAnswer)
    {
        $proposalAnswer->setProposal($this);
        $this->proposalAnswers[] = $proposalAnswer;

        return $this;
    }

    /**
     * Is user the author ?
     *
     * @param User $user
     *
     * @return bool
     */
    public function isAuthor(User $user = null)
    {
        return $user && $user === $this->getAuthor();
    }
//
//    /**
//     * Add Images
//     *
//     * @param  Image                $image
//     * @return ProcessParticipation
//     */
//    public function addImage(Image $image)
//    {
//        $image->setProposal($this);
//        $this->images[] = $image;
//
//        return $this;
//    }

    /**
     * Add Documents.
     *
     * @param Document $document
     *
     * @return ParticipationBaseAbstract
     */
    public function addDocument(Document $document)
    {
        $document->setProposal($this);
        $this->documents[] = $document;

        return $this;
    }

    /**
     * @return bool
     */
    public function getModerated()
    {
        return $this->moderated;
    }

    /**
     * @param bool $moderated
     *
     * @return Proposal
     */
    public function setModerated($moderated)
    {
        $this->moderated = $moderated;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUserDraft()
    {
        return $this->userDraft;
    }

    /**
     * @param bool $userDraft
     *
     * @return Proposal
     */
    public function setUserDraft($userDraft)
    {
        $this->userDraft = $userDraft;

        return $this;
    }

    /**
     * @param Document $document Document
     *
     * @return ProcessParticipation
     */
    public function addInstitutionalDocument(Document $document)
    {
        $document->setProposalInstitutionalDocument($this);
        $this->institutionalDocuments[] = $document;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxVotes()
    {
        return $this->maxVotes;
    }

    /**
     * @param int $maxVotes
     */
    public function setMaxVotes($maxVotes)
    {
        $this->maxVotes = $maxVotes;
    }
}
