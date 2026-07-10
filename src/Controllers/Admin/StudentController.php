<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validation;
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

    /**
     * POST /updateStudentPassword — admin override of a student's password.
     * No current-password check (admin action); the new one must be strong and
     * confirmed. Errors are surfaced back on the list via a flash message.
     */
    public function updatePassword(): void
    {
        $this->guard();

        $id      = (int) $this->input('id');
        $new     = (string) ($_POST['newPassword'] ?? '');
        $confirm = (string) ($_POST['confirmPassword'] ?? '');

        if ($id <= 0) {
            $this->redirect('/list_student');
        }

        if ($new !== $confirm) {
            $this->flash('Passwords do not match.');
        } elseif (!Validation::passes(Validation::strongPassword($new))) {
            $this->flash('Password must be at least 8 characters with a letter, number and symbol.');
        } else {
            User::setPassword($id, $new);
            $this->flash('Password updated.', 'success');
        }

        $this->redirect('/list_student');
    }

    /** Stash a one-time message for the next page render. */
    private function flash(string $message, string $type = 'error'): void
    {
        \App\Core\Session::flash('student_pw', ['type' => $type, 'message' => $message]);
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
