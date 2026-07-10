<!-- CSS style -->
<style>
    /* Center modal vertically */
    .modal-dialog {
        display: flex;
        align-items: center;
        min-height: calc(80% - 0.5rem);
    }

    /* Add color on text */
    .modal-title {
        color: black;
    }

    /* Style on search and input search */
    #search {
        border-radius: 5px;
        background-color: #343a40;
        color: white;
        border: 1px solid #6c757d;
        padding: 0.375rem 0.75rem;
    }

    .avatar {
        vertical-align: middle;
        width: 45px;
        height: 45px;
        border-radius: 50%;
    }
</style>


<div class="table-responsive p-5 pt-3">
    <?php $flash = \App\Core\Session::flash('trainer_pw'); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="mt-3 mb-3 d-flex justify-content-between align-items-center">
        <h3>Trainers List</h3>

        <!-- input search -->
        <div class="d-flex align-items-center"> <!-- Wrap label and input in a flex container -->
            <label for="search" class="me-4">Search:</label> <!-- Add margin to the label -->
            <input class="form-control pe-5 bg-secondary bg-opacity-10 border-0" type="search" placeholder="Search" aria-label="Search">
        </div>

        <!-- Button trigger modal -->
        <button type="button" style='background:#F28500;color:white' class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-modal"><i class="fa fa-plus-square"></i> Add Trainer</button>

    </div>
    <table class="table text-start align-middle table-bordered table-dark table-hover mb-0 mt-3">
        <thead>
            <tr class="text-white">
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Gender</th>
                <th scope="col">Profile</th>
                <th scope="col">Operations</th>
            </tr>
        </thead>

        <!-- ==================Update trainer ============== -->
        <tbody>
            <?php

            $trainers = $trainers ?? [];
            foreach ($trainers as $key => $trainer) :
            ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= e($trainer['name']) ?></td>
                    <td><?= e($trainer['email']) ?></td>
                    <td><?= e($trainer['phone']) ?></td>
                    <td><?= e($trainer['gender']) ?></td>
                    <td>
                        <img src="<?= e(uploadedImage((string) $trainer['profile_image'])) ?>" class="avatar">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#trainerDetail<?= $trainer['user_id'] ?>"><i class="fas fa-eye"></i> Detail</button>
                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editTrainer<?= $trainer['user_id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                        <form id="delete-form-<?= $trainer['user_id'] ?>" action="/delete_trainer" method="post" style="display: inline;">
                            <input type="text" name="id" value="<?= $trainer['user_id'] ?>" hidden>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#delete-modal<?= $trainer['user_id'] ?>"><i class="fas fa-trash"></i>Delete</button>
                            <button type="submit" form="delete-form-<?= $trainer['user_id'] ?>" class="btn btn-primary" style="display: none;">Confirm Delete</button>
                        </form>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="delete-modal<?= $trainer['user_id'] ?>" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title text-primary">Delete Confirmation</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-dark">Are you sure you want to delete this item?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" form="delete-form-<?= $trainer['user_id'] ?>" class="btn btn-primary">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trainer Detail Modal -->
                        <div class="modal fade" id="trainerDetail<?= $trainer['user_id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="border: none; border-radius: 18px; overflow: hidden;">
                                    <div class="modal-header" style="background: linear-gradient(135deg, #ffb454, #F28500); color: #1a1206;">
                                        <h5 class="modal-title" style="font-weight: 800;"><i class="fas fa-user-tie me-2"></i>Trainer Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-wrap gap-4 p-4" style="background: #1d1810; color: #f4ede0;">
                                        <img src="<?= e(uploadedImage((string) $trainer['profile_image'])) ?>" alt="<?= e($trainer['name']) ?>"
                                             style="width: 150px; height: 150px; object-fit: cover; border-radius: 14px; box-shadow: 0 12px 26px -10px rgba(0,0,0,.7);">
                                        <div class="flex-grow-1" style="min-width: 220px;">
                                            <h4 style="font-weight: 800; color: #fff;"><?= e($trainer['name']) ?></h4>
                                            <p class="mb-2"><span style="color:#a99e8b;">Role :</span> Trainer</p>
                                            <p class="mb-2"><span style="color:#a99e8b;">Email :</span> <?= e($trainer['email']) ?></p>
                                            <p class="mb-2"><span style="color:#a99e8b;">Phone :</span> <?= e($trainer['phone']) ?></p>
                                            <p class="mb-0"><span style="color:#a99e8b;">Gender :</span> <?= e($trainer['gender']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Trainer Modal (details + password) -->
                        <div class="modal fade" id="editTrainer<?= $trainer['user_id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit — <?= e($trainer['name']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-dark">
                                        <div class="row g-4">
                                            <!-- Details -->
                                            <div class="col-md-7">
                                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-id-card me-1"></i> Details</h6>
                                                <form action="/update_trainer" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="id" value="<?= (int) $trainer['user_id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" class="form-control" name="name" value="<?= e($trainer['name']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control" name="email" value="<?= e($trainer['email']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone</label>
                                                        <input type="text" class="form-control" name="phone" value="<?= e($trainer['phone']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Profile photo <span class="text-muted small">(leave empty to keep)</span></label>
                                                        <input type="file" class="form-control" name="image" accept="image/*">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i> Save details</button>
                                                </form>
                                            </div>
                                            <!-- Change password -->
                                            <div class="col-md-5 border-start ps-md-4">
                                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-key me-1"></i> Change password</h6>
                                                <form action="/updateTrainerPassword" method="post">
                                                    <input type="hidden" name="id" value="<?= (int) $trainer['user_id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">New password</label>
                                                        <input type="password" class="form-control" name="newPassword" placeholder="••••••••" autocomplete="new-password">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Confirm password</label>
                                                        <input type="password" class="form-control" name="confirmPassword" placeholder="••••••••" autocomplete="new-password">
                                                    </div>
                                                    <p class="text-muted small">At least 8 characters with a letter, number &amp; symbol.</p>
                                                    <button type="submit" class="btn btn-warning w-100 text-white" style="background:#F28500;"><i class="fas fa-key me-1"></i> Update password</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<!-- ========================= show popup form when create category ================= -->
<!-- Modal -->
<div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="exampleModalLabel">Add Trainder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/add_trainer" method="post" enctype="multipart/form-data">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name">
                        <label for="floatingInput">Name</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="email" name="email">
                        <label for="floatingPassword">Email</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="password" name="password">
                        <label for="floatingPassword">Password</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="phone" name="phone">
                        <label for="floatingPassword">Phone</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="radio" name="gender" value="Male"> Male
                        <input type="radio" name="gender" value="Female"> Female
                    </div>
                    <div class="form-floating mb-4">
                        <input type="file" name='image' class="form-control" aria-label="file example" style="background-color: rgba(0, 0, 0, 0.1);">
                    </div>
                    <div class="form-floating mb-4 btn d-flex flex-row-reverse">
                        <button type="submit" class="btn btn-success" style="margin-left: 10px;">Add</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>