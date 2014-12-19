<?php

namespace Flexsounds\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Flexsounds\TicketBundle\Entity\TicketMessage;

class TicketTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
        );

        $userManager = $this->getMock('Hackzilla\Interfaces\User\UserInterface');
        $this->assertTrue($userManager instanceof \Hackzilla\Interfaces\User\UserInterface);
      
        $type = new \Flexsounds\TicketBundle\Form\Type\TicketType($userManager, true);

        $data = new \Flexsounds\TicketBundle\Entity\Ticket();
        
        $form = $this->factory->create($type);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
