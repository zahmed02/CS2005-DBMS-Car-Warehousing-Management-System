<?php
include('connect.php');
include('header.php');

// Helper to populate car model dropdown
function getModels($conn, $selected = null) {
    $options = "";
    $result = $conn->query("SELECT ModelID, ModelName FROM carmodel");
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($selected == $row['ModelID']) ? 'selected' : '';
        $options .= "<option value='{$row['ModelID']}' $isSelected>" . htmlspecialchars($row['ModelName']) . "</option>";
    }
    return $options;
}

// Field labels for display and forms
$labels = [
    'EngineType'    => 'Engine Type',
    'FuelType'      => 'Fuel Type',
    'Transmission'  => 'Transmission',
    'DriveType'     => 'Drive Type',
    'BodyType'      => 'Body Type',
    'TopSpeed'      => 'Top Speed',
    'FuelCapacity'  => 'Fuel Capacity',
    'BatteryCapacity' => 'Battery Capacity',
    'Warranty'      => 'Warranty',
    'DesignedBy'    => 'Designed By',
    'ManufacturedIn'=> 'Manufactured In'
];

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['SpecID'];
    $conn->query("DELETE FROM carspecifications WHERE SpecID = $id");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['SpecID'];
    $modelID = (int)$_POST['ModelID'];
    $updates = [];
    foreach ($labels as $field => $label) {
        $val = $conn->real_escape_string(trim($_POST[$field]));
        $updates[] = "$field='$val'";
    }
    $launch = $conn->real_escape_string(trim($_POST['LaunchDate']));
    $updates[] = "LaunchDate='$launch'";
    $sql = "UPDATE carspecifications SET ModelID=$modelID, " . implode(', ', $updates) . " WHERE SpecID=$id";
    $conn->query($sql);
}

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $modelID = (int)$_POST['ModelID'];
    $cols = [];
    $vals = [];
    foreach ($labels as $field => $label) {
        $cols[] = $field;
        $vals[] = "'" . $conn->real_escape_string(trim($_POST[$field])) . "'";
    }
    $cols[] = 'LaunchDate';
    $vals[] = "'" . $conn->real_escape_string(trim($_POST['LaunchDate'])) . "'";
    $sql = "INSERT INTO carspecifications (ModelID, " . implode(', ', $cols) . ") VALUES ($modelID, " . implode(', ', $vals) . ")";
    $conn->query($sql);
}
?>
<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading"><h1>Car Specifications</h1></div>
                <button id="addBtn" class="impl_btn">Add Specification</button>
            </div>

            <div id="addForm" style="display:none; width:100%;">
                <h2>Add New Specification</h2>
                <form method="POST">
                    <label>Model:</label>
                    <select name="ModelID" required>
                        <?php echo getModels($conn); ?>
                    </select>
                    <?php foreach ($labels as $field => $label): ?>
                        <label><?php echo $label; ?>:</label>
                        <input type="text" name="<?php echo $field; ?>" required>
                    <?php endforeach; ?>
                    <label>Launch Date:</label>
                    <input type="datetime-local" name="LaunchDate" required>
                    <input type="submit" value="Add Specification" class="impl_btn">
                </form>
            </div>

            <?php
            $res = $conn->query("SELECT * FROM carspecifications");
            while ($row = $res->fetch_assoc()):
                $id = $row['SpecID'];
            ?>
                <div class="col-lg-4 col-md-6" id="box_<?php echo $id; ?>">
                    <div class="impl_fea_car_box" id="view_<?php echo $id; ?>">
                        <div class="impl_fea_car_data">
                            <h2>Spec #<?php echo $id; ?></h2>
                            <ul>
                                <li><span class="impl_fea_title">Model</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['ModelID']); ?></span></li>
                                <?php foreach ($labels as $field => $label): ?>
                                    <li><span class="impl_fea_title"><?php echo $label; ?></span><span class="impl_fea_name"><?php echo htmlspecialchars($row[$field]); ?></span></li>
                                <?php endforeach; ?>
                                <li><span class="impl_fea_title">Launch Date</span><span class="impl_fea_name"><?php echo date('Y-m-d H:i', strtotime($row['LaunchDate'])); ?></span></li>
                            </ul>
                            <button class="impl_btn editBtn" data-id="<?php echo $id; ?>">Edit</button>
                            <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="SpecID" value="<?php echo $id; ?>"><button class="impl_btn">Delete</button></form>
                        </div>
                    </div>
                    <div id="edit_<?php echo $id; ?>" style="display:none; width:100%;">
                        <h3>Edit Spec #<?php echo $id; ?></h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="SpecID" value="<?php echo $id; ?>">
                            <label>Model:</label>
                            <select name="ModelID" required>
                                <?php echo getModels($conn, $row['ModelID']); ?>
                            </select>
                            <?php foreach ($labels as $field => $label): ?>
                                <label><?php echo $label; ?>:</label>
                                <input type="text" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($row[$field]); ?>" required>
                            <?php endforeach; ?>
                            <label>Launch Date:</label>
                            <input type="datetime-local" name="LaunchDate" value="<?php echo date('Y-m-d\TH:i', strtotime($row['LaunchDate'])); ?>" required>
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
    document.getElementById('addBtn').addEventListener('click', function() {
        var f = document.getElementById('addForm');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    });
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            document.getElementById('view_' + id).style.display = 'none';
            document.getElementById('edit_' + id).style.display = 'block';
        });
    });
    document.querySelectorAll('.cancelEdit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            document.getElementById('edit_' + id).style.display = 'none';
            document.getElementById('view_' + id).style.display = 'block';
        });
    });
</script>