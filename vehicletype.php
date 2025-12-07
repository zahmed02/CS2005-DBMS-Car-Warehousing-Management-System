<?php
// vehicletype.php - Vehicle Type Management
include('connect.php');
include('header.php');

// Helper function to check if type is in use
function isTypeInUse($conn, $typeID) {
    $result = $conn->query("SELECT COUNT(*) as count FROM carmodel WHERE TypeID = $typeID");
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['TypeID'];
    
    // Check if type is being used by any car model
    if (isTypeInUse($conn, $id)) {
        $error = "Cannot delete vehicle type because it is assigned to one or more car models.";
    } else {
        $conn->query("DELETE FROM vehicletype WHERE TypeID = $id");
        $success = "Vehicle type deleted successfully.";
    }
}

// Handle add request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TypeName']) && !isset($_POST['action'])) {
    $typeName = $conn->real_escape_string(trim($_POST['TypeName']));
    
    // Check if type already exists
    $check = $conn->query("SELECT TypeID FROM vehicletype WHERE TypeName = '$typeName'");
    if ($check->num_rows > 0) {
        $error = "Vehicle type '$typeName' already exists.";
    } else {
        $conn->query("INSERT INTO vehicletype (TypeName) VALUES ('$typeName')");
        $success = "Vehicle type '$typeName' added successfully.";
    }
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['TypeID'];
    $typeName = $conn->real_escape_string(trim($_POST['TypeName']));
    
    // Check if new name conflicts with existing type
    $check = $conn->query("SELECT TypeID FROM vehicletype WHERE TypeName = '$typeName' AND TypeID != $id");
    if ($check->num_rows > 0) {
        $error = "Vehicle type '$typeName' already exists.";
    } else {
        $conn->query("UPDATE vehicletype SET TypeName = '$typeName' WHERE TypeID = $id");
        $success = "Vehicle type updated successfully.";
    }
}
?>

<style>
    .type-stats {
        background: rgba(0,0,0,0.1);
        padding: 15px;
        border-radius: 8px;
        margin: 10px 0;
    }
    .type-stats h4 {
        color: #fff;
        margin-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 10px;
    }
    .model-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .model-list li {
        padding: 8px 15px;
        margin: 5px 0;
        background: rgba(255,255,255,0.05);
        border-radius: 4px;
        border-left: 3px solid #007bff;
    }
    .model-list li:hover {
        background: rgba(255,255,255,0.1);
    }
    .type-count {
        display: inline-block;
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        text-align: center;
        line-height: 25px;
        font-size: 12px;
        margin-left: 10px;
    }
    .no-models {
        color: #aaa;
        font-style: italic;
        padding: 10px;
    }
</style>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading">
                    <h1>Vehicle Type Management</h1>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <button id="addBtn" class="impl_btn">Add Vehicle Type</button>
            </div>

            <!-- Add Form -->
            <div id="addForm" style="display:none; width:100%; margin: 20px 0; padding: 20px; background: rgba(0,0,0,0.1); border-radius: 8px;">
                <h2 style="color: #fff;">Add New Vehicle Type</h2>
                <form method="POST">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="flex: 1;">
                            <label style="color: #fff; display: block; margin-bottom: 5px;">Type Name:</label>
                            <input type="text" name="TypeName" placeholder="e.g., Sedan, SUV, Truck" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: #fff;" required>
                        </div>
                        <div style="margin-top: 25px;">
                            <input type="submit" value="Add Type" class="impl_btn">
                            <button type="button" id="cancelAdd" class="impl_btn" style="background: #666;">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Vehicle Types List -->
            <?php
            // Get all vehicle types with model counts
            $typesQuery = "SELECT vt.*, COUNT(cm.ModelID) as model_count 
                          FROM vehicletype vt 
                          LEFT JOIN carmodel cm ON vt.TypeID = cm.TypeID 
                          GROUP BY vt.TypeID 
                          ORDER BY vt.TypeName";
            $typesResult = $conn->query($typesQuery);
            
            while ($type = $typesResult->fetch_assoc()):
                $typeID = $type['TypeID'];
            ?>
                <div class="col-lg-6 col-md-6" style="margin-bottom: 30px;">
                    <div class="impl_fea_car_box" id="view_<?php echo $typeID; ?>">
                        <div class="impl_fea_car_data">
                            <h2>
                                <?php echo htmlspecialchars($type['TypeName']); ?>
                                <span class="type-count"><?php echo $type['model_count']; ?></span>
                            </h2>
                            
                            <!-- Type Stats Section -->
                            <div class="type-stats">
                                <h4>Associated Models:</h4>
                                <?php
                                // Get models of this type with brand info
                                $modelsQuery = "SELECT cm.ModelID, cm.ModelName, cb.BrandName 
                                              FROM carmodel cm 
                                              JOIN carbrand cb ON cm.BrandID = cb.BrandID 
                                              WHERE cm.TypeID = $typeID 
                                              ORDER BY cb.BrandName, cm.ModelName";
                                $modelsResult = $conn->query($modelsQuery);
                                
                                if ($modelsResult->num_rows > 0): ?>
                                    <ul class="model-list">
                                    <?php while ($model = $modelsResult->fetch_assoc()): ?>
                                        <li>
                                            <?php echo htmlspecialchars($model['BrandName'] . ' ' . $model['ModelName']); ?>
                                            <a href="carmodel.php#box_<?php echo $model['ModelID']; ?>" style="float: right; color: #007bff; text-decoration: none;">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="no-models">No car models assigned to this type yet.</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Type Specifications Summary -->
                            <div class="type-stats">
                                <h4>Common Specifications:</h4>
                                <?php
                                // Get common specs for this vehicle type
                                $specsQuery = "SELECT cs.BodyType, COUNT(*) as count 
                                             FROM carspecifications cs 
                                             JOIN carmodel cm ON cs.ModelID = cm.ModelID 
                                             WHERE cm.TypeID = $typeID 
                                             GROUP BY cs.BodyType 
                                             ORDER BY count DESC 
                                             LIMIT 3";
                                $specsResult = $conn->query($specsQuery);
                                
                                if ($specsResult->num_rows > 0): ?>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php while ($spec = $specsResult->fetch_assoc()): ?>
                                        <li style="padding: 5px 0; color: #aaa;">
                                            <i class="fa fa-car" style="margin-right: 8px;"></i>
                                            <?php echo htmlspecialchars($spec['BodyType']); ?>
                                            <span style="float: right; color: #777;">(<?php echo $spec['count']; ?>)</span>
                                        </li>
                                    <?php endwhile; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="no-models">No specifications data available.</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <button class="impl_btn editBtn" data-id="<?php echo $typeID; ?>">Edit</button>
                                <?php if ($type['model_count'] == 0): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="TypeID" value="<?php echo $typeID; ?>">
                                        <button class="impl_btn" style="background: #dc3545;">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <button class="impl_btn" style="background: #6c757d; cursor: not-allowed;" title="Cannot delete - assigned to <?php echo $type['model_count']; ?> model(s)">Delete</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Form -->
                    <div id="edit_<?php echo $typeID; ?>" style="display:none; width:100%; padding: 20px; background: rgba(0,0,0,0.1); border-radius: 8px; margin-top: 15px;">
                        <h3 style="color: #fff;">Edit Vehicle Type</h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="TypeID" value="<?php echo $typeID; ?>">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="flex: 1;">
                                    <label style="color: #fff; display: block; margin-bottom: 5px;">Type Name:</label>
                                    <input type="text" name="TypeName" value="<?php echo htmlspecialchars($type['TypeName']); ?>" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: #fff;" required>
                                </div>
                                <div style="margin-top: 25px;">
                                    <button type="submit" class="impl_btn">Save</button>
                                    <button type="button" class="impl_btn cancelEdit" data-id="<?php echo $typeID; ?>" style="background: #666;">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <?php if ($typesResult->num_rows == 0): ?>
                <div class="col-lg-12 col-md-12">
                    <div style="text-align: center; padding: 50px; color: #aaa;">
                        <i class="fa fa-car" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                        <h3>No Vehicle Types Found</h3>
                        <p>Add your first vehicle type to get started.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
    // Toggle Add Form
    document.getElementById('addBtn').addEventListener('click', function() {
        const form = document.getElementById('addForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
    
    document.getElementById('cancelAdd').addEventListener('click', function() {
        document.getElementById('addForm').style.display = 'none';
    });
    
    // Toggle Edit Forms
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('view_' + id).style.display = 'none';
            document.getElementById('edit_' + id).style.display = 'block';
        });
    });
    
    document.querySelectorAll('.cancelEdit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('edit_' + id).style.display = 'none';
            document.getElementById('view_' + id).style.display = 'block';
        });
    });
    
    // Auto-focus on add form input when shown
    document.getElementById('addBtn').addEventListener('click', function() {
        setTimeout(() => {
            const input = document.querySelector('#addForm input[name="TypeName"]');
            if (input) input.focus();
        }, 100);
    });
</script>