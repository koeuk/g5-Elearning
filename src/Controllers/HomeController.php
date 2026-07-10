<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Course;

/**
 * Public landing page ("/").
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $this->public('home/home', [
            'categories' => Category::allWithCourseCount(),
            'courses'    => Course::allWithTrainer(),
        ]);
    }
}
