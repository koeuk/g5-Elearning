<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validation;
use App\Models\User;

/**
 * Admin change-password ("/admin_password" form, "/admin_password_comfirm"
 * handler). Replaces app/controllers/admin/passwords/*.
 *
 * The current password is verified through Auth (not the old plaintext ===
 * compare) and the new one is stored hashed.
 */
final class PasswordController extends Controller
{
    /** GET /admin_password — show the change-password form. */
    public function edit(): void
    {
        $this->guard();
        $this->view('admin/password', ['input' => false, 'require' => self::blank()]);
    }

    /** POST /admin_password_comfirm — verify and update the password. */
    public function update(): void
    {
        $admin = $this->guard();
        $row   = User::find((int) $admin['user_id']);

        $errors = Validation::passwordChange(
            (string) ($row['password'] ?? ''),
            (string) ($_POST['currentPassword'] ?? ''),
            (string) ($_POST['newPassword'] ?? ''),
            (string) ($_POST['confirmPassword'] ?? '')
        );

        if (Validation::passes($errors)) {
            User::setPassword((int) $admin['user_id'], (string) $_POST['newPassword']);
            $this->redirect('/admin_home');
        }

        $this->view('admin/password', ['input' => true, 'require' => $errors]);
    }

    /** @return array<string, mixed> the signed-in admin */
    private function guard(): array
    {
        $admin = Auth::user();
        if (Auth::role() !== User::ROLE_ADMIN || $admin === null) {
            $this->redirect('/admin_signin');
        }
        return $admin;
    }

    /** @return array<string, string> */
    private static function blank(): array
    {
        return ['currentPassword' => '', 'newPassword' => '', 'confirmPassword' => ''];
    }
}
