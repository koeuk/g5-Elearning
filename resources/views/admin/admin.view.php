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

            <!-- New students tracking Start -->
            <style>
                .period-btn{border:1px solid rgba(242,133,0,.45);background:transparent;color:#F28500;
                    font-weight:600;font-size:.82rem;padding:.35rem .95rem;border-radius:8px;cursor:pointer;transition:background .15s,color .15s}
                .period-btn:hover{background:rgba(242,133,0,.14)}
                .period-btn.active{background:#F28500;color:#fff}
            </style>
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold text-start" style="color:#F28500;"><i class="fas fa-user-plus me-2"></i>New Students <span class="text-muted small">(first purchase)</span></h5>
                        <div id="periodToggle" role="group" aria-label="Time range" class="d-flex gap-2">
                            <button type="button" class="period-btn" data-period="week">Week</button>
                            <button type="button" class="period-btn" data-period="month">Month</button>
                            <button type="button" class="period-btn active" data-period="year">Year</button>
                            <button type="button" class="period-btn" data-period="all">All</button>
                        </div>
                    </div>
                    <?php if (empty($newStudentDates)) : ?>
                        <p class="text-muted mb-0">No students have signed up (bought a course) yet.</p>
                    <?php else : ?>
                        <div style="height:320px"><canvas id="newStudentsChart"></canvas></div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- New students tracking End -->

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

    /* ---- New students over time (Week / Month / Year / All) ---- */
    var newStudentDates = <?= json_encode(array_values($newStudentDates ?? []), JSON_UNESCAPED_UNICODE) ?>;
    var currentPeriod = 'year';

    var valueLabelsY = {
        id: 'valueLabelsY',
        afterDatasetsDraw: function (chart) {
            var ctx = chart.ctx, ink = themeColors().ink;
            chart.getDatasetMeta(0).data.forEach(function (bar, i) {
                var v = chart.data.datasets[0].data[i];
                if (!v) { return; }
                ctx.save();
                ctx.fillStyle = ink; ctx.font = '700 11px system-ui, sans-serif';
                ctx.textAlign = 'center'; ctx.textBaseline = 'bottom';
                ctx.fillText(v, bar.x, bar.y - 4);
                ctx.restore();
            });
        }
    };

    function pad(n) { return String(n).padStart(2, '0'); }
    function ymd(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }

    function buildBuckets(period) {
        var now = new Date(), keys = [], labels = [], byMonth = false, i, d;
        if (period === 'week') {
            for (i = 6; i >= 0; i--) { d = new Date(now); d.setDate(now.getDate() - i); keys.push(ymd(d)); labels.push(d.toLocaleDateString(undefined, { weekday: 'short' })); }
        } else if (period === 'month') {
            var y = now.getFullYear(), m = now.getMonth(), days = new Date(y, m + 1, 0).getDate();
            for (i = 1; i <= days; i++) { d = new Date(y, m, i); keys.push(ymd(d)); labels.push(String(i)); }
        } else if (period === 'year') {
            byMonth = true;
            for (i = 11; i >= 0; i--) { d = new Date(now.getFullYear(), now.getMonth() - i, 1); keys.push(ymd(d).slice(0, 7)); labels.push(d.toLocaleDateString(undefined, { month: 'short' })); }
        } else {
            byMonth = true;
            var min = newStudentDates.length ? newStudentDates.reduce(function (a, b) { return a < b ? a : b; }) : ymd(now);
            var cur = new Date(Number(min.slice(0, 4)), Number(min.slice(5, 7)) - 1, 1);
            var end = new Date(now.getFullYear(), now.getMonth(), 1);
            while (cur <= end) { keys.push(ymd(cur).slice(0, 7)); labels.push(cur.toLocaleDateString(undefined, { month: 'short', year: '2-digit' })); cur.setMonth(cur.getMonth() + 1); }
        }
        var counts = keys.map(function (k) { return newStudentDates.filter(function (dt) { return byMonth ? dt.slice(0, 7) === k : dt === k; }).length; });
        return { labels: labels, counts: counts };
    }

    function makeTimeBar(id, labels, counts) {
        var el = document.getElementById(id);
        if (!el) { return; }
        var c = themeColors();
        if (charts[id]) { charts[id].destroy(); }
        charts[id] = new Chart(el, {
            type: 'bar',
            data: { labels: labels, datasets: [{ data: counts, backgroundColor: c.bar, hoverBackgroundColor: c.barHover, borderRadius: 6, borderSkipped: false, maxBarThickness: 46 }] },
            options: {
                responsive: true, maintainAspectRatio: false, layout: { padding: { top: 16 } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (x) { return ' ' + x.parsed.y + ' new student' + (x.parsed.y === 1 ? '' : 's'); } } }
                },
                scales: {
                    x: { ticks: { color: c.ink, maxRotation: 0, autoSkip: true, maxTicksLimit: 16 }, grid: { display: false, drawBorder: false } },
                    y: { beginAtZero: true, ticks: { color: c.ink, precision: 0 }, grid: { color: c.grid, drawBorder: false } }
                }
            },
            plugins: [valueLabelsY]
        });
    }

    function renderNewStudents() {
        var b = buildBuckets(currentPeriod);
        makeTimeBar('newStudentsChart', b.labels, b.counts);
    }

    document.querySelectorAll('#periodToggle .period-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#periodToggle .period-btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            currentPeriod = btn.dataset.period;
            renderNewStudents();
        });
    });

    function renderCharts() {
        makeBar('coursesChart', coursesData, 'purchases');
        makeBar('studentsChart', studentsData, 'courses');
        renderNewStudents();
    }

    renderCharts();
    /* Rebuild with the right ink/grid when the dark/light toggle flips data-theme. */
    new MutationObserver(renderCharts).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
});
</script>
