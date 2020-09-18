<?php


namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface ApiEntityInterface
{
    public function getOwner(): ?UserInterface;
}