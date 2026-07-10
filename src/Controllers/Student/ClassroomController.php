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
 * the course's lessons (with videos), the per-lesson multiple-choice quizzes,
 * and the trainer. Submitting a quiz form auto-grades it and records the score.
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

        // A quiz submission carries quiz_lesson (the lesson being graded).
        $result = null;
        if ($this->isPost() && $studentId > 0 && $this->input('quiz_lesson') !== '') {
            $result = $this->gradeQuiz($studentId);
        }

        $course  = Course::find($courseId) ?: [];
        $teacher = isset($course['user_id']) ? (User::find((int) $course['user_id']) ?: []) : [];

        $this->view('courses/blog_learning', [
            'student'    => $student,
            'course'     => $course,
            'courseId'   => $courseId,
            'lessons'    => $this->lessonsWithQuestions($courseId),
            'teacher'    => $teacher,
            'quizResult' => $result,
        ]);
    }

    /**
     * Lessons for the course, each carrying its parsed MCQ questions in a
     * `quizzes` list (quiz_id, question, options, answer).
     *
     * @return array<int, array>
     */
    private function lessonsWithQuestions(int $courseId): array
    {
        $lessons = Lesson::forCourse($courseId);
        foreach ($lessons as $i => $lesson) {
            $lessons[$i]['quizzes'] = Quiz::questionsForLesson((int) $lesson['lesson_id']);
        }
        return $lessons;
    }

    /**
     * Auto-grade a submitted quiz and record the score.
     *
     * Expects: quiz_lesson (lesson id) and answers[quiz_id] = chosen option index.
     *
     * @return array{lesson_id:int, score:int, total:int, given:array<int,int>}|null
     */
    private function gradeQuiz(int $studentId): ?array
    {
        $lessonId  = (int) $this->input('quiz_lesson');
        $questions = Quiz::questionsForLesson($lessonId);
        if ($questions === []) {
            return null;
        }

        $answers = (array) ($_POST['answers'] ?? []);
        $given   = [];
        $score   = 0;
        foreach ($questions as $q) {
            $picked = isset($answers[$q['quiz_id']]) ? (int) $answers[$q['quiz_id']] : -1;
            $given[$q['quiz_id']] = $picked;
            if ($picked === $q['answer']) {
                $score++;
            }
        }

        $total = count($questions);
        QuizSubmission::record($studentId, $lessonId, $score, $total, date('Y-m-d H:i:s'));

        return ['lesson_id' => $lessonId, 'score' => $score, 'total' => $total, 'given' => $given];
    }
}
