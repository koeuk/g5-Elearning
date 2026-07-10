<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

/**
 * Admin student management ("/list_student" + add/delete handlers).
 * Replaces app/controllers/admin/students/*.
 */
final class StudentController extends Controller
{
    /** GET /list_student — list all students. */
    public function index(): void
    {
        $this->guard();
        $this->admin('admin/students/student_view', ['students' => User::students()]);
    }

    /** POST /addStudent — create a student account. */
    public function store(): void
    {
        $this->guard();
        User::create(
            $this->input('name'),
            $this->input('email'),
            (string) ($_POST['password'] ?? ''),
            $this->input('phone'),
            $this->input('gender') === 'Male' ? 'Male' : 'Female',
            $this->uploadImage(),
            User::ROLE_STUDENT
        );
        $this->redirect('/list_student');
    }

    /** POST /deleteStudent — remove a student. */
    public function destroy(): void
    {
        $this->guard();
        User::delete((int) $this->input('id'));
        $this->redirect('/list_student');
    }

    private function guard(): void
    {
        $this->requireAdmin();
    }

    private function uploadImage(): string
    {
        if (empty($_FILES['image']['name'])) {
            return 'non.webp';
        }
        $name = basename((string) $_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploading' . DIRECTORY_SEPARATOR . $name);
        return $name;
    }
}
