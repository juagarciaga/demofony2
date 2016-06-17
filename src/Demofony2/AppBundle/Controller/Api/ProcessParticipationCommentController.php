<?php

namespace Demofony2\AppBundle\Controller\Api;

use Demofony2\AppBundle\Entity\ProcessParticipation;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Demofony2\AppBundle\Entity\Comment;

/**
 * ProcessParticipationCommentController.
 *
 * @Rest\NamePrefix("api_")
 */
class ProcessParticipationCommentController extends FOSRestController
{
    /**
     * Returns comments of level 0 and total count.
     *
     * @param ParamFetcher         $paramFetcher
     * @param ProcessParticipation $processParticipation
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Get Comments of level 0 and total count",
     *                                                   statusCodes={
     *                                                   200="Returned when successful",
     *                                                   404={
     *                                                   "Returned when process participation not found",
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\QueryParam(name="page", requirements="\d+", description="Page offset.", default=1, strict = false)
     * @Rest\QueryParam(name="limit", requirements="\d+", description="Page limit.", default=10, strict = false)
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @Rest\Get("/processparticipations/{id}/comments")
     * @Rest\View(serializerGroups={"list"})
     *
     * @return \Doctrine\Common\Collections\Collections
     */
    public function cgetProcessparticipationCommentsAction(
        ParamFetcher $paramFetcher,
        ProcessParticipation $processParticipation
    ) {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');

        list($comments, $count) = $this->getProcessParticipationManager()->getComments(
            $processParticipation,
            $page,
            $limit
        );

        return ['comments' => $comments, 'count' => (int) $count];
    }

    /**
     * Returns children comments of level >0 and total count.
     *
     * @param ParamFetcher         $paramFetcher
     * @param ProcessParticipation $processParticipation
     * @param Comment              $comment
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Get Children Comments of level > 0 and total count",
     *                                                   statusCodes={
     *                                                   200="Returned when successful",
     *                                                   404={
     *                                                   "Returned when process participation not found",
     *                                                   "Returned when comment not found",
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   },
     *                                                   {
     *                                                   "name"="comment_id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Comment id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\QueryParam(name="page", requirements="\d+", description="Page offset.", default=1, strict = false)
     * @Rest\QueryParam(name="limit", requirements="\d+", description="Page limit.", default=10, strict = false)
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @ParamConverter("comment", class="Demofony2AppBundle:Comment", options={"id" = "comment_id"})
     * @Rest\Get("/processparticipations/{id}/comments/{comment_id}/childrens")
     * @Rest\View(serializerGroups={"children-list"})
     *
     * @return \Doctrine\Common\Collections\Collections
     */
    public function cgetProcessparticipationCommentsChildrensAction(
        ParamFetcher $paramFetcher,
        Comment $comment,
        ProcessParticipation $processParticipation
    ) {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        list($comments, $count) = $this->getProcessParticipationManager()->getChildrenInComment(
            $processParticipation,
            $comment,
            $page,
            $limit
        );

        return ['comments' => $comments, 'count' => (int) $count];
    }

    /**
     * Create new comment.
     *
     * @param Request              $request
     * @param ProcessParticipation $processParticipation
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Post new comment",
     *                                                   statusCodes={
     *                                                   201="Returned when successful",
     *                                                   400={
     *                                                   "Returned when process participation not found",
     *                                                   },
     *                                                   401={
     *                                                   "Returned when user is not logged"
     *                                                   },
     *                                                   500={
     *                                                   "Returned when debate is not open",
     *                                                   "Parent is not consistent"
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   }
     *                                                   },
     *                                                   input="Demofony2\AppBundle\Form\Type\Api\CommentType",
     *                                                   )
     * @Rest\Post("/processparticipations/{id}/comments")
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @Rest\View(serializerGroups={"list"}, statusCode=201)
     *
     * @return \FOS\RestBundle\View\View
     */
    public function postProcessparticipationCommentsAction(Request $request, ProcessParticipation $processParticipation)
    {
        $result = $this->getProcessParticipationManager()->postComment($processParticipation, $request);

        return $result;
    }

    /**
     * Edit  comment.
     *
     * @param Request              $request
     * @param ProcessParticipation $processParticipation
     * @param Comment              $comment
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Edit comment",
     *                                                   statusCodes={
     *                                                   204="Returned when successful",
     *                                                   400={
     *                                                   "Returned when process participation not found",
     *                                                   "Returned when comment not found",
     *                                                   "Returned when comment not belongs to process participation",
     *                                                   },
     *                                                   401={
     *                                                   "Returned when user is not logged"
     *                                                   },
     *                                                   500={
     *                                                   "Returned when debate is not open",
     *                                                   "Parent is not consistent"
     *                                                   }
     *                                                   },
     *                                                   parameters={
     *                                                   {"name"="comment[title]", "dataType"="string", "required"=false, "description"="comment title"},
     *                                                   {"name"="comment[comment]", "dataType"="string", "required"=false, "description"="comment description"}
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   },
     *                                                   {
     *                                                   "name"="comment_id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Comment id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\Put("/processparticipations/{id}/comments/{comment_id}")
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @ParamConverter("comment", class="Demofony2AppBundle:Comment", options={"id" = "comment_id"})
     * @Rest\View(statusCode=204)
     * @Security("has_role('ROLE_USER') && user === comment.getAuthor()")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function putProcessparticipationCommentsAction(
        Request $request,
        ProcessParticipation $processParticipation,
        Comment $comment
    ) {
        $result = $this->getProcessParticipationManager()->putComment($processParticipation, $comment, $request);

        return $result;
    }

    /**
     * Like  comment.
     *
     * @param Request              $request
     * @param ProcessParticipation $processParticipation
     * @param Comment              $comment
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Like comment",
     *                                                   statusCodes={
     *                                                   201="Returned when successful",
     *                                                   400={
     *                                                   "Returned when process participation not found",
     *                                                   "Returned when comment not found",
     *                                                   "Returned when comment not belongs to process participation",
     *                                                   },
     *                                                   401={
     *                                                   "Returned when user is not logged"
     *                                                   },
     *                                                   500={
     *                                                   "Returned when debate is not open",
     *                                                   "Returned when user already voted"
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   },
     *                                                   {
     *                                                   "name"="comment_id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Comment id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\Post("/processparticipations/{id}/comments/{comment_id}/like")
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @ParamConverter("comment", class="Demofony2AppBundle:Comment", options={"id" = "comment_id"})
     * @Rest\View(serializerGroups={"list"}, statusCode=201)
     * @Security("has_role('ROLE_USER')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function postProcessparticipationCommentsLikeAction(
        Request $request,
        ProcessParticipation $processParticipation,
        Comment $comment
    ) {
        $result = $this->getProcessParticipationManager()->likeComment($processParticipation, $comment);

        return $result;
    }

    /**
     * Unlike  comment.
     *
     * @param Request              $request
     * @param ProcessParticipation $processParticipation
     * @param Comment              $comment
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Unlike comment",
     *                                                   statusCodes={
     *                                                   201="Returned when successful",
     *                                                   400={
     *                                                   "Returned when process participation not found",
     *                                                   "Returned when comment not found",
     *                                                   "Returned when comment not belongs to process participation",
     *                                                   },
     *                                                   401={
     *                                                   "Returned when user is not logged"
     *                                                   },
     *                                                   500={
     *                                                   "Returned when debate is not open",
     *                                                   "Returned when user already voted"
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   },
     *                                                   {
     *                                                   "name"="comment_id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Comment id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\Post("/processparticipations/{id}/comments/{comment_id}/unlike")
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @ParamConverter("comment", class="Demofony2AppBundle:Comment", options={"id" = "comment_id"})
     * @Rest\View(serializerGroups={"list"}, statusCode=201)
     * @Security("has_role('ROLE_USER')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function postProcessparticipationCommentsUnlikeAction(
        Request $request,
        ProcessParticipation $processParticipation,
        Comment $comment
    ) {
        $result = $this->getProcessParticipationManager()->unLikeComment($processParticipation, $comment);

        return $result;
    }

    /**
     * Delete Like  comment.
     *
     * @param Request              $request
     * @param ProcessParticipation $processParticipation
     * @param Comment              $comment
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Delete Like comment",
     *                                                   statusCodes={
     *                                                   201="Returned when successful",
     *                                                   400={
     *                                                   "Returned when process participation not found",
     *                                                   "Returned when comment not found",
     *                                                   "Returned when comment not belongs to process participation",
     *                                                   },
     *                                                   401={
     *                                                   "Returned when user is not logged"
     *                                                   },
     *                                                   500={
     *                                                   "Returned when debate is not open",
     *                                                   "Returned when user already voted"
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   },
     *                                                   {
     *                                                   "name"="comment_id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Comment id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\Delete("/processparticipations/{id}/comments/{comment_id}/like")
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @ParamConverter("comment", class="Demofony2AppBundle:Comment", options={"id" = "comment_id"})
     * @Rest\View(serializerGroups={"list"}, statusCode=201)
     * @Security("has_role('ROLE_USER')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteProcessparticipationCommentsLikeAction(
        Request $request,
        ProcessParticipation $processParticipation,
        Comment $comment
    ) {
        $result = $this->getProcessParticipationManager()->deleteLikeComment($processParticipation, $comment, $this->getUser());

        return $result;
    }

    /**
     * Delete Unlike  comment.
     *
     * @param Request              $request
     * @param ProcessParticipation $processParticipation
     * @param Comment              $comment
     * @ApiDoc(
     *                                                   section="Process Participation",
     *                                                   resource=true,
     *                                                   description="Delete Unlike comment",
     *                                                   statusCodes={
     *                                                   201="Returned when successful",
     *                                                   400={
     *                                                   "Returned when process participation not found",
     *                                                   "Returned when comment not found",
     *                                                   "Returned when comment not belongs to process participation",
     *                                                   },
     *                                                   401={
     *                                                   "Returned when user is not logged"
     *                                                   },
     *                                                   500={
     *                                                   "Returned when debate is not open",
     *                                                   "Returned when user already voted"
     *                                                   }
     *                                                   },
     *                                                   requirements={
     *                                                   {
     *                                                   "name"="id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Process participation id"
     *                                                   },
     *                                                   {
     *                                                   "name"="comment_id",
     *                                                   "dataType"="integer",
     *                                                   "requirement"="\d+",
     *                                                   "description"="Comment id"
     *                                                   }
     *                                                   }
     *                                                   )
     * @Rest\Delete("/processparticipations/{id}/comments/{comment_id}/unlike")
     * @ParamConverter("processParticipation", class="Demofony2AppBundle:ProcessParticipation")
     * @ParamConverter("comment", class="Demofony2AppBundle:Comment", options={"id" = "comment_id"})
     * @Rest\View(serializerGroups={"list"}, statusCode=201)
     * @Security("has_role('ROLE_USER')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteProcessparticipationCommentsUnLikeAction(
        Request $request,
        ProcessParticipation $processParticipation,
        Comment $comment
    ) {
        $result = $this->getProcessParticipationManager()->deleteUnlikeComment($processParticipation, $comment, $this->getUser());

        return $result;
    }

    protected function getProcessParticipationManager()
    {
        return $this->get('app.process_participation');
    }
}
