<?php include('../includes/config.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Study Materials</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Student</a></li>
                    <li class="breadcrumb-item active">Study Materials</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="card">
          <div class="card-header py-2">
            <h3 class="card-title">Study Materials</h3>
          </div>
          <div class="card-body">
            <div class="table-responsive bg-white">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>Title</th>
                    <th>Attachment</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                    <?php
                    // Ensure std_id is set
                    $std_id = isset($_GET['std_id']) ? $_GET['std_id'] : null;

                    if (!$std_id) {
                        echo "<tr><td colspan='6'>Student ID is missing</td></tr>";
                    } else {
                        // Fetch student metadata
                        $usermeta = get_user_metadata($std_id);
                        $class = isset($usermeta['class']) ? $usermeta['class'] : null;

                        if (!$class) {
                            echo "<tr><td colspan='6'>No class assigned to student</td></tr>";
                        } else {
                            // Fetch study materials for the student's class
                            $query = mysqli_query($db_conn, "SELECT * FROM `posts` as p 
                            INNER JOIN `metadata` as m ON p.id = m.item_id 
                            WHERE p.`type` = 'study-material' 
                            AND m.meta_key = 'class' 
                            AND m.meta_value = '$class'");

                            if (!$query) {
                                echo "<tr><td colspan='6'>Query failed: " . mysqli_error($db_conn) . "</td></tr>";
                            } else {
                                $count = 1;
                                while ($att = mysqli_fetch_object($query)) {
                                    // Get class name
                                    $class_obj = get_post(['id' => $class]);
                                    $class_title = isset($class_obj->title) ? $class_obj->title : 'Unknown';

                                    // Get subject
                                    $subject_id = get_metadata($att->item_id, 'subject')[0]->meta_value;
                                    $subject_obj = get_post(['id' => $subject_id]);
                                    $subject_title = isset($subject_obj->title) ? $subject_obj->title : 'Unknown';

                                    // Get file attachment
                                    $file_attachment = get_metadata($att->item_id, 'file_attachment')[0]->meta_value;
                                    ?>
                                    <tr>
                                        <td><?=$count++?></td>
                                        <td><?=$att->title?></td>
                                        <td><a href="<?="../dist/uploads/".$file_attachment; ?>">Download File</a></td>
                                        <td><?=$class_title?></td>
                                        <td><?=$subject_title?></td>
                                        <td><?=$att->publish_date?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- /.row -->
    </div>
</section>

<?php include('footer.php'); ?>
