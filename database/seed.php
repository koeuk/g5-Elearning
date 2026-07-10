<?php

/*
 * Sample-data seeder.
 *
 * Populates a fresh database with a trainer, a handful of categories and some
 * courses so the public home and student dashboard have content to render.
 * Idempotent: skips any table that already has rows, so it is safe to re-run.
 *
 * Usage:  php database/seed.php
 * (Loads .env the same way the app does, so no manual env export is needed.)
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;

Env::load(__DIR__ . '/../.env');

/* --- Trainer (courses need an owner; roles_id = 2) ----------------------- */
$trainer = User::findByEmail('trainer@example.com', User::ROLE_TRAINER);
if ($trainer === []) {
    User::create('Sophea Chan', 'trainer@example.com', 'Trainer@123', '0123456789', 'Female', 'admin.png', User::ROLE_TRAINER);
    $trainer = User::findByEmail('trainer@example.com', User::ROLE_TRAINER);
    echo "Created trainer trainer@example.com (password: Trainer@123)\n";
} else {
    echo "Trainer already present — leaving as is.\n";
}
$trainerId = (int) $trainer['user_id'];

/* --- Categories ---------------------------------------------------------- */
$categorySeed = [
    ['Web Development', 'Build modern websites and web apps.', '01.jpg'],
    ['Data Science',    'Analyse data and build models.',      '02.jpg'],
    ['Design',          'UI, UX and graphic design.',          '03.jpg'],
    ['Marketing',       'Grow audiences and brands.',          '07.jpg'],
];

if (Category::all() === []) {
    foreach ($categorySeed as [$title, $desc, $img]) {
        Category::create($title, $desc, $img);
    }
    echo 'Seeded ' . count($categorySeed) . " categories.\n";
} else {
    echo "Categories already present — skipping.\n";
}

// Resolve titles -> ids for the course seed.
$catId = [];
foreach (Category::all() as $c) {
    $catId[$c['title']] = (int) $c['category_id'];
}

/* --- Courses ------------------------------------------------------------- */
$courseSeed = [
    ['Intro to HTML & CSS',        'Web Development', '19', '08.jpg'],
    ['JavaScript Fundamentals',    'Web Development', '25', '10.jpg'],
    ['Python for Data Analysis',   'Data Science',    '29', '18.jpg'],
    ['Machine Learning Basics',    'Data Science',    '39', '21.jpg'],
    ['Figma from Scratch',         'Design',          '15', '22.jpg'],
    ['Brand Design Essentials',    'Design',          '18', '01.jpg'],
    ['SEO Fundamentals',           'Marketing',       '12', '02.jpg'],
    ['Social Media Marketing',     'Marketing',       '22', '03.jpg'],
];

if (Course::all() === []) {
    $today = date('Y-m-d');
    $made  = 0;
    foreach ($courseSeed as [$title, $categoryTitle, $price, $img]) {
        $categoryId = $catId[$categoryTitle] ?? null;
        if ($categoryId === null) {
            echo "  ! skipped '{$title}' — category '{$categoryTitle}' not found\n";
            continue;
        }
        Course::create($title, "A hands-on {$title} course.", $categoryId, $today, $img, $trainerId, $price);
        $made++;
    }
    echo "Seeded {$made} courses.\n";
} else {
    echo "Courses already present — skipping.\n";
}

echo "Done.\n";
