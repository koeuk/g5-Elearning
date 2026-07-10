<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\User;

/**
 * Admin category management ("/categories" + insert/edit/delete handlers).
 *
 * Replaces app/controllers/admin/category/*. The listing view no longer queries
 * inline — the controller passes the categories in — and every action is guarded
 * to admins only.
 */
final class CategoryController extends Controller
{
    /** GET /categories — list categories with add/edit/delete modals. */
    public function index(): void
    {
        $this->guard();
        $this->admin('admin/category/category', ['categories' => Category::all()]);
    }

    /** POST /insertCategory — create a category. */
    public function store(): void
    {
        $this->guard();
        Category::create($this->input('title'), $this->input('description'), $this->uploadImage());
        $this->redirect('/categories');
    }

    /** POST /editCategory — update a category (image optional). */
    public function update(): void
    {
        $this->guard();
        $id      = (int) $this->input('id');
        $current = Category::find($id);
        $image   = empty($_FILES['image']['name'])
            ? (string) ($current['image'] ?? '')
            : $this->uploadImage();

        Category::update($id, $this->input('title'), $this->input('description'), $image);
        $this->redirect('/categories');
    }

    /** POST /deleteCategory — delete a category. */
    public function destroy(): void
    {
        $this->guard();
        Category::delete((int) $this->input('id'));
        $this->redirect('/categories');
    }

    private function guard(): void
    {
        if (Auth::role() !== User::ROLE_ADMIN) {
            $this->redirect('/admin_signin');
        }
    }

    /** Move an uploaded image into public/uploading, or use the default. */
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
