<?php

namespace App\Controllers\Trainer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validation;
use App\Models\User;

/**
 * Trainer authentication: the sign-in form ("/trainer_signin"), the credential
 * check ("/trainer_access") and logout ("/trainer_logout").
 *
 * Replaces app/controllers/trainers/signin/{login,access}. The legacy
 * re-hash "verification" is gone — credentials go through Validation::signin
 * with the trainer role, and the logged-in trainer is kept in the session
 * instead of threading $_POST['email'] through every page.
 */
final class AuthController extends Controller
{
    /** GET /trainer_signin — show the sign-in form (or skip it if already signed in). */
    public function showLogin(): void
    {
        if (Auth::role() === User::ROLE_TRAINER) {
            $this->redirect('/trainer');
        }

        $this->view('trainers/login', [
            'errors' => ['email' => '', 'password' => ''],
            'old'    => ['email' => ''],
        ]);
    }

    /** POST /trainer_access — verify credentials and start the trainer session. */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/trainer_signin');
        }

        $email    = $this->input('email');
        $password = $this->input('password');

        $errors = Validation::signin($email, $password, User::ROLE_TRAINER);

        if (!Validation::passes($errors)) {
            $this->view('trainers/login', [
                'errors' => $errors,
                'old'    => ['email' => $email],
            ]);
            return;
        }

        $trainer = User::findByEmail($email, User::ROLE_TRAINER);

        // Transparently upgrade legacy plaintext / outdated hashes on login.
        if (Auth::needsRehash((string) $trainer['password'])) {
            User::setPassword((int) $trainer['user_id'], $password);
        }

        Auth::login($trainer);
        Session::flash('success', 'Login successful. Welcome, ' . ($trainer['name'] ?? '') . '!');
        $this->redirect('/trainer');
    }

    /** GET /trainer_logout — clear the session and return to the sign-in form. */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/trainer_signin');
    }
}
