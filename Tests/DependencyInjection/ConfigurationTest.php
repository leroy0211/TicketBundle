<?php

namespace Flexsounds\TicketBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigurationTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $this->_object = new \Flexsounds\TicketBundle\DependencyInjection\Configuration();
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
