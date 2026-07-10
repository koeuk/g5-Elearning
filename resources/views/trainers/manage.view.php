<?php
/**
 * Trainer course-management console. Data comes from Trainer\ManageController.
 *
 * @var array $course                       the course being managed
 * @var array<int, array> $lessons          lessons (lesson_id, title, description, video)
 * @var array<int, array> $quizzes          flat rows: quiz_id, lesson_id, lesson_title, content
 * @var array<int, array> $results          flat rows: sumit_id, student, lesson_title, image
 * @var array<int, array> $students         enrolled: name, phone, email, date
 */
$courseId = (int) ($course['course_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage · <?= e($course['title'] ?? 'Course') ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <script>(function(){try{var s=localStorage.getItem('eLearnTheme');var t=s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light');document.documentElement.setAttribute('data-theme',t);document.documentElement.setAttribute('data-bs-theme',t);}catch(e){document.documentElement.setAttribute('data-theme','light');document.documentElement.setAttribute('data-bs-theme','light');}})();</script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/student-ui.css" rel="stylesheet">
    <style>
        body.ui-scope { font-family: var(--sans); }
        .d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
        .text-orange { color: var(--accent) !important; }
        .btn-orange { background: var(--accent); border-color: var(--accent); color: var(--accent-ink); font-weight: 700; }
        .btn-orange:hover { background: var(--accent-2); border-color: var(--accent-2); color: var(--accent-ink); }
        .btn-outline-orange { border: 1px solid var(--accent); color: var(--accent); }
        .btn-outline-orange:hover { background: var(--accent); color: var(--accent-ink); }
        .mgr-hero { max-width: 1140px; margin: 1.6rem auto 1.4rem; padding: 0 1.5rem; }
        .mgr-hero h1 { font-family: var(--serif); font-weight: 600; letter-spacing: -.01em; font-size: clamp(1.7rem,3.4vw,2.4rem); }
        .mgr-hero h1 em { font-style: italic; color: var(--accent); }
        .mgr-hero p { color: var(--muted); }
        .nav-pills { background: var(--surface-2); border: 1px solid var(--line); border-radius: 999px; padding: .35rem; display: inline-flex; gap: .25rem; }
        .nav-pills .nav-link { color: var(--muted); font-weight: 600; border-radius: 999px; }
        .nav-pills .nav-link.active { background: var(--accent); color: var(--accent-ink); }
        .card-panel { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; }
        .card-panel h3 { font-family: var(--serif); font-weight: 600; }
        table thead th { color: var(--accent); border-bottom: 2px solid var(--line); }
        .modal-title { color: var(--accent); font-weight: 700; font-family: var(--serif); }
    </style>
</head>
<body class="ui-scope">
<?= \App\Core\View::partial('layouts/trainer/topbar', ['trainer' => \App\Core\Auth::user() ?? []]) ?>

<div class="mgr-hero">
    <a href="/trainer" class="ui-btn ui-btn--ghost" style="padding:.5rem .95rem;font-size:.88rem"><i class="bi bi-arrow-left"></i> Back to dashboard</a>
    <p style="color:var(--accent);font-weight:700;text-transform:uppercase;letter-spacing:.14em;font-size:.72rem;margin:1.1rem 0 .3rem">Course studio</p>
    <h1 class="mb-1">Managing <em><?= e($course['title'] ?? '') ?></em></h1>
    <p class="mb-0">Lessons, quizzes, results and enrolled students — all in one place.</p>
    <div class="mt-3"><?= \App\Core\View::partial('layouts/partials/flash') ?></div>
</div>

<div class="container pb-5">
    <ul class="nav nav-pills gap-2 mb-3" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-lessons">Lessons</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-quizzes">Quizzes</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-results">Results</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-students">Students</button></li>
    </ul>

    <div class="tab-content">
        <!-- ===================== Lessons ===================== -->
        <div class="tab-pane fade show active" id="tab-lessons">
            <div class="card-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Lessons</h3>
                    <button class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#addLessonModal"><i class="fa fa-plus-square me-1"></i> Add lesson</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Title</th><th>Access</th><th>Description</th><th>Video</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php if (empty($lessons)) : ?>
                            <tr><td colspan="5" class="text-muted">No lessons yet. Add your first one.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($lessons as $lesson) : $free = !empty($lesson['is_free']); ?>
                            <tr>
                                <td><?= e($lesson['title']) ?></td>
                                <td>
                                    <?php if ($free) : ?>
                                        <span class="badge bg-success"><i class="fas fa-unlock-alt me-1"></i>Free</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-truncate" style="max-width: 300px;"><?= e($lesson['description']) ?></td>
                                <td><button class="btn btn-sm btn-link view-video" data-video="<?= e($lesson['video']) ?>"><i class="fas fa-play-circle text-orange fa-lg"></i></button></td>
                                <td class="d-flex gap-2">
                                    <button class="btn btn-sm btn-orange edit-lesson"
                                            data-id="<?= (int) $lesson['lesson_id'] ?>"
                                            data-title="<?= e($lesson['title']) ?>"
                                            data-description="<?= e($lesson['description']) ?>"
                                            data-video="<?= e($lesson['video']) ?>"
                                            data-free="<?= $free ? '1' : '0' ?>"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="btn btn-sm btn-danger del-lesson" data-id="<?= (int) $lesson['lesson_id'] ?>"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== Quizzes ===================== -->
        <div class="tab-pane fade" id="tab-quizzes">
            <div class="card-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Quizzes</h3>
                    <button class="btn btn-orange <?= empty($lessons) ? 'disabled' : '' ?>" data-bs-toggle="modal" data-bs-target="#addQuizModal"><i class="fa fa-plus-square me-1"></i> Add quiz</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Lesson</th><th>Question</th><th>Options</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php if (empty($quizzes)) : ?>
                            <tr><td colspan="4" class="text-muted">No questions yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($quizzes as $q) : ?>
                            <tr>
                                <td><?= e($q['lesson_title']) ?></td>
                                <td><?= e($q['question']) ?></td>
                                <td>
                                    <?php foreach ($q['options'] as $oi => $opt) : ?>
                                        <span class="badge <?= $oi === $q['answer'] ? 'bg-success' : 'bg-secondary' ?> me-1 mb-1"><?php if ($oi === $q['answer']) : ?><i class="fa fa-check me-1"></i><?php endif; ?><?= e($opt) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger del-quiz" data-id="<?= (int) $q['quiz_id'] ?>"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== Results ===================== -->
        <div class="tab-pane fade" id="tab-results">
            <div class="card-panel p-4">
                <h3 class="mb-3">Student quiz results</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Student</th><th>Lesson</th><th>Result</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php if (empty($results)) : ?>
                            <tr><td colspan="4" class="text-muted">No submissions yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($results as $r) : ?>
                            <tr>
                                <td><?= e($r['student']) ?></td>
                                <td><?= e($r['lesson_title']) ?></td>
                                <td><button class="btn btn-sm btn-link view-result" data-image="<?= e(uploadedImage($r['image'])) ?>"><i class="fas fa-image text-orange fa-lg"></i></button></td>
                                <td><button class="btn btn-sm btn-danger del-result" data-id="<?= (int) $r['sumit_id'] ?>"><i class="fas fa-trash"></i> Delete</button></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== Students ===================== -->
        <div class="tab-pane fade" id="tab-students">
            <div class="card-panel p-4">
                <h3 class="mb-3">Enrolled students</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Joined</th></tr></thead>
                        <tbody>
                        <?php if (empty($students)) : ?>
                            <tr><td colspan="4" class="text-muted">No students enrolled yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($students as $s) : ?>
                            <tr>
                                <td><?= e($s['name']) ?></td>
                                <td><?= e($s['phone']) ?></td>
                                <td><?= e($s['email']) ?></td>
                                <td><?= e($s['date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// A hidden field pair every form needs: the action verb + the course id.
$courseField = '<input type="hidden" name="course" value="' . $courseId . '">';
?>

<!-- Add Lesson -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-header"><h5 class="modal-title">Add Lesson</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="add_lesson"><?= $courseField ?>
            <div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"></textarea></div>
            <div class="mb-3"><label class="form-label">Video URL</label><input class="form-control" name="video" placeholder="https://www.youtube.com/embed/…" required></div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_free" value="1" id="al-free">
                <label class="form-check-label" for="al-free">Free lesson &mdash; previewable without buying the course</label>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-orange">Add lesson</button></div>
    </form>
</div></div></div>

<!-- Edit Lesson -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-header"><h5 class="modal-title">Edit Lesson</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="edit_lesson"><?= $courseField ?>
            <input type="hidden" name="lesson_id" id="el-id">
            <div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title" id="el-title" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" id="el-desc" rows="3"></textarea></div>
            <div class="mb-3"><label class="form-label">Video URL</label><input class="form-control" name="video" id="el-video" required></div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_free" value="1" id="el-free">
                <label class="form-check-label" for="el-free">Free lesson &mdash; previewable without buying the course</label>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-orange">Update</button></div>
    </form>
</div></div></div>

<!-- Delete Lesson -->
<div class="modal fade" id="delLessonModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-body text-center p-4">
            <input type="hidden" name="action" value="delete_lesson"><?= $courseField ?>
            <input type="hidden" name="lesson_id" id="dl-id">
            <h5 class="text-orange mb-3">Delete this lesson?</h5>
            <p class="text-muted small">Its quizzes and results will remain but detach from the lesson.</p>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger">Delete</button>
        </div>
    </form>
</div></div></div>

<!-- Add Quiz -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-header"><h5 class="modal-title">Add Question</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="add_quiz"><?= $courseField ?>
            <div class="mb-3"><label class="form-label">Lesson</label>
                <select class="form-select" name="lesson_id" required>
                    <option value="">Select a lesson</option>
                    <?php foreach ($lessons as $lesson) : ?>
                        <option value="<?= (int) $lesson['lesson_id'] ?>"><?= e($lesson['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Question</label>
                <input class="form-control" name="question" placeholder="e.g. What does HTML stand for?" required></div>
            <label class="form-label">Options <span class="text-muted small">(select the correct answer)</span></label>
            <?php for ($i = 0; $i < 4; $i++) : ?>
                <div class="input-group mb-2">
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="radio" name="answer" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?> aria-label="Correct option <?= $i + 1 ?>">
                    </div>
                    <input type="text" class="form-control" name="options[]" placeholder="Option <?= $i + 1 ?><?= $i >= 2 ? ' (optional)' : '' ?>" <?= $i < 2 ? 'required' : '' ?>>
                </div>
            <?php endfor; ?>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-orange">Add question</button></div>
    </form>
</div></div></div>

<!-- Edit Quiz -->
<div class="modal fade" id="editQuizModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-header"><h5 class="modal-title">Edit Quiz</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="edit_quiz"><?= $courseField ?>
            <input type="hidden" name="quiz_id" id="eq-id">
            <div class="mb-3"><label class="form-label">Lesson</label>
                <select class="form-select" name="lesson_id" id="eq-lesson" required>
                    <?php foreach ($lessons as $lesson) : ?>
                        <option value="<?= (int) $lesson['lesson_id'] ?>"><?= e($lesson['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Quiz URL</label><input class="form-control" name="content" id="eq-content" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-orange">Update</button></div>
    </form>
</div></div></div>

<!-- Delete Quiz -->
<div class="modal fade" id="delQuizModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-body text-center p-4">
            <input type="hidden" name="action" value="delete_quiz"><?= $courseField ?>
            <input type="hidden" name="quiz_id" id="dq-id">
            <h5 class="text-orange mb-3">Delete this quiz?</h5>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger">Delete</button>
        </div>
    </form>
</div></div></div>

<!-- Delete Result -->
<div class="modal fade" id="delResultModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form action="/trainer_manage" method="post">
        <div class="modal-body text-center p-4">
            <input type="hidden" name="action" value="delete_result"><?= $courseField ?>
            <input type="hidden" name="sumit_id" id="dr-id">
            <h5 class="text-orange mb-3">Delete this result?</h5>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger">Delete</button>
        </div>
    </form>
</div></div></div>

<!-- View Video / Quiz / Result -->
<div class="modal fade" id="viewVideoModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-body p-2"><div class="ratio ratio-16x9"><iframe id="vv-frame" src="" allowfullscreen></iframe></div></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
</div></div></div>
<div class="modal fade" id="viewQuizModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-body p-2"><div class="ratio ratio-16x9"><iframe id="vq-frame" src="" allowfullscreen></iframe></div></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
</div></div></div>
<div class="modal fade" id="viewResultModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-body text-center p-3"><img id="vr-img" src="" alt="result" class="img-fluid rounded"></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const bs = id => bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
    const on = (sel, fn) => document.querySelectorAll(sel).forEach(el => el.addEventListener('click', () => fn(el)));

    // Populate + open edit/delete modals from the row's data-* attributes.
    on('.edit-lesson', el => {
        document.getElementById('el-id').value = el.dataset.id;
        document.getElementById('el-title').value = el.dataset.title;
        document.getElementById('el-desc').value = el.dataset.description;
        document.getElementById('el-video').value = el.dataset.video;
        document.getElementById('el-free').checked = el.dataset.free === '1';
        bs('editLessonModal').show();
    });
    on('.del-lesson', el => { document.getElementById('dl-id').value = el.dataset.id; bs('delLessonModal').show(); });
    on('.edit-quiz', el => {
        document.getElementById('eq-id').value = el.dataset.id;
        document.getElementById('eq-lesson').value = el.dataset.lesson;
        document.getElementById('eq-content').value = el.dataset.content;
        bs('editQuizModal').show();
    });
    on('.del-quiz', el => { document.getElementById('dq-id').value = el.dataset.id; bs('delQuizModal').show(); });
    on('.del-result', el => { document.getElementById('dr-id').value = el.dataset.id; bs('delResultModal').show(); });

    // View popups.
    on('.view-video', el => { document.getElementById('vv-frame').src = el.dataset.video; bs('viewVideoModal').show(); });
    on('.view-quiz', el => { document.getElementById('vq-frame').src = el.dataset.content; bs('viewQuizModal').show(); });
    on('.view-result', el => { document.getElementById('vr-img').src = el.dataset.image; bs('viewResultModal').show(); });
    ['viewVideoModal', 'viewQuizModal'].forEach(id =>
        document.getElementById(id).addEventListener('hidden.bs.modal', () =>
            document.querySelector('#' + id + ' iframe').src = ''));
</script>
<script src="assets/theme.js"></script>
<script>
  // Keep Bootstrap's own dark mode in sync with the shared theme toggle.
  (function(){ var el = document.documentElement;
    new MutationObserver(function(){ el.setAttribute('data-bs-theme', el.getAttribute('data-theme') || 'light'); })
      .observe(el, { attributes: true, attributeFilter: ['data-theme'] });
  })();
</script>
</body>
</html>
