<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validation;
use App\Models\User;

/**
 * Student change-password ("/student_password" form,
 * "/student_password_comfirm" handler). Replaces
 * app/controllers/students/passwords/*.
 *
 * The current password is verified through Auth and the new one is stored
 * hashed. The subject is always the signed-in student.
 */
final class PasswordController extends Controller
{
    /** GET/POST /student_password — show the change-password form. */
    public function edit(): void
    {
        $this->guard();
        $this->view('students/password', ['input' => false, 'require' => self::blank()]);
    }

    /** POST /student_password_comfirm — verify and update the password. */
    public function update(): void
    {
        $id  = $this->guard();
        $row = User::find($id) ?: [];

        $errors = Validation::passwordChange(
            (string) ($row['password'] ?? ''),
            (string) ($_POST['currentPassword'] ?? ''),
            (string) ($_POST['newPassword'] ?? ''),
            (string) ($_POST['confirmPassword'] ?? '')
        );

        if (Validation::passes($errors)) {
            User::setPassword($id, (string) ($_POST['newPassword'] ?? ''));
            $this->redirect('/student_profile');
        }

        $this->view('students/password', ['input' => true, 'require' => $errors]);
    }

    /** @return int the signed-in student's id */
    private function guard(): int
    {
        if (Auth::role() !== User::ROLE_STUDENT || Auth::id() === null) {
            $this->redirect('/signin');
        }
        return Auth::id();
    }

    /** @return array<string, string> */
    private static function blank(): array
    {
        return ['currentPassword' => '', 'newPassword' => '', 'confirmPassword' => ''];
    }
}
