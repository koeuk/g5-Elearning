<?php

/*
 * Quiz seeder — adds one quiz per lesson so the classroom "Quizzes" tab has
 * content. A quiz's `content` is the URL the "Start quiz" button opens.
 *
 * Idempotent: a lesson that already has at least one quiz is skipped. Run with:
 *   php database/seeds/seed_quizzes.php
 * (Run seed_lessons.php first — quizzes attach to lessons.)
 */

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\Env;
use App\Models\Quiz;

Env::load(dirname(__DIR__, 2) . '/.env');

/**
 * course title => quiz URL. Real, topical quizzes where one exists; the rest
 * are sample links — swap them for your own Google Form / quiz any time.
 */
$quizUrlByCourse = [
    'Intro to HTML & CSS'      => 'https://www.w3schools.com/quiztest/quiztest.asp?qtest=HTML',
    'JavaScript Fundamentals'  => 'https://www.w3schools.com/quiztest/quiztest.asp?qtest=JS',
    'Python for Data Analysis' => 'https://www.w3schools.com/quiztest/quiztest.asp?qtest=PYTHON',
    'Machine Learning Basics'  => 'https://www.w3schools.com/quiztest/quiztest.asp?qtest=AI',
    'Figma from Scratch'       => 'https://forms.gle/sample-figma-quiz',
    'Brand Design Essentials'  => 'https://forms.gle/sample-branding-quiz',
    'SEO Fundamentals'         => 'https://www.w3schools.com/quiztest/quiztest.asp?qtest=SEO',
    'Social Media Marketing'   => 'https://forms.gle/sample-social-quiz',
];

$pdo = Database::connection();

// One row per lesson, with its course title, ordered so output reads nicely.
$rows = $pdo->query(
    'SELECT l.lesson_id, l.title AS lesson, c.title AS course
     FROM lessons l JOIN courses c ON l.course_id = c.course_id
     ORDER BY c.course_id, l.lesson_id'
)->fetchAll();

$added = 0;
$skipped = 0;
foreach ($rows as $r) {
    $lessonId = (int) $r['lesson_id'];

    if (count(Quiz::forLesson($lessonId)) > 0) {
        $skipped++;
        continue;
    }

    $url = $quizUrlByCourse[$r['course']] ?? 'https://forms.gle/sample-quiz';
    Quiz::create($lessonId, $url);
    $added++;
}

echo "Added {$added} quiz(zes), skipped {$skipped} lesson(s) that already had one.\n";
echo "Total quizzes now: " . $pdo->query('SELECT COUNT(*) FROM quizzes')->fetchColumn() . "\n";
