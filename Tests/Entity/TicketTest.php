<?php

namespace Flexsounds\TicketBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $this->_object = new \Flexsounds\TicketBundle\Entity\Ticket();
    }

    public function tearDown()
    {
        unset($this->_object);
    }

    public function testObjectCreated()
    {
        $this->assertTrue(\is_object($this->_object));
    }
}
