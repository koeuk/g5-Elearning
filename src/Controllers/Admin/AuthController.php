<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validation;
use App\Models\User;

/**
 * Admin authentication: the welcome splash ("/admin"), the sign-in form
 * ("/admin_signin") and the credential check ("/admin_access").
 *
 * Replaces the legacy app/controllers/admin/start_admin + signin/{login,access}
 * controllers. The broken re-hash check in the old access.controller is gone —
 * verification now goes through App\Core\Auth via Validation::adminSignin.
 */
final class AuthController extends Controller
{
    /** GET /admin — welcome splash, also the logout landing page. */
    public function start(): void
    {
        Auth::logout();
        $this->view('admin/start_admin');
    }

    /** GET /admin_signin — show the sign-in form. */
    public function showLogin(): void
    {
        $this->view('admin/login', [
            'errors' => ['name' => '', 'password' => ''],
            'old'    => ['name' => ''],
        ]);
    }

    /** POST /admin_access — verify credentials and start the admin session. */
    public function login(): void
    {
        $name     = $this->input('name');
        $password = $this->input('password');

        $errors = Validation::adminSignin($name, $password);

        if (!Validation::passes($errors)) {
            $this->view('admin/login', [
                'errors' => $errors,
                'old'    => ['name' => $name],
            ]);
            return;
        }

        $admin = User::findAdminByName($name);

        // Transparently upgrade legacy plaintext / outdated hashes on login.
        if (Auth::needsRehash((string) $admin['password'])) {
            User::setPassword((int) $admin['user_id'], $password);
        }

        Auth::login($admin);
        Session::flash('success', 'Login successful. Welcome, ' . ($admin['name'] ?? 'Admin') . '!');
        $this->redirect('/admin_home');
    }
}
