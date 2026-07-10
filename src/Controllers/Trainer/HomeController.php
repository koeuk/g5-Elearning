<?php

namespace App\Controllers\Trainer;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Course;
use App\Models\User;

/**
 * Trainer dashboard ("/trainer").
 *
 * Replaces app/controllers/trainers/hom.controller + the inline queries the old
 * home.view.php ran via trainer.info.model (getTrainersInfo / getCourseRespone /
 * getCourseed / selectTrainers). The acting trainer now comes from the session
 * rather than a re-posted $_POST['email'].
 */
final class HomeController extends Controller
{
    /** GET /trainer — profile, the trainer's own courses and all courses. */
    public function index(): void
    {
        if (Auth::role() !== User::ROLE_TRAINER) {
            $this->redirect('/trainer_signin');
        }

        $trainer = User::find((int) Auth::id());
        if ($trainer === false) {
            Auth::logout();
            $this->redirect('/trainer_signin');
        }

        $allCourses = Course::all();

        // Resolve each course's trainer name once (used in the "All courses" list).
        $trainerNames = [];
        foreach ($allCourses as $course) {
            $uid = (int) $course['user_id'];
            if (!isset($trainerNames[$uid])) {
                $owner = User::find($uid);
                $trainerNames[$uid] = $owner['name'] ?? '(unknown)';
            }
        }

        $this->view('trainers/home', [
            'trainer'      => $trainer,
            'myCourses'    => Course::byTrainer((int) $trainer['user_id']),
            'allCourses'   => $allCourses,
            'trainerNames' => $trainerNames,
        ]);
    }
}
