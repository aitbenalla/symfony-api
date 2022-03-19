<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(): ?\Symfony\Component\Security\Core\User\UserInterface
    {
        dd($this->security->getUser());
        return $this->security->getUser();
    }
}