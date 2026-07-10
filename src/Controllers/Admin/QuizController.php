<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Quiz;

/**
 * Admin quiz management ("/admin_quizzes"): create multiple-choice questions for
 * any lesson, and delete them. Questions are stored one-per-row as JSON in
 * quizzes.content and taken by students in the classroom.
 */
final class QuizController extends Controller
{
    /** GET /admin_quizzes — question list + "add question" form. */
    public function index(): void
    {
        $this->requireAdmin();
        $this->admin('admin/quizzes/quiz', [
            'lessons'   => $this->lessonOptions(),
            'questions' => $this->questionList(),
        ]);
    }

    /** POST /admin_quizzes_add — add one MCQ question to a lesson. */
    public function store(): void
    {
        $this->requireAdmin();

        $lessonId = (int) $this->input('lesson_id');
        $question = $this->input('question');
        [$options, $answer] = $this->cleanOptions();

        if ($lessonId > 0 && $question !== '' && count($options) >= 2) {
            Quiz::addQuestion($lessonId, $question, $options, $answer);
            Session::flash('success', 'Question added.');
        } else {
            Session::flash('error', 'Pick a lesson, enter a question and at least two options.');
        }

        $this->redirect('/admin_quizzes');
    }

    /** POST /admin_quizzes_delete — remove a question. */
    public function destroy(): void
    {
        $this->requireAdmin();
        Quiz::delete((int) $this->input('id'));
        Session::flash('success', 'Question deleted.');
        $this->redirect('/admin_quizzes');
    }

    /**
     * Collect non-empty option fields (name="options[]") in order and remap the
     * chosen "answer" index so it still points at the correct option after any
     * blank rows are dropped.
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
     * All lessons with their course title, for the "add question" dropdown.
     *
     * @return array<int, array{lesson_id:int, title:string, course:string}>
     */
    private function lessonOptions(): array
    {
        $rows = Database::connection()->query(
            'SELECT l.lesson_id, l.title, c.title AS course
             FROM lessons l JOIN courses c ON l.course_id = c.course_id
             ORDER BY c.title, l.lesson_id'
        )->fetchAll();

        return array_map(static fn ($r) => [
            'lesson_id' => (int) $r['lesson_id'],
            'title'     => (string) $r['title'],
            'course'    => (string) $r['course'],
        ], $rows);
    }

    /**
     * Every stored question with its lesson + course, newest first.
     *
     * @return array<int, array>
     */
    private function questionList(): array
    {
        $rows = Database::connection()->query(
            'SELECT q.quiz_id, q.content, l.title AS lesson, c.title AS course
             FROM quizzes q
             JOIN lessons l ON q.lesson_id = l.lesson_id
             JOIN courses c ON l.course_id = c.course_id
             ORDER BY q.quiz_id DESC'
        )->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $q = Quiz::parse((string) $r['content']);
            if ($q === null) {
                continue;
            }
            $out[] = [
                'quiz_id'  => (int) $r['quiz_id'],
                'lesson'   => (string) $r['lesson'],
                'course'   => (string) $r['course'],
                'question' => $q['question'],
                'options'  => $q['options'],
                'answer'   => $q['answer'],
            ];
        }
        return $out;
    }
}
