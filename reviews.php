<?php
include('connect.php');
include('header.php');

// Helpers
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

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['ReviewID'];
        $conn->query("DELETE FROM reviews WHERE ReviewID = $id");
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'update') {
        $id        = (int)$_POST['ReviewID'];
        $userID    = (int)$_POST['UserID'];
        $modelID   = (int)$_POST['ModelID'];
        $rating    = (int)$_POST['Rating'];
        $comment   = $conn->real_escape_string($_POST['Comment']);
        $reviewDate= $conn->real_escape_string($_POST['ReviewDate']);

        $sql = "UPDATE reviews SET
            UserID=$userID,
            ModelID=$modelID,
            Rating=$rating,
            Comment='$comment',
            ReviewDate='$reviewDate'
         WHERE ReviewID=$id";
        $conn->query($sql);
    } else {
        // Add new review
        $userID    = (int)$_POST['UserID'];
        $modelID   = (int)$_POST['ModelID'];
        $rating    = (int)$_POST['Rating'];
        $comment   = $conn->real_escape_string($_POST['Comment']);
        $reviewDate= $conn->real_escape_string($_POST['ReviewDate']);

        $sql = "INSERT INTO reviews (UserID, ModelID, Rating, Comment, ReviewDate) VALUES
            ($userID, $modelID, $rating, '$comment', '$reviewDate')";
        $conn->query($sql);
    }
}
?>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading"><h1>Reviews</h1></div>
                <button id="addBtn" class="impl_btn">Add Review</button>
            </div>

            <div id="addForm" style="display:none; width:100%;">
                <h2>Add New Review</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <label>User:</label>
                    <select name="UserID" required>
                        <?php echo getUsers($conn); ?>
                    </select>
                    <label>Model:</label>
                    <select name="ModelID" required>
                        <?php echo getModels($conn); ?>
                    </select>
                    <label>Rating:</label>
                    <input type="number" name="Rating" min="1" max="5" required>
                    <label>Comment:</label>
                    <input type="text" name="Comment" maxlength="255" required>
                    <label>ReviewDate:</label>
                    <input type="datetime-local" name="ReviewDate" required>
                    <input type="submit" value="Add Review" class="impl_btn">
                </form>
            </div>

            <?php
            $res = $conn->query("SELECT * FROM reviews");
            while ($row = $res->fetch_assoc()):
                $id = $row['ReviewID'];
            ?>
                <div class="col-lg-12 col-md-12" id="box_<?php echo $id; ?>">
                    <div class="impl_fea_car_box" id="view_<?php echo $id; ?>">
                        <div class="impl_fea_car_data">
                            <h2>Review #<?php echo $id; ?></h2>
                            <ul>
                                <li><span class="impl_fea_title">User</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['UserID']); ?></span></li>
                                <li><span class="impl_fea_title">Model</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['ModelID']); ?></span></li>
                                <li><span class="impl_fea_title">Rating</span><span class="impl_fea_name"><?php echo $row['Rating']; ?></span></li>
                                <li><span class="impl_fea_title">Comment</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['Comment']); ?></span></li>
                                <li><span class="impl_fea_title">ReviewDate</span><span class="impl_fea_name"><?php echo date('Y-m-d H:i', strtotime($row['ReviewDate'])); ?></span></li>
                            </ul>
                            <button class="impl_btn editBtn" data-id="<?php echo $id; ?>">Edit</button>
                            <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="ReviewID" value="<?php echo $id; ?>"><button class="impl_btn">Delete</button></form>
                        </div>
                    </div>
                    <div id="edit_<?php echo $id; ?>" style="display:none; width:100%;">
                        <h3>Edit Review #<?php echo $id; ?></h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="ReviewID" value="<?php echo $id; ?>">
                            <label>User:</label>
                            <select name="UserID" required><?php echo getUsers($conn,$row['UserID']); ?></select>
                            <label>Model:</label>
                            <select name="ModelID" required><?php echo getModels($conn,$row['ModelID']); ?></select>
                            <label>Rating:</label>
                            <input type="number" name="Rating" min="1" max="5" value="<?php echo $row['Rating']; ?>" required>
                            <label>Comment:</label>
                            <input type="text" name="Comment" maxlength="500" value="<?php echo htmlspecialchars($row['Comment']); ?>" required>
                            <label>ReviewDate:</label>
                            <input type="datetime-local" name="ReviewDate" value="<?php echo date('Y-m-dTH:i', strtotime($row['ReviewDate'])); ?>" required>
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
// Toggle panels
document.getElementById('addBtn').onclick = ()=>{
    const f = document.getElementById('addForm');
    f.style.display = f.style.display==='none' ? 'block' : 'none';
};
document.querySelectorAll('.editBtn').forEach(btn => btn.onclick = ()=>{
    const id = btn.dataset.id;
    document.getElementById('view_' + id).style.display = 'none';
    document.getElementById('edit_' + id).style.display = 'block';
});
document.querySelectorAll('.cancelEdit').forEach(btn => btn.onclick = ()=>{
    const id = btn.dataset.id;
    document.getElementById('edit_' + id).style.display = 'none';
    document.getElementById('view_' + id).style.display = 'block';
});
</script>
