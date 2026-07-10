<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

/**
 * Authenticated student landing page ("/student").
 *
 * A deliberately small dashboard for now: the full 537-line legacy course-grid
 * home (resources/views/students/home.view.php) is a later slice. What matters
 * here is that the page identifies the student from the session (Auth::user())
 * rather than a posted email, and refuses access when nobody is signed in.
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::role() !== User::ROLE_STUDENT) {
            $this->redirect('/signin');
        }

        $this->view('students/dashboard', [
            'student' => Auth::user() ?? [],
        ]);
    }
}
