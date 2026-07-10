<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;

/**
 * Admin course management ("/courses_as_admin", "/viewCourse", "/courseEdit"
 * and the create/update/delete handlers). Replaces app/controllers/admin/courses/*.
 *
 * The create/edit <select>s submit the category *title* and trainer *name*
 * (the option tags carry no value), so store()/update() resolve those back to
 * ids — matching the original getIdCategory()/getIdTrainer() behaviour.
 */
final class CourseController extends Controller
{
    /** GET /courses_as_admin, /viewCourse — course list + create modal. */
    public function index(): void
    {
        $this->guard();

        $this->admin('admin/courses/course', [
            'courses'    => $this->coursesWithLabels(),
            'categories' => Category::all(),
            'trainers'   => User::trainers(),
        ]);
    }

    /** POST /courseEdit — edit form for a single course. */
    public function edit(): void
    {
        $this->guard();

        $this->admin('admin/courses/course_edit', [
            'course'     => Course::find((int) $this->input('id')) ?: [],
            'categories' => Category::all(),
            'trainers'   => User::trainers(),
        ]);
    }

    /** POST /createCourse — create a course. */
    public function store(): void
    {
        $this->guard();

        $category = Category::findByTitle($this->input('category_id'));
        $trainer  = User::trainerIdByName($this->input('user_id'));

        if ($category !== false && $trainer !== false) {
            Course::create(
                $this->input('title'),
                $this->input('description'),
                (int) $category['category_id'],
                $this->input('date') ?: date('Y-m-d'),
                $this->uploadImage(),
                (int) $trainer['user_id'],
                $this->input('price')
            );
        }

        $this->redirect('/viewCourse');
    }

    /** POST /updateCourse — update a course (image optional). */
    public function update(): void
    {
        $this->guard();

        $id       = (int) $this->input('course_id');
        $category = Category::findByTitle($this->input('category_id'));
        $trainer  = User::trainerIdByName($this->input('user_id'));
        $catId    = $category !== false ? (int) $category['category_id'] : 0;
        $userId   = $trainer !== false ? (int) $trainer['user_id'] : 0;

        if (empty($_FILES['image']['name'])) {
            Course::updateWithoutImage($id, $this->input('title'), $this->input('description'), $userId, $catId, $this->input('price'));
        } else {
            Course::update($id, $this->input('title'), $this->input('description'), $userId, $catId, $this->input('price'), $this->uploadImage());
        }

        $this->redirect('/viewCourse');
    }

    /** POST /deleteCourse — delete a course. */
    public function destroy(): void
    {
        $this->guard();
        $id = (int) ($this->input('course_id') ?: $this->input('id') ?: ($_GET['id'] ?? 0));
        Course::delete($id);
        $this->redirect('/viewCourse');
    }

    /**
     * Course rows enriched with their category title and trainer name, resolved
     * with two lookup maps instead of a query per row.
     *
     * @return array<int, array>
     */
    private function coursesWithLabels(): array
    {
        $categoryTitle = [];
        foreach (Category::all() as $category) {
            $categoryTitle[(int) $category['category_id']] = $category['title'];
        }

        $trainerName = [];
        foreach (User::trainers() as $trainer) {
            $trainerName[(int) $trainer['user_id']] = $trainer['name'];
        }

        $rows = [];
        foreach (Course::all() as $course) {
            $rows[] = $course + [
                'category_title' => $categoryTitle[(int) $course['category_id']] ?? '',
                'trainer_name'   => $trainerName[(int) $course['user_id']] ?? '',
            ];
        }
        return $rows;
    }

    private function guard(): void
    {
        if (Auth::role() !== User::ROLE_ADMIN) {
            $this->redirect('/admin_signin');
        }
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
