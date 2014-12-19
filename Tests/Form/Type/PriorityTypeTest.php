<?php

namespace Flexsounds\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class PriorityTypeTest extends TypeTestCase
{
    private $_object;

    public function setUp()
    {
        $this->_object = new \Flexsounds\TicketBundle\Form\Type\PriorityType();
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
