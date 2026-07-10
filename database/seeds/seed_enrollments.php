<?php

/*
 * Demo enrollment history — creates a spread of sample students who each bought
 * a course on a historical date across the past ~12 months, so the admin
 * dashboard's "New Students" tracking chart (Week / Month / Year / All) shows a
 * real trend instead of a single point.
 *
 * These are clearly-named demo rows (email demo_learner_*@example.com). Remove
 * them any time with:
 *   DELETE FROM payments WHERE user_id IN (SELECT user_id FROM users WHERE email LIKE 'demo\\_learner\\_%');
 *   DELETE FROM users WHERE email LIKE 'demo\\_learner\\_%';
 *
 * Idempotent: a demo student that already exists is skipped. Run with:
 *   php database/seeds/seed_enrollments.php
 */

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\Env;
use App\Models\Course;
use App\Models\User;

Env::load(dirname(__DIR__, 2) . '/.env');

$pdo     = Database::connection();
$courses = Course::all();
if ($courses === []) {
    exit("No courses found — seed courses first.\n");
}

$names = [
    'Sophea Kim', 'Dara Sok', 'Ratana Chan', 'Vichea Nou', 'Bopha Ly',
    'Kosal Meas', 'Chenda Pich', 'Rithy Sam', 'Maly Tep', 'Visal Hong',
    'Sreymom Chea', 'Panha Voun',
];

$added = 0;
$skipped = 0;
foreach ($names as $i => $name) {
    $email = 'demo_learner_' . $i . '@example.com';

    if (!empty(User::findByEmail($email))) {
        $skipped++;
        continue;
    }

    User::create(
        $name,
        $email,
        '1234567',
        '0' . str_pad((string) (10000000 + $i), 9, '0', STR_PAD_LEFT),
        $i % 2 === 0 ? 'Male' : 'Female',
        'non.webp',
        User::ROLE_STUDENT
    );

    $user   = User::findByEmail($email, User::ROLE_STUDENT);
    $course = $courses[$i % count($courses)];

    // One purchase, dated i months ago (0 = this month) with a little day jitter.
    $date = (new DateTime())
        ->modify('-' . $i . ' months')
        ->modify('-' . (($i * 3) % 20) . ' days')
        ->format('Y-m-d');

    $pdo->prepare('INSERT INTO payments (user_id, course_id, total, date) VALUES (:u, :c, :t, :d)')
        ->execute([
            ':u' => (int) $user['user_id'],
            ':c' => (int) $course['course_id'],
            ':t' => (string) $course['price'],
            ':d' => $date,
        ]);

    echo "  + {$name} bought {$course['title']} on {$date}\n";
    $added++;
}

echo "\nDone. Added {$added} demo students with historical purchases, skipped {$skipped}.\n";
