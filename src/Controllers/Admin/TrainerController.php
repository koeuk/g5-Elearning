<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validation;
use App\Models\User;

/**
 * Admin trainer management ("/list_trainer" + add/delete handlers).
 * Replaces app/controllers/admin/trainer/*.
 */
final class TrainerController extends Controller
{
    /** GET /list_trainer — list all trainers. */
    public function index(): void
    {
        $this->guard();
        $this->admin('admin/trainer/trainer', ['trainers' => User::trainers()]);
    }

    /** POST /add_trainer — create a trainer account. */
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
            User::ROLE_TRAINER
        );
        $this->redirect('/list_trainer');
    }

    /** POST /delete_trainer — remove a trainer. */
    public function destroy(): void
    {
        $this->guard();
        User::delete((int) $this->input('id'));
        $this->redirect('/list_trainer');
    }

    /**
     * POST /update_trainer — edit a trainer's details (name/email/phone/image).
     * Blank fields fall back to the existing value; a duplicate email is ignored
     * so the users.email UNIQUE constraint can't throw.
     */
    public function update(): void
    {
        $this->guard();

        $id = (int) $this->input('id');
        if ($id <= 0) {
            $this->redirect('/list_trainer');
        }

        $current = User::find($id) ?: [];
        $image   = empty($_FILES['image']['name'])
            ? (string) ($current['profile_image'] ?? 'non.webp')
            : $this->uploadImage();

        $name  = $this->input('name')  ?: (string) ($current['name'] ?? '');
        $phone = $this->input('phone') ?: (string) ($current['phone'] ?? '');
        $email = $this->input('email') ?: (string) ($current['email'] ?? '');

        $owner = User::findByEmail($email);
        if (!empty($owner) && (int) $owner['user_id'] !== $id) {
            $email = (string) ($current['email'] ?? '');
        }

        User::update($id, $name, $email, $phone, (string) ($current['gender'] ?? 'Male'), $image);
        Session::flash('trainer_pw', ['type' => 'success', 'message' => 'Trainer details updated.']);
        $this->redirect('/list_trainer');
    }

    /**
     * POST /updateTrainerPassword — admin override of a trainer's password.
     * The new password must be strong and confirmed.
     */
    public function updatePassword(): void
    {
        $this->guard();

        $id      = (int) $this->input('id');
        $new     = (string) ($_POST['newPassword'] ?? '');
        $confirm = (string) ($_POST['confirmPassword'] ?? '');

        if ($id <= 0) {
            $this->redirect('/list_trainer');
        }

        if ($new !== $confirm) {
            Session::flash('trainer_pw', ['type' => 'error', 'message' => 'Passwords do not match.']);
        } elseif (!Validation::passes(Validation::strongPassword($new))) {
            Session::flash('trainer_pw', ['type' => 'error', 'message' => 'Password must be at least 8 characters with a letter, number and symbol.']);
        } else {
            User::setPassword($id, $new);
            Session::flash('trainer_pw', ['type' => 'success', 'message' => 'Password updated.']);
        }

        $this->redirect('/list_trainer');
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
