<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;

/**
 * Student profile ("/student_profile") and profile edit ("/get_edit").
 *
 * Replaces app/controllers/students/{profile,edit}. The profile subject is the
 * signed-in student (from the session); an admin may also open a specific
 * student's profile by posting their id.
 */
final class ProfileController extends Controller
{
    /** GET/POST /student_profile — show the profile + purchased courses. */
    public function show(): void
    {
        $id = $this->subjectId();
        if ($id === 0) {
            $this->redirect('/signin');
        }

        $this->view('students/profile', [
            'student' => User::find($id) ?: [],
            'courses' => $this->purchasedCourses($id),
            'isAdmin' => Auth::role() === User::ROLE_ADMIN,
        ]);
    }

    /** POST /get_edit — update the profile (image optional; password untouched). */
    public function update(): void
    {
        $isAdmin = Auth::role() === User::ROLE_ADMIN;
        $id      = $this->subjectId();
        if ($id === 0) {
            $this->redirect('/signin');
        }

        $current = User::find($id) ?: [];
        $image   = empty($_FILES['image']['name'])
            ? (string) ($current['profile_image'] ?? 'non.webp')
            : $this->uploadImage();

        // Fall back to the existing values when a field is left blank, so an
        // empty submit can't wipe the name/email/phone.
        $name  = $this->input('name')  ?: (string) ($current['name'] ?? '');
        $phone = $this->input('phone') ?: (string) ($current['phone'] ?? '');
        $email = $this->input('email') ?: (string) ($current['email'] ?? '');

        // Guard the users.email UNIQUE constraint: if the new email already
        // belongs to a different account, keep the current one instead of
        // letting the UPDATE throw a duplicate-key error.
        $owner = User::findByEmail($email);
        if (!empty($owner) && (int) $owner['user_id'] !== $id) {
            $email = (string) ($current['email'] ?? '');
        }

        User::update($id, $name, $email, $phone, (string) ($current['gender'] ?? 'Male'), $image);

        // Keep the session copy in sync when a student edits their own profile.
        if (!$isAdmin) {
            Auth::login(User::find($id) ?: []);
        }

        $this->redirect($isAdmin ? '/list_student' : '/student_profile');
    }

    /**
     * The user whose profile is shown: a specific id for admins, otherwise the
     * signed-in student.
     */
    private function subjectId(): int
    {
        if (Auth::role() === User::ROLE_ADMIN) {
            return (int) $this->input('id');
        }
        if (Auth::role() === User::ROLE_STUDENT) {
            return Auth::id() ?? 0;
        }
        return 0;
    }

    /**
     * Full course rows the student has paid for.
     *
     * @return array<int, array>
     */
    private function purchasedCourses(int $id): array
    {
        $rows = [];
        foreach (Payment::forUser($id) as $payment) {
            $course = Course::find((int) $payment['course_id']);
            if ($course !== false) {
                $rows[] = $course;
            }
        }
        return $rows;
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
