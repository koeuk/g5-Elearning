<?php
/**
 * Admin dashboard body. Data comes from Admin\DashboardController.
 *
 * @var array{categories: int, courses: int, revenue: int, users: int} $stats
 * @var array<int, array{title: string, count: int}>                    $popular
 * @var array<int, array{title: string, user: string, date: string, total: string}> $payments
 */
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
