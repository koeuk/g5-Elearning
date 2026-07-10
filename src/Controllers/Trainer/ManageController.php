<?php

namespace App\Controllers\Trainer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;

/**
 * Trainer course management ("/trainer_manage"): lessons, quizzes, quiz results
 * and enrolled students for one of the trainer's own courses.
 *
 * Replaces app/controllers/trainers/trainer.controller + the ~680-line
 * manage.view that ran everything through the legacy manage.model and threaded
 * $_POST['email'] / $_POST['course'] through every form. The acting trainer now
 * comes from the session, the course is validated to belong to them, and each
 * write goes through the Lesson / Quiz / QuizSubmission models.
 *
 * Writes use the POST → redirect → GET pattern so a refresh never re-submits.
 */
final class ManageController extends Controller
{
    public function show(): void
    {
        $trainer  = $this->requireTrainer();
        $courseId = (int) ($_POST['course'] ?? $_GET['course'] ?? 0);
        $course   = Course::find($courseId);

        // The course must exist and belong to the signed-in trainer.
        if ($course === false || (int) $course['user_id'] !== (int) $trainer['user_id']) {
            $this->redirect('/trainer');
        }

        if ($this->isPost()) {
            $this->handle($courseId);
            $this->redirect('/trainer_manage?course=' . $courseId);
        }

        $lessons = Lesson::forCourse($courseId);

        $this->view('trainers/manage', [
            'course'   => $course,
            'lessons'  => $lessons,
            'quizzes'  => $this->quizRows($lessons),
            'results'  => $this->resultRows($lessons),
            'students' => $this->studentRows($courseId),
        ]);
    }

    /** Route a write to the matching model call, then flash the outcome. */
    private function handle(int $courseId): void
    {
        switch ($this->input('action')) {
            case 'add_lesson':
                $title = $this->input('title');
                if ($title === '' || $this->input('video') === '') {
                    Session::flash('error', 'Lesson title and video are required.');
                    return;
                }
                if (Lesson::existsInCourse($courseId, $title) !== []) {
                    Session::flash('error', 'A lesson with that title already exists in this course.');
                    return;
                }
                Lesson::create($title, $this->input('description'), $this->input('video'), $courseId, $this->input('is_free') === '1');
                Session::flash('success', 'Lesson added.');
                return;

            case 'edit_lesson':
                Lesson::update(
                    (int) $this->input('lesson_id'),
                    $this->input('title'),
                    $this->input('description'),
                    $this->input('video'),
                    $this->input('is_free') === '1'
                );
                Session::flash('success', 'Lesson updated.');
                return;

            case 'delete_lesson':
                Lesson::delete((int) $this->input('lesson_id'));
                Session::flash('success', 'Lesson deleted.');
                return;

            case 'add_quiz':
                $qLesson  = (int) $this->input('lesson_id');
                $question = $this->input('question');
                [$options, $answer] = $this->cleanOptions();
                if ($qLesson <= 0 || $question === '' || count($options) < 2) {
                    Session::flash('error', 'Pick a lesson, enter a question and at least two options.');
                    return;
                }
                Quiz::addQuestion($qLesson, $question, $options, $answer);
                Session::flash('success', 'Question added.');
                return;

            case 'delete_quiz':
                Quiz::delete((int) $this->input('quiz_id'));
                Session::flash('success', 'Question deleted.');
                return;

            case 'delete_result':
                QuizSubmission::delete((int) $this->input('sumit_id'));
                Session::flash('success', 'Result deleted.');
                return;
        }
    }

    /**
     * Collect non-empty option fields (name="options[]") in order, remapping the
     * chosen answer index past any blank rows.
     *
     * @return array{0: array<int,string>, 1: int}
     */
    private function cleanOptions(): array
    {
        $raw     = (array) ($_POST['options'] ?? []);
        $chosen  = (int) $this->input('answer');
        $options = [];
        $answer  = 0;
        foreach ($raw as $i => $opt) {
            $opt = trim((string) $opt);
            if ($opt === '') {
                continue;
            }
            if ($i === $chosen) {
                $answer = count($options);
            }
            $options[] = $opt;
        }
        return [$options, $answer];
    }

    /**
     * Flatten each lesson's MCQ questions into display rows.
     *
     * @param array<int, array> $lessons
     * @return array<int, array{quiz_id:int, lesson_id:int, lesson_title:string, question:string, options:array<int,string>, answer:int}>
     */
    private function quizRows(array $lessons): array
    {
        $rows = [];
        foreach ($lessons as $lesson) {
            foreach (Quiz::questionsForLesson((int) $lesson['lesson_id']) as $q) {
                $rows[] = [
                    'quiz_id'      => $q['quiz_id'],
                    'lesson_id'    => (int) $lesson['lesson_id'],
                    'lesson_title' => (string) $lesson['title'],
                    'question'     => $q['question'],
                    'options'      => $q['options'],
                    'answer'       => $q['answer'],
                ];
            }
        }
        return $rows;
    }

    /**
     * Flatten each lesson's quiz submissions, resolved to the student name.
     *
     * @param array<int, array> $lessons
     * @return array<int, array{sumit_id: int, student: string, lesson_title: string, image: string}>
     */
    private function resultRows(array $lessons): array
    {
        $rows = [];
        foreach ($lessons as $lesson) {
            foreach (QuizSubmission::forLesson((int) $lesson['lesson_id']) as $sub) {
                $student = User::find((int) $sub['user_id']);
                $rows[] = [
                    'sumit_id'     => (int) $sub['sumit_id'],
                    'student'      => $student['name'] ?? '(deleted user)',
                    'lesson_title' => (string) $lesson['title'],
                    'image'        => (string) $sub['image'],
                ];
            }
        }
        return $rows;
    }

    /**
     * Enrolled students (from payments), resolved to their contact details.
     *
     * @return array<int, array{name: string, phone: string, email: string, date: string}>
     */
    private function studentRows(int $courseId): array
    {
        $rows = [];
        foreach (Payment::forCourse($courseId) as $payment) {
            $student = User::find((int) $payment['user_id']);
            if ($student === false) {
                continue;
            }
            $rows[] = [
                'name'  => (string) $student['name'],
                'phone' => (string) ($student['phone'] ?? ''),
                'email' => (string) ($student['email'] ?? ''),
                'date'  => (string) ($payment['date'] ?? ''),
            ];
        }
        return $rows;
    }

    /** @return array<string, mixed> the logged-in trainer's row */
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
}
