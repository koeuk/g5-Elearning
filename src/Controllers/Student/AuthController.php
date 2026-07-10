<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validation;
use App\Models\User;

/**
 * Student sign-in and registration.
 *
 * Replaces the legacy pair of controllers whose "auth" was
 * `password_verify($input, password_hash($stored))` (a convoluted plaintext
 * compare) and which identified the logged-in student by threading
 * `$_POST['email']` through every subsequent page. Credentials now go through
 * Validation/Auth, and a successful login is recorded in the session via
 * Auth::login(), so downstream pages read Auth::user() instead of a posted email.
 *
 * Routes: GET /signin, POST /access, GET /signup, POST /create_student, GET /logout.
 */
final class AuthController extends Controller
{
    /** GET /signin — show the sign-in form. */
    public function showLogin(): void
    {
        $this->view('students/signin', [
            'submitted' => false,
            'errors'    => ['email' => '', 'password' => ''],
            'old'       => ['email' => ''],
        ]);
    }

    /** POST /access — verify credentials, start a session, land on the dashboard. */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/signin');
        }

        $email    = $this->input('email');
        $password = $this->rawPassword();

        $errors = Validation::signin($email, $password, User::ROLE_STUDENT);

        if (!Validation::passes($errors)) {
            $this->view('students/signin', [
                'submitted' => true,
                'errors'    => $errors,
                'old'       => ['email' => $email],
            ]);
            return;
        }

        $user = User::findByEmail($email, User::ROLE_STUDENT);

        // Transparently upgrade legacy plaintext / outdated hashes on login.
        if (Auth::needsRehash((string) $user['password'])) {
            User::setPassword((int) $user['user_id'], $password);
        }

        Auth::login($user);
        $this->redirect('/student');
    }

    /** GET /signup — show the registration form. */
    public function showRegister(): void
    {
        $this->view('students/signup', [
            'submitted' => false,
            'errors'    => $this->blankRegistrationErrors(),
            'old'       => ['name' => '', 'email' => '', 'phone' => '', 'gender' => ''],
        ]);
    }

    /** POST /create_student — validate, create the student, log them in. */
    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/signup');
        }

        $name     = $this->input('name');
        $email    = $this->input('email');
        $phone    = $this->input('phone');
        $gender   = $this->input('gender') === 'Male' ? 'Male' : 'Female';
        $password = $this->rawPassword();
        $confirm  = (string) ($_POST['password_comfirm'] ?? '');

        $errors = Validation::registration($name, $email, $password, $phone, $confirm);

        if (!Validation::passes($errors)) {
            $this->view('students/signup', [
                'submitted' => true,
                'errors'    => $errors,
                'old'       => ['name' => $name, 'email' => $email, 'phone' => $phone, 'gender' => $gender],
            ]);
            return;
        }

        $image = $this->storeProfileImage();
        User::create($name, $email, $password, $phone, $gender, $image, User::ROLE_STUDENT);

        Auth::login(User::findByEmail($email, User::ROLE_STUDENT));
        $this->redirect('/student');
    }

    /** GET /logout — clear the session and return to the public site. */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }

    /**
     * Passwords are read raw (not trimmed like other inputs) so leading/trailing
     * characters are preserved exactly as typed.
     */
    private function rawPassword(): string
    {
        return (string) ($_POST['password'] ?? '');
    }

    /**
     * Move the uploaded profile image into uploading/ and return its stored
     * filename, or the default avatar when no file was provided. basename()
     * guards against path traversal in the client-supplied filename.
     */
    private function storeProfileImage(): string
    {
        $file = $_FILES['image'] ?? null;
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['name'] ?? '') === '') {
            return 'non.webp';
        }

        $name = basename((string) $file['name']);
        move_uploaded_file($file['tmp_name'], 'uploading' . DIRECTORY_SEPARATOR . $name);

        return $name;
    }

    /** @return array<string, string> */
    private function blankRegistrationErrors(): array
    {
        return ['name' => '', 'email' => '', 'password' => '', 'password_comfirm' => '', 'phone' => ''];
    }
}
