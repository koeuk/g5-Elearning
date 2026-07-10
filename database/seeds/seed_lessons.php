<?php

/*
 * Lesson seeder — adds sample lessons (with YouTube embed videos) to each
 * seeded course so the student classroom (/blog_learning) has content.
 *
 * Idempotent: a lesson is skipped if one with the same title already exists in
 * that course. Run with:  php database/seeds/seed_lessons.php
 */

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Core\Env;
use App\Models\Lesson;

Env::load(dirname(__DIR__, 2) . '/.env');

/**
 * course title => [ [title, description, youtube-embed-url], ... ]
 * Videos are sample educational embeds; swap the URLs for your own any time.
 */
$byCourse = [
    'Intro to HTML & CSS' => [
        ['Getting Started with HTML', 'Set up your editor and write your first HTML document from scratch.', 'https://www.youtube.com/embed/qz0aGYrrlhU'],
        ['Structuring Content with Tags', 'Headings, paragraphs, lists, links and images — the building blocks of a page.', 'https://www.youtube.com/embed/kUMe1FH4CHE'],
        ['Styling with CSS', 'Colors, fonts, spacing and the box model to make your pages look great.', 'https://www.youtube.com/embed/1PnVor36_40'],
        ['Layouts with Flexbox', 'Arrange elements into responsive rows and columns using Flexbox.', 'https://www.youtube.com/embed/fYq5PXgSsbE'],
    ],
    'JavaScript Fundamentals' => [
        ['Variables & Data Types', 'let, const, strings, numbers, booleans and how JavaScript stores values.', 'https://www.youtube.com/embed/W6NZfCO5SIk'],
        ['Functions & Scope', 'Write reusable functions and understand how scope works.', 'https://www.youtube.com/embed/N8ap4k_1QEQ'],
        ['DOM Manipulation', 'Select and change page elements dynamically with JavaScript.', 'https://www.youtube.com/embed/y17RuWkWdn8'],
        ['Events & Interactivity', 'Respond to clicks, input and other user events.', 'https://www.youtube.com/embed/XF1_MlZ5l6M'],
    ],
    'Python for Data Analysis' => [
        ['Python Basics', 'Variables, loops and functions to get you productive in Python fast.', 'https://www.youtube.com/embed/rfscVS0vtbw'],
        ['Working with Pandas', 'Load, filter and transform tabular data with the pandas library.', 'https://www.youtube.com/embed/vmEHCJofslg'],
        ['Cleaning Messy Data', 'Handle missing values, duplicates and inconsistent formats.', 'https://www.youtube.com/embed/bDhvCp3_lYw'],
        ['Visualising Data', 'Turn numbers into clear charts with matplotlib.', 'https://www.youtube.com/embed/3Xc3CA655Y4'],
    ],
    'Machine Learning Basics' => [
        ['What is Machine Learning?', 'The big ideas behind ML and where it is used in the real world.', 'https://www.youtube.com/embed/ukzFI9rgwfU'],
        ['Supervised Learning', 'Classification and regression with labelled data.', 'https://www.youtube.com/embed/4b4MUYve_U8'],
        ['Training Your First Model', 'Build and fit a simple model with scikit-learn.', 'https://www.youtube.com/embed/0B5eIE_1vpU'],
        ['Evaluating Models', 'Accuracy, over-fitting and how to tell if a model is any good.', 'https://www.youtube.com/embed/85dtiMz9tSo'],
    ],
    'Figma from Scratch' => [
        ['The Figma Interface', 'A tour of frames, the canvas, panels and tools.', 'https://www.youtube.com/embed/FTFaQWZBqQ8'],
        ['Frames & Layouts', 'Use auto-layout to build flexible, responsive designs.', 'https://www.youtube.com/embed/Cx3_Q3iBmxo'],
        ['Components & Variants', 'Create reusable components and manage their states.', 'https://www.youtube.com/embed/1lJvGjfMPfg'],
        ['Prototyping Basics', 'Link screens and add interactions to demo your design.', 'https://www.youtube.com/embed/JbTt_kIKzis'],
    ],
    'Brand Design Essentials' => [
        ['What Makes a Brand', 'Beyond the logo: voice, values and visual identity.', 'https://www.youtube.com/embed/2ZQGVmXHZ7A'],
        ['Logo Design Principles', 'Simplicity, memorability and scalability in logo design.', 'https://www.youtube.com/embed/eZDwYWkgpN4'],
        ['Colour & Typography', 'Choose palettes and typefaces that fit a brand personality.', 'https://www.youtube.com/embed/QrNi9FmdlxY'],
        ['Building a Brand Kit', 'Package your assets into a consistent, reusable system.', 'https://www.youtube.com/embed/sIkzhw4gtBw'],
    ],
    'SEO Fundamentals' => [
        ['How Search Engines Work', 'Crawling, indexing and ranking explained simply.', 'https://www.youtube.com/embed/DvwS7cV9GmQ'],
        ['Keyword Research', 'Find the terms your audience is actually searching for.', 'https://www.youtube.com/embed/1TGj0Fm0tS0'],
        ['On-Page SEO', 'Titles, headings, content and internal links that rank.', 'https://www.youtube.com/embed/MYE6T_gd7H0'],
        ['Link Building', 'Earn quality backlinks that boost your authority.', 'https://www.youtube.com/embed/-8U8lRTPz9w'],
    ],
    'Social Media Marketing' => [
        ['Choosing the Right Platforms', 'Match channels to your audience and goals.', 'https://www.youtube.com/embed/2Rgql4l3JtA'],
        ['Content Strategy', 'Plan a content calendar people actually want to follow.', 'https://www.youtube.com/embed/gWFC_9C6H0k'],
        ['Growing Your Audience', 'Organic tactics to grow reach and engagement.', 'https://www.youtube.com/embed/Z8lLND3vwOs'],
        ['Measuring Results', 'Track the metrics that matter and iterate.', 'https://www.youtube.com/embed/eYUP8f5w4iM'],
    ],
];

$pdo = \App\Core\Database::connection();
$courseId = static function (string $title) use ($pdo): ?int {
    $s = $pdo->prepare('SELECT course_id FROM courses WHERE title = :t');
    $s->execute([':t' => $title]);
    $id = $s->fetchColumn();
    return $id === false ? null : (int) $id;
};

$added = 0;
$skipped = 0;
foreach ($byCourse as $title => $lessons) {
    $cid = $courseId($title);
    if ($cid === null) {
        echo "  ! course not found: {$title}\n";
        continue;
    }
    foreach ($lessons as [$lTitle, $desc, $video]) {
        if (Lesson::existsInCourse($cid, $lTitle) !== []) {
            $skipped++;
            continue;
        }
        Lesson::create($lTitle, $desc, $video, $cid);
        $added++;
    }
    echo "  course #{$cid} {$title}: " . count(Lesson::forCourse($cid)) . " lessons\n";
}

echo "\nDone. Added {$added} lesson(s), skipped {$skipped} existing.\n";
