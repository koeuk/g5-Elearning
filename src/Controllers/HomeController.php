<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Public landing page ("/").
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $this->public('home/home');
    }
}
