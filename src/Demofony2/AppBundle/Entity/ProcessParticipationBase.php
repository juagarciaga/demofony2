<?php

namespace Demofony2\AppBundle\Entity;

use Demofony2\AppBundle\Enum\ProcessParticipationStateEnum;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ProcessParticipationBase extends ParticipationBaseAbstract
{
    const DEBATE = ProcessParticipationStateEnum::DEBATE;
    const PRESENTATION = ProcessParticipationStateEnum::PRESENTATION;
    const CLOSED = ProcessParticipationStateEnum::CLOSED;

//    /**
//     * @var \DateTime
//     * @ORM\Column(type="datetime")
//     * @Serializer\Groups({"detail"})
//     */
//    protected $presentationAt;

    /**
     * @var \DateTime
     * @ORM\Column( type="datetime")
     * @Serializer\Groups({"detail"})
     */
    protected $debateAt;

    /**
     * @var ArrayCollection
     */
    protected $documents;

    /**
     * @var ArrayCollection
     */
    protected $institutionalDocuments;

    /**
     * @ORM\ManyToOne(targetEntity="Demofony2\UserBundle\Entity\User", inversedBy="processParticipations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $author;

    /**
     * @var ArrayCollection
     */
    protected $categories;

    /**
     * @var ArrayCollection
     **/
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\ProposalAnswer", mappedBy="processParticipation", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     * @Serializer\Groups({"detail"})
     **/
    protected $proposalAnswers;

    /**
     * @var bool
     * @ORM\Column(name="published", type="boolean")
     */
    protected $published;

    /**
     * @var bool
     * @ORM\Column(name="state", type="integer")
     * @Serializer\Groups({"detail"})
     */
    protected $state;

    /**
     * @var int
     * @ORM\Column(name="max_votes", type="integer")
     * @Serializer\Groups({"detail"})
     */
    protected $maxVotes = 1;

    /**
     * @var ArrayCollection
     **/
    protected $pages;

    /**
     * @var string
     * @ORM\Column( type="text", nullable=true)
     */
    protected $infoText;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->published = false;
        $this->automaticState = true;
        $this->pages = new ArrayCollection();
        $this->state = ProcessParticipationStateEnum::PRESENTATION;
    }

    /**
     * Set debateAt.
     *
     * @param \DateTime $debateAt
     *
     * @return ProcessParticipationBase
     */
    public function setDebateAt($debateAt)
    {
        $this->debateAt = $debateAt;

        return $this;
    }

    /**
     * Get debateAt.
     *
     * @return \DateTime
     */
    public function getDebateAt()
    {
        return $this->debateAt;
    }

    /**
     * @return string
     */
    public function getStateName()
    {
        return ProcessParticipationStateEnum::getTranslations()[$this->getState()];
    }

    /**
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param bool $published
     *
     * @return ProcessParticipationBase
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return bool
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param bool $state
     *
     * @return ProcessParticipation
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Remove Page.
     *
     * @param ProcessParticipationPage
     */
    public function removePage(ProcessParticipationPage $page)
    {
        $this->pages->removeElement($page);
    }

    /**
     * Get Pages.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return mixed
     */
    public function getInfoText()
    {
        return $this->infoText;
    }

    /**
     * @param mixed $infoText
     *
     * @return ProcessParticipationBase
     */
    public function setInfoText($infoText)
    {
        $this->infoText = $infoText;

        return $this;
    }

    /**
     * @Assert\True(message = "constraint.dates_correlative")
     */
    public function isDatesValid()
    {
        return ($this->debateAt < $this->getFinishAt()) ? true : false;
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
