<?php

namespace App\Models;

use App\Core\Database;

/**
 * `quizzes` table. Each row is one multiple-choice question; the `content`
 * column stores it as JSON: {"q": "...", "options": ["A","B",...], "answer": 1}.
 */
final class Quiz
{
    /** @return array<int, array> */
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quizzes');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** @return array<int, array> */
    public static function forLesson(int $lessonId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quizzes WHERE lesson_id = :id');
        $stmt->execute([':id' => $lessonId]);
        return $stmt->fetchAll();
    }

    /**
     * Quizzes for a lesson with the JSON `content` parsed into
     * question/options/answer. Rows whose content isn't valid MCQ JSON (legacy
     * URL quizzes) are skipped.
     *
     * @return array<int, array{quiz_id:int, lesson_id:int, question:string, options:array<int,string>, answer:int}>
     */
    public static function questionsForLesson(int $lessonId): array
    {
        $out = [];
        foreach (self::forLesson($lessonId) as $row) {
            $q = self::parse((string) $row['content']);
            if ($q !== null) {
                $out[] = [
                    'quiz_id'   => (int) $row['quiz_id'],
                    'lesson_id' => (int) $row['lesson_id'],
                ] + $q;
            }
        }
        return $out;
    }

    public static function find(int $id): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quizzes WHERE quiz_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(int $lessonId, string $content): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO quizzes (lesson_id, content) VALUES (:id, :content)');
        $stmt->execute([':id' => $lessonId, ':content' => $content]);
    }

    /**
     * Store a multiple-choice question (question text, its options and the index
     * of the correct option) as JSON in the content column.
     *
     * @param array<int, string> $options
     */
    public static function addQuestion(int $lessonId, string $question, array $options, int $answerIndex): void
    {
        self::create($lessonId, self::encode($question, $options, $answerIndex));
    }

    /**
     * Decode a stored MCQ. Returns null when the content is not valid MCQ JSON
     * (e.g. an old URL-based quiz), so callers can skip it gracefully.
     *
     * @return array{question:string, options:array<int,string>, answer:int}|null
     */
    public static function parse(string $content): ?array
    {
        $data = json_decode($content, true);
        if (!is_array($data) || !isset($data['q'], $data['options']) || !is_array($data['options'])) {
            return null;
        }
        $options = array_values(array_map('strval', $data['options']));
        if (count($options) < 2) {
            return null;
        }
        $answer = (int) ($data['answer'] ?? 0);
        if ($answer < 0 || $answer >= count($options)) {
            $answer = 0;
        }
        return ['question' => (string) $data['q'], 'options' => $options, 'answer' => $answer];
    }

    /**
     * JSON-encode an MCQ for storage.
     *
     * @param array<int, string> $options
     */
    public static function encode(string $question, array $options, int $answerIndex): string
    {
        return (string) json_encode([
            'q'       => $question,
            'options' => array_values($options),
            'answer'  => $answerIndex,
        ], JSON_UNESCAPED_UNICODE);
    }

    public static function update(int $quizId, int $lessonId, string $content): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE quizzes SET lesson_id = :lesson_id, content = :content WHERE quiz_id = :quiz_id'
        );
        $stmt->execute([':lesson_id' => $lessonId, ':content' => $content, ':quiz_id' => $quizId]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM quizzes WHERE quiz_id = :id');
        $stmt->execute([':id' => $id]);
    }
}
