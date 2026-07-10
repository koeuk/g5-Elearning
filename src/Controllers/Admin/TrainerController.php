<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
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
