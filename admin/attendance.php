<?php
include('../includes/config.php');
include('header.php');
include('sidebar.php');

// Ensure std_id is set and valid
$std_id = isset($_GET['std_id']) ? intval($_GET['std_id']) : null;

if (!$std_id) {
    echo "<p style='color: red;'>⚠️ Error: Student ID is missing!</p>";
    exit;
}

// Fetch student details
$usermeta = get_user_metadata($std_id);
$user_data = get_users(array('id' => $std_id));

?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Student Details</h3>
    </div>
    <div class="card-body">
        <strong>Name: </strong> 
        <?= (!empty($user_data) && isset($user_data[0])) ? htmlspecialchars($user_data[0]->name) : "<span style='color:red;'>Name not found</span>"; ?> <br>

        <strong>Class: </strong> 
        <?= isset($usermeta['class']) ? htmlspecialchars($usermeta['class']) : "<span style='color:red;'>Class not available</span>"; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Attendance Records</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Sign-in Time</th>
                    <th>Sign-out Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get the current month and year
                $current_month = strtolower(date('F'));
                $current_year = date('Y');

                // Fetch attendance records
                $sql = "SELECT * FROM `attendance` 
                        WHERE `attendance_month` = ? 
                        AND YEAR(`current_session`) = ? 
                        AND `student_id` = ?";

                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssi", $current_month, $current_year, $std_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && $row = mysqli_fetch_assoc($result)) {
                    $attendance_data = unserialize($row['attendance_value']);

                    if (is_array($attendance_data) && !empty($attendance_data)) {
                        foreach ($attendance_data as $date => $value) { ?>
                            <tr>
                                <td><?= htmlspecialchars($date); ?></td>
                                <td><?= !empty($value['signin_at']) ? "<span style='color:green;'>Present</span>" : "<span style='color:red;'>Absent</span>"; ?></td>
                                <td><?= !empty($value['signin_at']) ? date('d-m-Y h:i:s', $value['signin_at']) : '-'; ?></td>
                                <td><?= !empty($value['signout_at']) ? date('d-m-Y h:i:s', $value['signout_at']) : '-'; ?></td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; color:red;'>No attendance data found</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; color:red;'>No attendance record found</td></tr>";
                }

                mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>  
