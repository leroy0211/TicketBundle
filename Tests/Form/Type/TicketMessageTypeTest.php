<?php

namespace Flexsounds\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Flexsounds\TicketBundle\Entity\TicketMessage;

class TicketMessageTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'priority' => TicketMessage::PRIORITY_HIGH,
            'message'  => null,
        );

        $userManager = $this->getMock('Hackzilla\Interfaces\User\UserInterface');
        $this->assertTrue($userManager instanceof \Hackzilla\Interfaces\User\UserInterface);
      
        $type = new \Flexsounds\TicketBundle\Form\Type\TicketMessageType($userManager, true);

        $data = new \Flexsounds\TicketBundle\Entity\TicketMessage();
        $data->setPriority(TicketMessage::PRIORITY_HIGH);
        
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
