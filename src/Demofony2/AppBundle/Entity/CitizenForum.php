<?php
/**
 * Demofony2 for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 * 
 * @author: Marc Morales Valldepérez <marcmorales83@gmail.com> 
 * 
 * Date: 30/03/15
 * Time: 11:13
 */
namespace Demofony2\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Demofony2\AppBundle\Enum\ProcessParticipationStateEnum;

/**
 * CitizenForum
 * @ORM\Table(name="demofony2_")
 * @ORM\Entity(repositoryClass="Demofony2\AppBundle\Repository\CitizenForumRepository")
 * @Gedmo\SoftDeleteable(fieldName="removedAt")
 */
class CitizenForum extends ProcessParticipation
{
    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Document", mappedBy="citizenForum", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     **/
    protected $documents;

    /**
     * @ORM\ManyToOne(targetEntity="Demofony2\UserBundle\Entity\User", inversedBy="citizensForums")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $author;

    /**
     * @ORM\ManyToMany(targetEntity="Demofony2\AppBundle\Entity\Category", inversedBy="citizenForums")
     * @ORM\JoinTable(name="demofony2_citizen_forums_category")
     * @Serializer\Groups({"detail"})
     *
     **/
    protected $categories;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Comment", mappedBy="citizenForum", cascade={"persist"})
     **/
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\ProposalAnswer", mappedBy="citizenForum", cascade={"persist"})
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
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\ProcessParticipationPage", mappedBy="citizenForum", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "DESC"})
     * @Assert\Valid
     **/
    protected $pages;

    /**
     * Constructor
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
     * Add Documents
     *
     * @param  Document                  $document
     * @return ParticipationBaseAbstract
     */
    public function addDocument(Document $document)
    {
        $document->setProcessParticipation($this);
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Add Comments
     *
     * @param  Comment                   $comment
     * @return CitizenForum
     */
    public function addComment(Comment $comment)
    {
        $comment->setProcessParticipation($this);
        $this->comments[] = $comment;

        return $this;
    }

}