<?php

namespace Flexsounds\TicketBundle\Entity;

interface TicketManagerInterface{

	public function createTicket();

	public function createMessage();

	public function updateTicket(Ticket $ticket);

	public function deleteTicket(Ticket $ticket);

	public function findTickets();

	public function findTicketsBy(array $criteria);

}