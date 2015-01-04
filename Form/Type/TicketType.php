<?php

namespace Flexsounds\TicketBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Hackzilla\Interfaces\User\UserInterface;

class TicketType extends AbstractType
{

    private $_userManager;

    public function __construct(UserInterface $userManager)
    {
        $this->_userManager = $userManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('requester', null,  array(
                    'property' => 'fullName',
                    'empty_value' => '-Choose a requester-'
                ))
                ->add('subject')
                ->add('messages', 'collection', array(
                    'type' => new TicketMessageType($this->_userManager, true),
                    'label' => false,
                    'allow_add' => true,
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flexsounds\TicketBundle\Entity\Ticket'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'flexsounds_bundle_ticketbundle_tickettype';
    }

}
