<?php


namespace App\Entity;


interface ApiResourceEntityInterface
{
    public function getNotFoundMessage(): string;
}