<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;

/**
 * Student classroom for a purchased course ("/blog_learning").
 *
 * Reached from the "Join course" / "Open course" buttons, which POST a
 * course_id. Replaces app/controllers/courses/blog_learning.controller. Shows
 * the course's lessons (with videos), its quizzes, and the trainer; a POST with
 * an uploaded image records a quiz answer for the selected lesson.
 */
final class ClassroomController extends Controller
{
    public function show(): void
    {
        if (Auth::role() !== User::ROLE_STUDENT) {
            $this->redirect('/signin');
        }

        $student   = Auth::user() ?? [];
        $studentId = (int) ($student['user_id'] ?? 0);
        $courseId  = (int) $this->input('course_id');

        if ($this->isPost() && $studentId > 0) {
            $this->submitQuizAnswer($studentId);
        }

        $course  = Course::find($courseId) ?: [];
        $teacher = isset($course['user_id']) ? (User::find((int) $course['user_id']) ?: []) : [];

        $this->view('courses/blog_learning', [
            'student'  => $student,
            'course'   => $course,
            'courseId' => $courseId,
            'lessons'  => $this->lessonsWithQuizzes($courseId),
            'teacher'  => $teacher,
        ]);
    }

    /**
     * Lessons for the course, each carrying its quizzes.
     *
     * @return array<int, array>
     */
    private function lessonsWithQuizzes(int $courseId): array
    {
        $lessons = Lesson::forCourse($courseId);
        foreach ($lessons as $i => $lesson) {
            $lessons[$i]['quizzes'] = Quiz::forLesson((int) $lesson['lesson_id']);
        }
        return $lessons;
    }

    /** Record an uploaded quiz answer image for the chosen lesson. */
    private function submitQuizAnswer(int $studentId): void
    {
        $lessonTitle = $this->input('lesson_select');
        if ($lessonTitle === '' || $lessonTitle === 'Select the lesson' || empty($_FILES['image']['name'])) {
            return;
        }

        $lesson = Lesson::findByTitle($lessonTitle);
        if ($lesson === false) {
            return;
        }

        $image = basename((string) $_FILES['image']['name']);
        if (QuizSubmission::existsByImage($image) !== []) {
            return;
        }

        move_uploaded_file($_FILES['image']['tmp_name'], 'uploading' . DIRECTORY_SEPARATOR . $image);
        QuizSubmission::create($studentId, (int) $lesson['lesson_id'], $image);
    }
}
