<?php
/**
 * Admin dashboard body. Data comes from Admin\DashboardController.
 *
 * @var array{categories: int, courses: int, revenue: int, users: int} $stats
 * @var array<int, array{title: string, count: int}>                    $popular
 * @var array<int, array{title: string, user: string, date: string, total: string}> $payments
 * @var array<int, array{title: string, count: int}>                    $coursesBought
 * @var array<int, array{name: string, count: int}>                     $topStudents
 */
$coursesBought = $coursesBought ?? [];
$topStudents   = $topStudents ?? [];
?>
            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center gap-3 p-4">
                            <i class="fas fa-th-large fa-3x " style='color:#F28500'></i>
                            <div class="ms-3">
                                <p class="mb-2">Categories</p>
                                <h6 class="mb-0"><?= (int) $stats['categories'] ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center gap-3 p-4">
                        <i class="fas fa-book fa-3x " style='color:#F28500'></i>
                            <div class="ms-3">
                                <p class="mb-2">Courses</p>
                                <h6 class="mb-0"><?= (int) $stats['courses'] ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center gap-3 p-4">
                        <i class="fas fa-chart-line fa-3x "style='color:#F28500'></i>
                            <div class="ms-3">
                                <p class="mb-2">Revenue</p>
                                <h6 class="mb-0">$<?= (int) $stats['revenue'] ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex gap-3 align-items-center p-4">
                            <i class="bi bi-people fa-2x" style='color:#F28500'></i>
                            <div class="ms-3">
                                <p class="mb-2">Users</p>
                                <h6 class="mb-0"><?= (int) $stats['users'] ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sale & Revenue End -->

            <!-- Charts Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12 col-xl-6">
                        <div class="bg-secondary rounded p-4 h-100">
                            <h5 class="mb-3 fw-bold text-start" style="color:#F28500;"><i class="fas fa-shopping-cart me-2"></i>Courses Bought</h5>
                            <?php if (empty($coursesBought)) : ?>
                                <p class="text-muted mb-0">No purchases yet.</p>
                            <?php else : ?>
                                <div style="height:300px"><canvas id="coursesChart"></canvas></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="bg-secondary rounded p-4 h-100">
                            <h5 class="mb-3 fw-bold text-start" style="color:#F28500;"><i class="fas fa-user-graduate me-2"></i>Top Students <span class="text-muted small">(courses bought)</span></h5>
                            <?php if (empty($topStudents)) : ?>
                                <p class="text-muted mb-0">No students have bought a course yet.</p>
                            <?php else : ?>
                                <div style="height:300px"><canvas id="studentsChart"></canvas></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Charts End -->

            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="mb-0 fw-bold text-start" style="color:#F28500;"><i class="fas fa-fire me-2"></i>Popular Courses</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">Title</th>
                                    <th scope="col">students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popular as $row) : ?>
                                <tr>
                                    <td><?= e($row['title']) ?></td>
                                    <td><?= (int) $row['count'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Recent Sales End -->

            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="mb-0 fw-bold text-start" style="color:#F28500;"><i class="fas fa-receipt me-2"></i>User Payments</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">Title</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $pay) : ?>
                                <tr>
                                    <td><?= e($pay['title']) ?></td>
                                    <td><?= e($pay['user']) ?></td>
                                    <td><?= e($pay['date']) ?></td>
                                    <td>$<?= e($pay['total']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Recent Sales End -->

<script>
/* Admin dashboard charts (Chart.js v3). Runs after the footer loads chart.min.js. */
window.addEventListener('load', function () {
    if (typeof Chart === 'undefined') { return; }

    var coursesData  = <?= json_encode(array_map(static fn ($r) => ['label' => $r['title'], 'value' => (int) $r['count']], $coursesBought), JSON_UNESCAPED_UNICODE) ?>;
    var studentsData = <?= json_encode(array_map(static fn ($r) => ['label' => $r['name'], 'value' => (int) $r['count']], $topStudents), JSON_UNESCAPED_UNICODE) ?>;
    var charts = {};

    function themeColors() {
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            ink:  dark ? '#e7eaf2' : '#1f2430',
            grid: dark ? 'rgba(255,255,255,.09)' : 'rgba(20,25,40,.08)',
            bar:  '#F28500',
            barHover: '#ff9e2c'
        };
    }

    /* Draw each value just past its bar end — direct labels, no plugin dependency. */
    var valueLabels = {
        id: 'valueLabels',
        afterDatasetsDraw: function (chart) {
            var ctx = chart.ctx, ink = themeColors().ink;
            chart.getDatasetMeta(0).data.forEach(function (bar, i) {
                ctx.save();
                ctx.fillStyle = ink;
                ctx.font = '700 12px system-ui, sans-serif';
                ctx.textBaseline = 'middle';
                ctx.fillText(chart.data.datasets[0].data[i], bar.x + 7, bar.y);
                ctx.restore();
            });
        }
    };

    function makeBar(id, rows, unit) {
        var el = document.getElementById(id);
        if (!el) { return; }
        var c = themeColors();
        if (charts[id]) { charts[id].destroy(); }
        charts[id] = new Chart(el, {
            type: 'bar',
            data: {
                labels: rows.map(function (r) { return r.label; }),
                datasets: [{
                    data: rows.map(function (r) { return r.value; }),
                    backgroundColor: c.bar,
                    hoverBackgroundColor: c.barHover,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 28
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { right: 26 } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (x) { return ' ' + x.parsed.x + ' ' + unit; } } }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { color: c.ink, precision: 0 }, grid: { color: c.grid, drawBorder: false } },
                    y: { ticks: { color: c.ink }, grid: { display: false, drawBorder: false } }
                }
            },
            plugins: [valueLabels]
        });
    }

    function renderCharts() {
        makeBar('coursesChart', coursesData, 'purchases');
        makeBar('studentsChart', studentsData, 'courses');
    }

    renderCharts();
    /* Rebuild with the right ink/grid when the dark/light toggle flips data-theme. */
    new MutationObserver(renderCharts).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
});
</script>
