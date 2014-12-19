<?php

namespace Flexsounds\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Flexsounds\TicketBundle\Entity\Ticket;
use Flexsounds\TicketBundle\Entity\TicketMessage;
use Flexsounds\TicketBundle\Form\Type\TicketType;
use Flexsounds\TicketBundle\Form\Type\TicketMessageType;
use Flexsounds\TicketBundle\TicketEvents;
use Flexsounds\TicketBundle\Event\TicketEvent;

/**
 * Ticket controller.
 *
 */
class TicketController extends Controller
{

    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction(Request $request)
    {
        $userManager = $this->get('flexsounds_ticket.user');
        $translator = $this->get('translator');

        $ticketState = $request->get('state', $translator->trans('STATUS_OPEN'));
        

        $repositoryTicket = $this->getDoctrine()->getRepository('FlexsoundsTicketBundle:Ticket');

        $repositoryTicketMessage = $this->getDoctrine()->getRepository('FlexsoundsTicketBundle:TicketMessage');

        $query = $repositoryTicket->getTicketList($userManager, $repositoryTicketMessage->getTicketStatus($translator, $ticketState));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('FlexsoundsTicketBundle:Ticket:index.html.twig', array(
                    'pagination' => $pagination,
                    'ticketState' => $ticketState,
        ));
    }

    /**
     * Creates a new Ticket entity.
     *
     */
    public function createAction(Request $request)
    {
        $userManager = $this->get('flexsounds_ticket.user');
        $ticketManager = $this->get('flexsounds_ticket.ticket_manager');

        $ticket = $ticketManager->createTicket();
        $form = $this->createForm(new TicketType($userManager), $ticket);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $message = $ticket->getMessages()->current();
            $message->setStatus(TicketMessage::STATUS_OPEN)
                    ->setUser($userManager->getCurrentUser())
                    ->setTicket($ticket)
            ;

            $ticketManager->updateTicket($ticket, $message);

            $event = new TicketEvent($ticket);
            $this->get('event_dispatcher')->dispatch(TicketEvents::TICKET_CREATE, $event);

            return $this->redirect($this->generateUrl('flexsounds_ticket_show', array('id' => $ticket->getId())));
        }

        return $this->render('FlexsoundsTicketBundle:Ticket:new.html.twig', array(
                    'entity' => $ticket,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Ticket entity.
     *
     */
    public function newAction()
    {
        $entity = new Ticket();
        $userManager = $this->get('flexsounds_ticket.user');
        $form = $this->createForm(new TicketType($userManager), $entity);

        return $this->render('FlexsoundsTicketBundle:Ticket:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction(Ticket $ticket=null)
    {
        if(!$ticket){
            return $this->redirect($this->generateUrl('flexsounds_ticket'));

        }
        $userManager = $this->get('flexsounds_ticket.user');
        $this->checkUserPermission($userManager->getCurrentUser(), $ticket);

        $data = array('ticket' => $ticket);

        $message = new TicketMessage();
        $message->setPriority($ticket->getPriority());
        $message->setStatus($ticket->getStatus());

        if (TicketMessage::STATUS_CLOSED != $ticket->getStatus()) {
            $data['form'] = $this->createForm(new TicketMessageType($userManager), $message)->createView();
        }

        if ($this->get('flexsounds_ticket.user')->hasRole($userManager->getCurrentUser(), 'ROLE_TICKET_ADMIN')) {
            $data['delete_form'] = $this->createDeleteForm($ticket->getId())->createView();
        }

        return $this->render('FlexsoundsTicketBundle:Ticket:show.html.twig', $data);
    }

    private function checkUserPermission($user, $ticket)
    {
        if (!\is_object($user) || (!$this->get('flexsounds_ticket.user')->hasRole($user, 'ROLE_TICKET_ADMIN') && $ticket->getUserCreated() != $user->getId())) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function replyAction(Request $request, Ticket $ticket)
    {
        $userManager = $this->get('flexsounds_ticket.user');
        $ticketManager = $this->get('flexsounds_ticket.ticket_manager');

        $user = $userManager->getCurrentUser();
        $this->checkUserPermission($user, $ticket);

        $message = $ticketManager->createMessage();
        $message->setPriority($ticket->getPriority());

        $form = $this->createForm(new TicketMessageType($userManager), $message);
        $form->submit($request);

        if ($form->isValid()) {

            $message->setUser($user);
            $message->setTicket($ticket);

            $ticketManager->updateTicket($ticket, $message);
            
            $event = new TicketEvent($ticket);
            $this->get('event_dispatcher')->dispatch(TicketEvents::TICKET_UPDATE, $event);

            return $this->redirect($this->generateUrl('flexsounds_ticket_show', array('id' => $ticket->getId())));
        }

        return $this->showAction($ticket);
    }

    /**
     * Deletes a Ticket entity.
     *
     */
    public function deleteAction(Request $request, Ticket $ticket)
    {
        $userManager = $this->get('flexsounds_ticket.user');
        $user = $userManager->getCurrentUser();

        if (!\is_object($user) || !$userManager->hasRole($user, 'ROLE_TICKET_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $form = $this->createDeleteForm($ticket->getId());
        $form->submit($request);

        if ($form->isValid()) {
            if (!$ticket) {
                throw $this->createNotFoundException($this->get('translator')->trans('ERROR_FIND_TICKET_ENTITY'));
            }

            $ticketManager = $this->get('flexsounds_ticket.ticket_manager');
            $ticketManager->deleteTicket($ticket);
            $event = new TicketEvent($ticket);
            $this->get('event_dispatcher')->dispatch(TicketEvents::TICKET_DELETE, $event);

        }

        return $this->redirect($this->generateUrl('flexsounds_ticket'));
    }

    /**
     * Creates a form to delete a Ticket entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
