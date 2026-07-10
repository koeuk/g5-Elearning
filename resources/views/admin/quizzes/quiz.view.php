<?php
/**
 * Admin quiz management — add multiple-choice questions to a lesson and list
 * existing ones. Rendered inside the admin layout.
 *
 * @var array $lessons   [{lesson_id, title, course}] for the lesson dropdown
 * @var array $questions [{quiz_id, course, lesson, question, options[], answer}]
 */
$lessons   = $lessons ?? [];
$questions = $questions ?? [];

// Group lessons by course for the <optgroup> dropdown.
$byCourse = [];
foreach ($lessons as $l) {
    $byCourse[$l['course']][] = $l;
}
?>
<div class="p-5 pt-3">
    <div class="mt-3 mb-4">
        <h3>Quiz Questions</h3>
        <p class="text-muted mb-0">Add multiple-choice questions to a lesson. Students take them in the classroom and get an instant score.</p>
    </div>

    <!-- Add question -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3"><i class="fa fa-plus-circle text-orange me-2"></i>Add a question</h5>
            <form action="/admin_quizzes_add" method="post">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label" for="lesson_id">Lesson</label>
                        <select class="form-select" id="lesson_id" name="lesson_id" required>
                            <option value="">Choose a lesson…</option>
                            <?php foreach ($byCourse as $course => $ls): ?>
                                <optgroup label="<?= e($course) ?>">
                                    <?php foreach ($ls as $l): ?>
                                        <option value="<?= (int) $l['lesson_id'] ?>"><?= e($l['title']) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label" for="question">Question</label>
                        <input type="text" class="form-control" id="question" name="question" placeholder="e.g. What does HTML stand for?" required>
                    </div>
                </div>

                <label class="form-label mt-3">Options <span class="text-muted small">(select the radio for the correct answer)</span></label>
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="input-group mb-2">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="radio" name="answer" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?> aria-label="Mark option <?= $i + 1 ?> correct">
                        </div>
                        <input type="text" class="form-control" name="options[]" placeholder="Option <?= $i + 1 ?><?= $i >= 2 ? ' (optional)' : '' ?>" <?= $i < 2 ? 'required' : '' ?>>
                    </div>
                <?php endfor; ?>

                <button type="submit" class="btn btn-orange mt-2"><i class="fa fa-save me-2"></i>Add question</button>
            </form>
        </div>
    </div>

    <!-- Existing questions -->
    <div class="table-responsive">
        <table class="table text-start align-middle table-bordered table-dark table-hover mb-0">
            <thead>
                <tr class="text-white">
                    <th>#</th>
                    <th>Course</th>
                    <th>Lesson</th>
                    <th>Question</th>
                    <th>Options</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($questions)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No questions yet — add one above.</td></tr>
                <?php endif; ?>
                <?php foreach ($questions as $k => $q): ?>
                    <tr>
                        <td><?= $k + 1 ?></td>
                        <td><?= e($q['course']) ?></td>
                        <td><?= e($q['lesson']) ?></td>
                        <td><?= e($q['question']) ?></td>
                        <td>
                            <?php foreach ($q['options'] as $oi => $opt): ?>
                                <span class="badge <?= $oi === $q['answer'] ? 'bg-success' : 'bg-secondary' ?> me-1 mb-1">
                                    <?php if ($oi === $q['answer']): ?><i class="fa fa-check me-1"></i><?php endif; ?><?= e($opt) ?>
                                </span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <form action="/admin_quizzes_delete" method="post" onsubmit="return confirm('Delete this question?');">
                                <input type="hidden" name="id" value="<?= (int) $q['quiz_id'] ?>">
                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
