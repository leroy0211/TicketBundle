<?php

namespace Flexsounds\TicketBundle;

final class TicketEvents
{
    /**
     * The flexsounds.ticket.create event is thrown each time an ticket is created
     * in the system.
     *
     * The flexsounds.ticket.update event is thrown each time an ticket is updated
     * in the system.
     *
     * The flexsounds.ticket.delete event is thrown each time an ticket is deleted
     * in the system.
     *
     * The event listeners receives an
     * Flexsounds\TicketBundle\Event\TicketEvent instance.
     *
     * @var string
     */
    const TICKET_CREATE = 'flexsounds.ticket.create';
    const TICKET_UPDATE = 'flexsounds.ticket.update';
    const TICKET_DELETE = 'flexsounds.ticket.delete';
}
