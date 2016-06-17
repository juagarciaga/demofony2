<?php

namespace Demofony2\UserBundle\Controller;

use Demofony2\AppBundle\Enum\UserRolesEnum;
use Demofony2\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Demofony2\UserBundle\Form\Type\ProfileFormType;
use FOS\UserBundle\Controller\ProfileController as FOSProfileController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

/**
 * Controller managing the user profile.
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends FOSProfileController
{
    const ITEMS_BY_PAGE = 10;

    /**
     * @param Request $request
     * @param string  $username
     * @param int     $comments
     * @param int     $page
     *
     * @return Response
     */
    public function showPublicProfileAction(Request $request, $username, $comments = 1, $proposals = 1)
    {
        $user = $this->container->get('app.user')->findByUsername($username);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('Not Found');
        }

        if (!$this->container->get('security.authorization_checker')->isGranted(
                UserRolesEnum::ROLE_ADMIN,
                $user
            ) && !$user->isEnabled()
        ) {
            throw new NotFoundHttpException('Not Found');
        }

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        $commentsQueryBuilder = $em->getRepository('Demofony2AppBundle:Comment')->queryByUser($user);
        $proposalsQueryBuilder = $em->getRepository('Demofony2AppBundle:Proposal')->queryByUserProfileAndUserLogged(
            $user,
            $this->getUser()
        );

        $pagination = $this->container->get('app.pagination');
        $pagination->setItemsByPage(self::ITEMS_BY_PAGE)
            ->setFirstParamName('comments')
            ->setSecondParamName('proposals')
            ->setFirstPaginationPage($comments)
            ->setSecondPaginationPage($proposals)
            ->setSecondPaginationQueryBuilder($proposalsQueryBuilder)
            ->setFirstPaginationQueryBuilder($commentsQueryBuilder)
            ->setFirstPaginationRoute('fos_user_profile_public_show_comments')
            ->setSecondPaginationRoute('fos_user_profile_public_show_proposals');
        list($comments, $proposals, $isOpenTab) = $pagination->getDoublePagination();


        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Profile:show.html.twig',
            array(
                'user' => $user,
                'comments' => $comments,
                'proposals' => $proposals,
                'open' => $isOpenTab
            )
        );
    }

    public function editAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->container->get('form.factory')->create(new ProfileFormType(), $user);

//        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
//        $formFactory = $this->container->get('fos_user.profile.form.factory');
//
//        $form = $formFactory->createForm();
//        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_public_show_proposals');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(
                    FOSUserEvents::PROFILE_EDIT_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Profile:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    public function getUser()
    {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

    public function getToken()
    {
        return $this->container->get('security.token_storage')->getToken();
    }
}
