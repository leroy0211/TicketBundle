<?php

namespace Flexsounds\TicketBundle\Extension;

class UserExtension extends \Twig_Extension
{
    private $userManager;

    public function __construct($container) {
        $this->userManager = $container->get('flexsounds_ticket.user');
    }

    public function getFilters() {
        return array(
            'isTicketAdmin' => new \Twig_Filter_Method($this, 'isTicketAdmin'),
        );
    }

    public function isTicketAdmin($user, $role)
    {
        if (!is_object($user)) {
            $user = $this->userManager->getUserById($user);
        }

        return $user->hasRole($role);
    }

    public function getName()
    {
        return 'flexsounds_ticket_user_extension';
    }
}
