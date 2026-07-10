<?php

/*
 * Quiz seeder — adds multiple-choice questions to each course's first lesson so
 * the classroom "Quizzes" tab has an auto-graded quiz. Each question is stored
 * as JSON in quizzes.content: {"q":..., "options":[...], "answer": index}.
 *
 * Re-runnable: clears existing quizzes for a lesson before seeding it, so it
 * won't create duplicates. Run seed_lessons.php first.
 *   php database/seeds/seed_quizzes.php
 */

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\Env;
use App\Models\Quiz;

Env::load(dirname(__DIR__, 2) . '/.env');

/** course title => list of [question, [options...], correctIndex] */
$questionsByCourse = [
    'Intro to HTML & CSS' => [
        ['What does HTML stand for?', ['Hyper Trainer Marking Language', 'Hyper Text Markup Language', 'Home Tool Markup Language'], 1],
        ['Which tag creates a hyperlink?', ['<link>', '<a>', '<href>'], 1],
        ['Which CSS property changes text colour?', ['font-color', 'text-color', 'color'], 2],
    ],
    'JavaScript Fundamentals' => [
        ['Which keyword declares a block-scoped variable?', ['var', 'let', 'function'], 1],
        ['What does typeof "hello" return?', ['string', 'text', 'char'], 0],
        ['Which operator is strict equality?', ['=', '==', '==='], 2],
    ],
    'Python for Data Analysis' => [
        ['Which library provides DataFrames?', ['numpy', 'pandas', 'flask'], 1],
        ['How do you start a comment in Python?', ['//', '#', '/*'], 1],
        ['Which function prints to the console?', ['echo', 'console.log', 'print'], 2],
    ],
    'Machine Learning Basics' => [
        ['Learning from labelled data is called…', ['Unsupervised', 'Supervised', 'Reinforcement'], 1],
        ['A popular Python ML library is…', ['scikit-learn', 'jQuery', 'bootstrap'], 0],
        ['Overfitting means the model…', ['is too simple', 'memorises the training data', 'has no data'], 1],
    ],
    'Figma from Scratch' => [
        ['Figma is primarily a tool for…', ['video editing', 'UI/UX design', 'spreadsheets'], 1],
        ['A reusable design element in Figma is a…', ['layer', 'component', 'vector'], 1],
        ['Which feature makes designs responsive?', ['auto-layout', 'plugins', 'comments'], 0],
    ],
    'Brand Design Essentials' => [
        ['A brand is more than a…', ['logo', 'price', 'store'], 0],
        ['Consistent colours and fonts build brand…', ['chaos', 'identity', 'traffic'], 1],
        ['A collection of brand assets is a brand…', ['kit', 'box', 'wall'], 0],
    ],
    'SEO Fundamentals' => [
        ['SEO stands for…', ['Search Engine Optimization', 'Social Engine Options', 'Site Entry Order'], 0],
        ['A search engine reading pages is called…', ['indexing', 'crawling', 'ranking'], 1],
        ['Keywords should match the user\'s…', ['budget', 'search intent', 'browser'], 1],
    ],
    'Social Media Marketing' => [
        ['Choose platforms based on your…', ['favourite colour', 'audience', 'the weather'], 1],
        ['A content calendar helps you…', ['plan posts', 'delete accounts', 'hide content'], 0],
        ['Engagement rate measures…', ['followers only', 'interactions vs reach', 'page load'], 1],
    ],
];

$pdo = Database::connection();
$firstLesson = static function (string $courseTitle) use ($pdo): ?int {
    $s = $pdo->prepare(
        'SELECT l.lesson_id FROM lessons l JOIN courses c ON l.course_id = c.course_id
         WHERE c.title = :t ORDER BY l.lesson_id LIMIT 1'
    );
    $s->execute([':t' => $courseTitle]);
    $id = $s->fetchColumn();
    return $id === false ? null : (int) $id;
};

$added = 0;
foreach ($questionsByCourse as $course => $questions) {
    $lessonId = $firstLesson($course);
    if ($lessonId === null) {
        echo "  ! no lesson for course: {$course}\n";
        continue;
    }
    // Clear existing quizzes on this lesson so re-runs don't duplicate.
    $pdo->prepare('DELETE FROM quizzes WHERE lesson_id = :id')->execute([':id' => $lessonId]);

    foreach ($questions as [$q, $options, $answer]) {
        Quiz::addQuestion($lessonId, $q, $options, $answer);
        $added++;
    }
    echo "  {$course}: {$added} total questions on lesson #{$lessonId}\n";
}

// Any remaining URL-based quizzes on other lessons are obsolete — remove them.
$pdo->exec("DELETE FROM quizzes WHERE content NOT LIKE '{%'");

echo "\nSeeded {$added} MCQ questions. Total quizzes now: "
    . $pdo->query('SELECT COUNT(*) FROM quizzes')->fetchColumn() . "\n";
