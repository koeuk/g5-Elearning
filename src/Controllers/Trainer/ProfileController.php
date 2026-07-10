<?php

namespace App\Controllers\Trainer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validation;
use App\Models\User;

/**
 * Trainer self-service: edit profile ("/trainer_edits") and change password
 * ("/trainer_password_comfirm").
 *
 * Replaces app/controllers/trainers/edit.controller + passwords/getnew.
 * The acting trainer is taken from the session (Auth::id()) rather than a
 * posted user_id, so a trainer can only ever edit their own account.
 */
final class ProfileController extends Controller
{
    private const UPLOAD_DIR = __DIR__ . '/../../../public/uploading/';

    /** POST /trainer_edits — update name / email / phone / optional avatar. */
    public function update(): void
    {
        $trainer = $this->requireTrainer();

        $name  = $this->input('name');
        $email = $this->input('email');
        $phone = $this->input('phone');

        if ($name === '' || $email === '') {
            Session::flash('error', 'Name and email are required.');
            $this->redirect('/trainer');
        }

        // Keep the existing avatar unless a new file was uploaded.
        $image = (string) ($trainer['profile_image'] ?? '');
        $uploaded = $this->storeUpload('image');
        if ($uploaded !== null) {
            $image = $uploaded;
        }

        User::update(
            (int) $trainer['user_id'],
            $name,
            $email,
            $phone,
            (string) ($trainer['gender'] ?? ''),
            $image
        );

        // Keep the session copy in step with the edited name/email.
        Auth::login(User::find((int) $trainer['user_id']) ?: $trainer);
        Session::flash('success', 'Profile updated.');
        $this->redirect('/trainer');
    }

    /** POST /trainer_password_comfirm — verify current password and set a new one. */
    public function updatePassword(): void
    {
        $trainer = $this->requireTrainer();

        $errors = Validation::passwordChange(
            (string) $trainer['password'],
            $this->input('currentPassword'),
            $this->input('newPassword'),
            $this->input('confirmPassword')
        );

        if (!Validation::passes($errors)) {
            Session::flash('error', 'Password change failed: ' . implode(' ', array_filter($errors)));
            $this->redirect('/trainer');
        }

        User::setPassword((int) $trainer['user_id'], $this->input('newPassword'));
        Session::flash('success', 'Password changed.');
        $this->redirect('/trainer');
    }

    /** @return array<string, mixed> the logged-in trainer's full row */
    private function requireTrainer(): array
    {
        if (Auth::role() !== User::ROLE_TRAINER) {
            $this->redirect('/trainer_signin');
        }
        $trainer = User::find((int) Auth::id());
        if ($trainer === false) {
            Auth::logout();
            $this->redirect('/trainer_signin');
        }
        return $trainer;
    }

    /**
     * Move an uploaded file into public/uploading and return its stored name,
     * or null when no (valid) file was submitted.
     */
    private function storeUpload(string $field): ?string
    {
        if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $name = basename((string) $_FILES[$field]['name']);
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        return move_uploaded_file($_FILES[$field]['tmp_name'], self::UPLOAD_DIR . $name) ? $name : null;
    }
}
