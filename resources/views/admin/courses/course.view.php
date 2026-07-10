<!-- Form pop up create form -->
<!-- Payment Modal -->


<!-- ........................\ -->
<div class="container-fluid p-0">
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

     /* ===== Course details modal (refined dark + amber) ===== */
     .cd-modal .modal-content {
          border: none; border-radius: 22px; overflow: hidden;
          background: #1d1810; color: #f4ede0;
          box-shadow: 0 40px 90px -30px rgba(0, 0, 0, .85);
     }
     .cd-head {
          display: flex; align-items: center; justify-content: space-between;
          padding: 1rem 1.4rem; color: #1a1206;
          background: linear-gradient(135deg, #ffb454, #F28500);
     }
     .cd-head h5 { margin: 0; font-weight: 800; letter-spacing: -.01em; display: flex; align-items: center; gap: .55rem; }
     .cd-body { display: flex; gap: 1.6rem; padding: 1.6rem; }
     .cd-media {
          position: relative; flex: 0 0 38%; border-radius: 16px; overflow: hidden; min-height: 300px;
          background: linear-gradient(135deg, #3a2f1c, #241d12);
          box-shadow: 0 20px 40px -18px rgba(0, 0, 0, .7);
     }
     .cd-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
     .cd-media::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 58%, rgba(0, 0, 0, .5)); }
     .cd-cat {
          position: absolute; top: 12px; left: 12px; z-index: 2;
          background: rgba(242, 133, 0, .95); color: #1a1206;
          font-size: .72rem; font-weight: 700; letter-spacing: .04em;
          padding: .32rem .72rem; border-radius: 999px;
     }
     .cd-info { flex: 1; display: flex; flex-direction: column; min-width: 0; }
     .cd-title { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; margin: 0 0 1rem; line-height: 1.15; color: #fff; }
     .cd-stats { display: flex; gap: .7rem; margin-bottom: 1.1rem; flex-wrap: wrap; }
     .cd-stat { background: rgba(255, 255, 255, .04); border: 1px solid rgba(242, 133, 0, .18); border-radius: 12px; padding: .55rem .9rem; }
     .cd-stat .k { font-size: .66rem; text-transform: uppercase; letter-spacing: .08em; color: #a99e8b; }
     .cd-stat .v { font-size: 1.15rem; font-weight: 800; color: #ffb454; line-height: 1.2; }
     .cd-rows { display: flex; flex-direction: column; gap: .55rem; margin-bottom: 1.2rem; }
     .cd-row { display: flex; align-items: center; gap: .65rem; font-size: .92rem; }
     .cd-row .ic { width: 30px; height: 30px; flex: none; display: grid; place-items: center; border-radius: 9px; background: rgba(242, 133, 0, .12); color: #ffb454; }
     .cd-row .ic svg { width: 15px; height: 15px; }
     .cd-row .lbl { color: #a99e8b; min-width: 74px; }
     .cd-row .val { font-weight: 600; color: #f4ede0; }
     .cd-about { margin-top: auto; }
     .cd-about .h { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; color: #a99e8b; margin-bottom: .35rem; }
     .cd-about p { color: #cfc6b6; font-size: .9rem; line-height: 1.55; margin: 0; }
     @media (max-width: 680px) { .cd-body { flex-direction: column; } .cd-media { flex: none; min-height: 200px; } }
     </style>


     <!-- .............................. -->
     <div class="table-responsive pb-3 p-5 pt-4">
          <div class=" pt-2 d-flex justify-content-between align-items-center">
               <h3>Courses List</h3>

               <!-- input search -->
               <div class="d-flex align-items-center">
                    <!-- <form action="controllers/admin/courses/courseSearching.controller.php" method="post" > -->
                    <label for="search" class="me-4">Search:</label> <!-- Add margin to the label -->
                    <input class="form-control pe-5 bg-secondary bg-opacity-10 border-0" id="searchCourse"
                         name="searchCourse" type="text" placeholder="Search" aria-label="Search">
                    <!-- </form> -->
               </div>

               <!-- Button trigger modal -->
               <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style='background:#F28500;color:white'><i
                         class="fa fa-plus-square"></i> Create Course</button>

          </div>
          <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
               aria-hidden="true">
               <div class="modal-dialog" style="max-width: 1000px;">
                    <div class="modal-content">

                         <div class="modal-body  rounded-4">
                              <div class="modal-header">
                                   <h5 class="modal-title text-primary" id="exampleModalLabel">Create Course</h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                              </div>
                              <!-- <div class="update bg-primary p-3 rounded-3 "> -->
                              <!-- </div> -->
                              <form action="/createCourse" method="post"
                                   class="rounded-4 p-4 d-sm-flex justify-content-between gap-3 "
                                   enctype="multipart/form-data">
                                   <div class="div w-100">

                                        <div class="mb-3">
                                             <label for="recipient-name" class="col-form-label">Course:</label>
                                             <input type="text" name="title" class="form-control bg-white" id="title">
                                        </div>
                                        <div class="mb-3">
                                             <label for="recipient-name" class="col-form-label">Description:</label>
                                             <textarea class="form-control color-danger bg-white " name="description"
                                                  id="description"></textarea>
                                        </div>
                                        <div class="mb-3">
                                             <!-- call database -->
                                             <label for="message-text" class="col-form-label">Category :</label>
                                             <select class="form-select bg-white" id="sell1" name="category_id"
                                                  aria-label="Default select example">
                                                  <?php
                                                  $categories = $categories ?? [];
                                                  foreach ($categories as $categiry) :
                                                  ?>
                                                  <option><?= $categiry['title'] ?>
                                                  </option>
                                                  <?php endforeach ?>
                                             </select>

                                        </div>
                                   </div>

                                   <div class="div w-100">

                                        <div class="mb-3">
                                             <label for="message-text" class="col-form-label border-0 ">Trainer
                                                  :</label>
                                             <select class="form-select bg-white" id="sell1" name="user_id"
                                                  aria-label="Default select example">
                                                  <!-- call database -->
                                                  <?php
                                                  $trainers = $trainers ?? [];
                                                  // $trainers = getTrainerWithUserName(); 
                                                  foreach ($trainers as $trainer) : ?>
                                                  <!-- $displayValue = isset($connection) ? $trainer['user_id'] : $trainer['name']; -->
                                                  <option><?= $trainer['name'] ?></option>
                                                  <?php endforeach ?>
                                             </select>
                                        </div>
                                        <div class="mb-3">
                                             <label for="message-text" class="col-form-label">Price :</label>
                                             <input type="text" name="price" class="form-control bg-white " id="price">
                                        </div>
                                        <!-- <div class="mb-3">
                                             <label for="date" class="col-form-label">Date:</label>
                                             <input type="date" name="date" class="form-control" id="date">
                                        </div> -->

                                        <div class="mb-3 ">
                                             <label for="message-text" class="col-form-label">Upload images :</label>

                                             <input type="file" name='image' class="form-control"
                                                  aria-label="file example">
                                        </div>
                                        <div class="modal-footer">
                                             <button type="button" class="btn btn-danger p-2"
                                                  data-bs-dismiss="modal">Cancel</button>
                                             <button type="submit" class="btn btn-success p-2">Create</button>
                                        </div>
                                   </div>

                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>

     <!-- Table get the course and create the course -->
     <div class="container p-5 pt-0 ">
          <table class="table text-start align-middle table-bordered table-dark table-hover mb-0 ">
               <thead class="bg-primary text-white">
                    <tr>
                         <th class="text-center">ID</th>
                         <th class="text-center">Title </th>
                         <!-- <th class="text-center">Description</th> -->
                         <!-- <th class="text-center"></th> -->
                         <th class="text-center">Category</th>
                         <th class="text-center">Trainer</th>
                         <th class="text-center">Images</th>
                         <th class="text-center">Price</th>

                         <!-- th class="text-center">Role ID</th> -->
                         <th class="text-center">Action</th>
                    </tr>
               </thead>
               <tbody>

                    <?php
                    $getCourses = $courses ?? [];
                    foreach ($getCourses as $key => $course) : ?>

                    <tr>
                         <td scope="row" class="text-start "><?= $key + 1 ?></td>
                         <td class="text-start"><?= $course['title'] ?></td>
                         <!-- <td class="text-center"><?= $course["description"] ?></td> -->
                         <td class="text-start"><?= $course['category_title'] ?></td>
                         <td class="text-start"><?= $course['trainer_name'] ?></td>

                         <td class="text-start ">
                              <div class="position-relative">
                                   <img class="rounded-circle" src="<?= e(uploadedImage((string) $course['image_courses'])) ?>" alt=""
                                        style="width: 40px; height: 40px; object-fit: cover;">
                              </div>
                         </td>
                         <td class="text-start"><?= $course['price'] ?></td>
                         <td class="text-start d-sm-flex gap-1 align-items-center p-3 ">
                              <form action="/courseEdit" method="post">
                                   <input type="text" name='id' value='<?= $course['course_id'] ?>' hidden>
                                   <button class="btn btn-sm btn-success" >
                                        <i class=" fas fa-edit">Edit</i>
                                   </button>
                              </form>

                              <button type="button" class="btn btn-sm btn-warning show-detail" data-bs-toggle="modal"
                                   data-bs-target="#detailModal<?= $course['course_id'] ?>">
                                   <i class="fas fa-eye ">Details</i>
                              </button>

                              <form id="delete-form" style="display: inline;">
                                   <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#deleteCourse<?= $course['course_id'] ?>"><i
                                             class="fas fa-trash">Delete</i></button>
                                   <button type="submit" form="delete-form-<?= $course['course_id'] ?>"
                                        class="btn btn-primary" style="display: none;">Confirm Delete</button>
                              </form>
                              <div class="modal fade" id="deleteCourse<?= $course['course_id'] ?>" tabindex="-1"
                                   aria-labelledby="deleteCourse<?= $course['course_id'] ?>" aria-hidden="true">
                                   <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                             <div class="modal-header bg-primary text-white"
                                                  style="border: 3px solid white;">
                                                  <h5 class="modal-title"
                                                       id="deleteCategoryLabel<?= $course['course_id'] ?>">Delete Course
                                                  </h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                       aria-label="Close"></button>
                                             </div>
                                             <div class="modal-body">
                                                  <p class="lead text-dark ">Are you sure you want to delete "<span
                                                            class="text-primary"><?= $course['title'] ?></span>"?</p>
                                             </div>
                                             <div class="modal-footer w-100  ">
                                                  <a href="/viewCourse" class="btn btn-sm  btn-success ">Cancel</a>
                                                  <form action="/deleteCourse?id<?= $course['course_id'] ?>"
                                                       method="post">
                                                       <input type="text" hidden value="<?= $course['course_id'] ?>"
                                                            name='course_id'>
                                                       <button type='submit' class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash">Delete</i>
                                                       </button>
                                                  </form>
                                             </div>
                                        </div>
                                   </div>
                              </div>
     </div>


     <div class="modal fade cd-modal" id="detailModal<?= $course['course_id'] ?>" tabindex="-1"
          aria-labelledby="detailModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" style="max-width: 900px;">
               <div class="modal-content">
                    <div class="cd-head">
                         <h5>
                              <i class="fas fa-graduation-cap"></i> Course Details
                         </h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="cd-body">
                         <!-- Media -->
                         <div class="cd-media">
                              <span class="cd-cat"><?= e($course['category_title']) ?></span>
                              <img src="<?= e(uploadedImage((string) $course['image_courses'])) ?>" alt="<?= e($course['title']) ?>">
                         </div>
                         <!-- Info -->
                         <div class="cd-info">
                              <h3 class="cd-title"><?= e($course['title']) ?></h3>

                              <div class="cd-stats">
                                   <div class="cd-stat">
                                        <div class="k">Price</div>
                                        <div class="v">$<?= e((string) $course['price']) ?></div>
                                   </div>
                                   <div class="cd-stat">
                                        <div class="k">Enrolled</div>
                                        <div class="v"><?= (int) ($course['enrolled'] ?? 0) ?></div>
                                   </div>
                              </div>

                              <div class="cd-rows">
                                   <div class="cd-row">
                                        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg></span>
                                        <span class="lbl">Trainer</span>
                                        <span class="val"><?= e($course['trainer_name']) ?></span>
                                   </div>
                                   <div class="cd-row">
                                        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span>
                                        <span class="lbl">Category</span>
                                        <span class="val"><?= e($course['category_title']) ?></span>
                                   </div>
                              </div>

                              <div class="cd-about">
                                   <div class="h">About this course</div>
                                   <p><?= e($course['description']) ?></p>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     </td>
     </tr>
     <?php endforeach; ?>

     </tbody>
     <!-- <tbody>
               </tbody> -->
     </table>
</div>

<!-- Javascrip for Searching  -->
<script>
const searchCourses = document.querySelector("#searchCourse");
const tbodyChild = document.querySelector("tbody");

searchCourses.addEventListener("keyup", () => {
     const children = tbodyChild.children;
     const searchTerm = searchCourses.value.toLowerCase(); // Convert search input to lowercase

     for (let i = 0; i < children.length; i++) {
          const contentToSearch = children[i].children[1].textContent
               .toLowerCase(); // Convert content to lowercase

          if (contentToSearch.includes(searchTerm)) {
               children[i].style.display = "table-row";
          } else {
               children[i].style.display = "none";
          }
     }
});
</script>