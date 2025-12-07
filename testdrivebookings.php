<?php
include('connect.php');
include('header.php');

// Helper functions
function getUsers($conn, $selected = null) {
    $options = "";
    $res = $conn->query("SELECT UserID, FullName FROM users ORDER BY FullName ASC");
    while ($row = $res->fetch_assoc()) {
        $sel = ($row['UserID'] == $selected) ? 'selected' : '';
        $options .= "<option value='{$row['UserID']}' $sel>" . htmlspecialchars($row['FullName']) . "</option>";
    }
    return $options;
}

function getModels($conn, $selected = null) {
    $options = "";
    $res = $conn->query("SELECT ModelID, ModelName FROM carmodel ORDER BY ModelName ASC");
    while ($row = $res->fetch_assoc()) {
        $sel = ($row['ModelID'] == $selected) ? 'selected' : '';
        $options .= "<option value='{$row['ModelID']}' $sel>" . htmlspecialchars($row['ModelName']) . "</option>";
    }
    return $options;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['BookingID'];
        $conn->query("DELETE FROM testdrivebookings WHERE BookingID = $id");
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'update') {
        $id       = (int)$_POST['BookingID'];
        $userID   = (int)$_POST['UserID'];
        $modelID  = (int)$_POST['ModelID'];
        $prefDate = $conn->real_escape_string($_POST['PreferredDate']);
        $status   = $conn->real_escape_string($_POST['Status']);
        $bookingDate = $conn->real_escape_string($_POST['BookingDate']);

        $sql = "UPDATE testdrivebookings SET
            UserID=$userID,
            ModelID=$modelID,
            PreferredDate='$prefDate',
            Status='$status',
            BookingDate='$bookingDate'
         WHERE BookingID=$id";
        $conn->query($sql);
    } else {
        $userID   = (int)$_POST['UserID'];
        $modelID  = (int)$_POST['ModelID'];
        $prefDate = $conn->real_escape_string($_POST['PreferredDate']);
        $status   = $conn->real_escape_string($_POST['Status']);
        $bookingDate = $conn->real_escape_string($_POST['BookingDate']);

        $sql = "INSERT INTO testdrivebookings (UserID, ModelID, PreferredDate, Status, BookingDate) VALUES
            ($userID, $modelID, '$prefDate', '$status', '$bookingDate')";
        $conn->query($sql);
    }
}
?>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading"><h1>Test Drive Bookings</h1></div>
                <button id="addBtn" class="impl_btn">Add Booking</button>
            </div>

            <div id="addForm" style="display:none; width:100%;">
                <h2>Add New Booking</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <label>User:</label>
                    <select name="UserID" required><?php echo getUsers($conn); ?></select>
                    <label>Model:</label>
                    <select name="ModelID" required><?php echo getModels($conn); ?></select>
                    <label>Preferred Date:</label>
                    <input type="datetime-local" name="PreferredDate" required>
                    <label>Status:</label>
                    <input type="text" name="Status" required>
                    <label>Booking Date:</label>
                    <input type="datetime-local" name="BookingDate" required>
                    <input type="submit" value="Add Booking" class="impl_btn">
                </form>
            </div>

            <?php
            $res = $conn->query("SELECT * FROM testdrivebookings");
            while ($row = $res->fetch_assoc()):
                $id = $row['BookingID'];
            ?>
                <div class="col-lg-12 col-md-12" id="box_<?php echo $id; ?>">
                    <div class="impl_fea_car_box" id="view_<?php echo $id; ?>">
                        <div class="impl_fea_car_data">
                            <h2>Booking #<?php echo $id; ?></h2>
                            <ul>
                                <li><span class="impl_fea_title">User</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['UserID']); ?></span></li>
                                <li><span class="impl_fea_title">Model</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['ModelID']); ?></span></li>
                                <li><span class="impl_fea_title">Preferred Date</span><span class="impl_fea_name"><?php echo date('Y-m-d H:i', strtotime($row['PreferredDate'])); ?></span></li>
                                <li><span class="impl_fea_title">Status</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['Status']); ?></span></li>
                                <li><span class="impl_fea_title">Booking Date</span><span class="impl_fea_name"><?php echo date('Y-m-d H:i', strtotime($row['BookingDate'])); ?></span></li>
                            </ul>
                            <button class="impl_btn editBtn" data-id="<?php echo $id; ?>">Edit</button>
                            <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="BookingID" value="<?php echo $id; ?>"><button class="impl_btn">Delete</button></form>
                        </div>
                    </div>
                    <div id="edit_<?php echo $id; ?>" style="display:none; width:100%;">
                        <h3>Edit Booking #<?php echo $id; ?></h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="BookingID" value="<?php echo $id; ?>">
                            <label>User:</label>
                            <select name="UserID" required><?php echo getUsers($conn, $row['UserID']); ?></select>
                            <label>Model:</label>
                            <select name="ModelID" required><?php echo getModels($conn, $row['ModelID']); ?></select>
                            <label>Preferred Date:</label>
                            <input type="datetime-local" name="PreferredDate" value="<?php echo date('Y-m-d\TH:i', strtotime($row['PreferredDate'])); ?>" required>
                            <label>Status:</label>
                            <input type="text" name="Status" value="<?php echo htmlspecialchars($row['Status']); ?>" required>
                            <label>Booking Date:</label>
                            <input type="datetime-local" name="BookingDate" value="<?php echo date('Y-m-d\TH:i', strtotime($row['BookingDate'])); ?>" required>
                            <button type="submit" class="impl_btn">Save</button>
                            <button type="button" class="impl_btn cancelEdit" data-id="<?php echo $id; ?>">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
document.getElementById('addBtn').onclick = () => {
  document.getElementById('addForm').style.display = 'block';
};

document.querySelectorAll('.editBtn').forEach(btn => {
  btn.onclick = () => {
    const id = btn.dataset.id;
    document.getElementById('view_' + id).style.display = 'none';
    document.getElementById('edit_' + id).style.display = 'block';
  };
});

document.querySelectorAll('.cancelEdit').forEach(btn => {
  btn.onclick = () => {
    const id = btn.dataset.id;
    document.getElementById('edit_' + id).style.display = 'none';
    document.getElementById('view_' + id).style.display = 'block';
  };
});
</script>
