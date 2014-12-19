<?php

namespace Flexsounds\TicketBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Flexsounds\TicketBundle\Entity\TicketManagerInterface;
use Flexsounds\TicketBundle\Entity\Ticket;
use Flexsounds\TicketBundle\Entity\TicketMessage;

class TicketManager implements TicketManagerInterface {

	public function __construct(ObjectManager $om){
		$this->ObjectManager = $om;
		$this->repository = $om->getRepository('FlexsoundsTicketBundle:Ticket');
	}

	/*
	Create a new instance of Ticket entity
	*/
	public function createTicket(){
		return new Ticket;
	}

	/*
	Create a new instance of TicketMessage Entity
	*/
	public function createMessage(){
		return new TicketMessage;
	}

	/*
	Update or Create a Ticket in the database
	Update or Create a TicketMessage in the database
	*/
	public function updateTicket(Ticket $ticket, TicketMessage $message=null){
		if(!\is_null($ticket)){
			$this->ObjectManager->persist($ticket);
		}
		if(!\is_null($message)){
			$this->ObjectManager->persist($message);
		}
		$this->ObjectManager->flush();
		return $ticket;
	}

	/*
	Delete a ticket from the database
	*/
	public function deleteTicket(Ticket $ticket){

		foreach($ticket->getMessages() as $message){
			$this->ObjectManager->remove($message);
		}

		$this->ObjectManager->remove($ticket);
		$this->ObjectManager->flush();
	}

	/*
	Find all tickets in the database
	*/
	public function findTickets(){
		return $this->repository->findAll();
	}

	/*
	Find ticket by criteria
	*/
	public function findTicketsBy(array $criteria){
		return $this->repository->findBy($criteria);
	}

}