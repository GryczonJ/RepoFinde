<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
//use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    #[Route('/', name: 'home')]
    public function index()
    {
        echo "Hello, World!";
    }
}