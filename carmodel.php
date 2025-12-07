<?php
include('connect.php');
include('header.php');

// Helper functions for dropdowns
define("UPLOAD_DIR", 'uploads/carmodel/');
function getBrands($conn, $selected = null) {
    $options = "";
    $result = $conn->query("SELECT BrandID, BrandName FROM carbrand");
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($selected == $row['BrandID']) ? 'selected' : '';
        $options .= "<option value='{$row['BrandID']}' $isSelected>{$row['BrandName']}</option>";
    }
    return $options;
}

function getVehicleTypes($conn, $selected = null) {
    $options = "";
    $result = $conn->query("SELECT TypeID, TypeName FROM vehicletype");
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($selected == $row['TypeID']) ? 'selected' : '';
        $options .= "<option value='{$row['TypeID']}' $isSelected>{$row['TypeName']}</option>";
    }
    return $options;
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['ModelID'];
    $res = $conn->query("SELECT MainImage, RearImage FROM carmodel WHERE ModelID = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['MainImage']) && file_exists($row['MainImage'])) unlink($row['MainImage']);
        if (!empty($row['RearImage']) && file_exists($row['RearImage'])) unlink($row['RearImage']);
    }
    $conn->query("DELETE FROM carmodel WHERE ModelID = $id");
}

// Handle update request
// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['ModelID'];
    $modelName = $conn->real_escape_string(trim($_POST['ModelName']));
    $brandID = (int)$_POST['BrandID'];
    $typeID = (int)$_POST['TypeID'];
    $priceRange = $conn->real_escape_string(trim($_POST['PriceRange']));
    $modelYear = $conn->real_escape_string(trim($_POST['ModelYear']));
    $place = $conn->real_escape_string(trim($_POST['ManufacturePlace']));
    $inStock = isset($_POST['InStock']) ? 1 : 0;
    $qty = (int)$_POST['AvailableQty'];
    $sponsor = $conn->real_escape_string(trim($_POST['SponsoredBy']));
    $trending = isset($_POST['Trending']) ? 1 : 0;

    $updates = [];
    
    // MainImage - FIXED
    if (isset($_FILES['MainImage']) && $_FILES['MainImage']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
        $fn = uniqid() . '_main_' . basename($_FILES['MainImage']['name']);
        $path = UPLOAD_DIR . $fn;
        if (getimagesize($_FILES['MainImage']['tmp_name']) && move_uploaded_file($_FILES['MainImage']['tmp_name'], $path)) {
            // Get old image path properly
            $oldResult = $conn->query("SELECT MainImage FROM carmodel WHERE ModelID=$id");
            if ($oldResult && $oldRow = $oldResult->fetch_assoc()) {
                $oldImage = $oldRow['MainImage'];
                if (!empty($oldImage) && file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            $updates[] = "MainImage='$path'";
        }
    }
    
    // RearImage - FIXED
    if (isset($_FILES['RearImage']) && $_FILES['RearImage']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
        $fn = uniqid() . '_rear_' . basename($_FILES['RearImage']['name']);
        $path = UPLOAD_DIR . $fn;
        if (getimagesize($_FILES['RearImage']['tmp_name']) && move_uploaded_file($_FILES['RearImage']['tmp_name'], $path)) {
            // Get old image path properly
            $oldResult = $conn->query("SELECT RearImage FROM carmodel WHERE ModelID=$id");
            if ($oldResult && $oldRow = $oldResult->fetch_assoc()) {
                $oldImage = $oldRow['RearImage'];
                if (!empty($oldImage) && file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            $updates[] = "RearImage='$path'";
        }
    }

    // Build SQL query
    $sql = "UPDATE carmodel SET
        ModelName='$modelName', 
        BrandID=$brandID, 
        TypeID=$typeID, 
        PriceRange=$priceRange,
        ModelYear='$modelYear', 
        ManufacturePlace='$place', 
        InStock=$inStock,
        AvailableQty=$qty, 
        SponsoredBy='$sponsor', 
        Trending=$trending";
    
    // Add image updates if any
    if (!empty($updates)) {
        $sql .= ', ' . implode(', ', $updates);
    }
    
    $sql .= " WHERE ModelID=$id";
    
    // Execute with error handling
    if ($conn->query($sql)) {
        // Success - you could add a success message here
    } else {
        // Error handling
        echo "Error updating car model: " . $conn->error;
    }
}

// Handle add request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $modelName = $conn->real_escape_string(trim($_POST['ModelName']));
    $brandID = (int)$_POST['BrandID'];
    $typeID = (int)$_POST['TypeID'];
    $priceRange = $conn->real_escape_string(trim($_POST['PriceRange']));
    $modelYear = $conn->real_escape_string(trim($_POST['ModelYear']));
    $place = $conn->real_escape_string(trim($_POST['ManufacturePlace']));
    $inStock = isset($_POST['InStock']) ? 1 : 0;
    $qty = (int)$_POST['AvailableQty'];
    $sponsor = $conn->real_escape_string(trim($_POST['SponsoredBy']));
    $trending = isset($_POST['Trending']) ? 1 : 0;

    $mainImg = '';
    if (isset($_FILES['MainImage']) && $_FILES['MainImage']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
        $f = uniqid() . '_main_' . basename($_FILES['MainImage']['name']);
        $p = UPLOAD_DIR . $f;
        if (getimagesize($_FILES['MainImage']['tmp_name']) && move_uploaded_file($_FILES['MainImage']['tmp_name'], $p)) {
            $mainImg = $p;
        }
    }
    $rearImg = '';
    if (isset($_FILES['RearImage']) && $_FILES['RearImage']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
        $f = uniqid() . '_rear_' . basename($_FILES['RearImage']['name']);
        $p = UPLOAD_DIR . $f;
        if (getimagesize($_FILES['RearImage']['tmp_name']) && move_uploaded_file($_FILES['RearImage']['tmp_name'], $p)) {
            $rearImg = $p;
        }
    }
    $ins = "INSERT INTO carmodel (ModelName,BrandID,TypeID,PriceRange,ModelYear,ManufacturePlace,InStock,AvailableQty,SponsoredBy,MainImage,RearImage,Trending)
        VALUES ('$modelName',$brandID,$typeID,$priceRange,'$modelYear','$place',$inStock,$qty,'$sponsor','$mainImg','$rearImg',$trending)";
    $conn->query($ins);
}
?>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading"><h1>Car Models</h1></div>
                <button id="addBtn" class="impl_btn">Add Car Model</button>
            </div>

            <div id="addForm" style="display:none; width:100%;">
                <h2>Add New Model</h2>
                <form method="POST" enctype="multipart/form-data">
                    <label>Model Name:</label>
                    <input type="text" name="ModelName" required>
                    <label>Main Image:</label>
                    <input type="file" id="mainImgInput" name="MainImage" accept="image/*" required>
                    <img id="mainPreview" style="display:none; max-width:100px;"><br>
                    <label>Rear Image:</label>
                    <input type="file" id="rearImgInput" name="RearImage" accept="image/*" required>
                    <img id="rearPreview" style="display:none; max-width:100px;"><br>
                    <label>Brand:</label>
                    <select name="BrandID" required>
                        <?php echo getBrands($conn); ?>
                    </select>
                    <label>Vehicle Type:</label>
                    <select name="TypeID" required>
                        <?php echo getVehicleTypes($conn); ?>
                    </select>
                    <label>Price Range:</label>
                    <input type="number" name="PriceRange" required>
                    <label>Model Year:</label>
                    <input type="datetime-local" name="ModelYear" required>
                    <label>Manufacture Place:</label>
                    <input type="text" name="ManufacturePlace" required>
                    <label>In Stock:</label>
                    <input type="checkbox" name="InStock">
                    <label>Available Qty:</label>
                    <input type="number" name="AvailableQty" required>
                    <label>Sponsored By:</label>
                    <input type="text" name="SponsoredBy" required>
                    <label>Trending:</label>
                    <input type="checkbox" name="Trending">
                    <input type="submit" value="Add Model">
                </form>
            </div>

            <?php
            $res = $conn->query("SELECT * FROM carmodel");
            while ($row = $res->fetch_assoc()) {
                $id = $row['ModelID'];
            ?>
                <div class="col-lg-4 col-md-6" id="box_<?php echo $id; ?>">
                    <div class="impl_fea_car_box" id="view_<?php echo $id; ?>">
                        <div class="impl_fea_car_img">
                            <img src="<?php echo $row['MainImage']; ?>" class="img-fluid">
                        </div>
                        <div class="impl_fea_car_data">
                            <h2><?php echo $row['ModelName']; ?></h2>
                            <ul>
                                <li><span class="impl_fea_title">Brand</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['BrandID']); ?></span></li>
                                <li><span class="impl_fea_title">Type</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['TypeID']); ?></span></li>
                                <li><span class="impl_fea_title">Price</span><span class="impl_fea_name"><?php echo $row['PriceRange']; ?></span></li>
                                <li><span class="impl_fea_title">Year</span><span class="impl_fea_name"><?php echo date('Y', strtotime($row['ModelYear'])); ?></span></li>
                                <li><span class="impl_fea_title">Place</span><span class="impl_fea_name"><?php echo $row['ManufacturePlace']; ?></span></li>
                                <li><span class="impl_fea_title">In Stock</span><span class="impl_fea_name"><?php echo $row['InStock'] ? 'Yes':'No'; ?></span></li>
                                <li><span class="impl_fea_title">Qty</span><span class="impl_fea_name"><?php echo $row['AvailableQty']; ?></span></li>
                                <li><span class="impl_fea_title">Sponsored</span><span class="impl_fea_name"><?php echo $row['SponsoredBy']; ?></span></li>
                                <li><span class="impl_fea_title">Trending</span><span class="impl_fea_name"><?php echo $row['Trending'] ? 'Yes':'No'; ?></span></li>
                            </ul>
                            <button class="impl_btn editBtn" data-id="<?php echo $id; ?>">Edit</button>
                            <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="ModelID" value="<?php echo $id; ?>"><button class="impl_btn">Delete</button></form>
                        </div>
                    </div>
                    <div id="edit_<?php echo $id; ?>" style="display:none; width:100%;">
                        <h3>Edit <?php echo $row['ModelName']; ?></h3>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="ModelID" value="<?php echo $id; ?>">
                            <label>Model Name:</label>
                            <input type="text" name="ModelName" value="<?php echo htmlspecialchars($row['ModelName']); ?>" required>
                            <label>Main Image:</label>
                            <input type="file" name="MainImage" accept="image/*">
                            <img src="<?php echo $row['MainImage']; ?>" style="max-width:100px;"><br>
                            <label>Rear Image:</label>
                            <input type="file" name="RearImage" accept="image/*">
                            <img src="<?php echo $row['RearImage']; ?>" style="max-width:100px;"><br>
                            <label>Brand:</label>
                            <select name="BrandID" required>
                                <?php echo getBrands($conn, $row['BrandID']); ?>
                            </select>
                            <label>Vehicle Type:</label>
                            <select name="TypeID" required>
                                <?php echo getVehicleTypes($conn, $row['TypeID']); ?>
                            </select>
                            <label>Price Range:</label>
                            <input type="number" name="PriceRange" value="<?php echo $row['PriceRange']; ?>" required>
                            <label>Model Year:</label>
                            <input type="datetime-local" name="ModelYear" value="<?php echo date('Y-m-dTH:i', strtotime($row['ModelYear'])); ?>" required>
                            <label>Manufacture Place:</label>
                            <input type="text" name="ManufacturePlace" value="<?php echo htmlspecialchars($row['ManufacturePlace']); ?>" required>
                            <label>In Stock:</label>
                            <input type="checkbox" name="InStock" <?php echo $row['InStock'] ? 'checked' : ''; ?>>
                            <label>Available Qty:</label>
                            <input type="number" name="AvailableQty" value="<?php echo $row['AvailableQty']; ?>" required>
                            <label>Sponsored By:</label>
                            <input type="text" name="SponsoredBy" value="<?php echo htmlspecialchars($row['SponsoredBy']); ?>" required>
                            <label>Trending:</label>
                            <input type="checkbox" name="Trending" <?php echo $row['Trending'] ? 'checked' : ''; ?>>
                            <button type="submit" class="impl_btn">Save</button>
                            <button type="button" class="impl_btn cancelEdit" data-id="<?php echo $id; ?>">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
    document.getElementById('addBtn').addEventListener('click', function() {
        var f = document.getElementById('addForm');
        f.style.display = (f.style.display === 'none') ? 'block' : 'none';
    });
    // Image previews
    document.getElementById('mainImgInput').addEventListener('change', function(e) {
        var f = e.target.files[0], p = document.getElementById('mainPreview');
        if (f) { var r = new FileReader(); r.onload = function(ev){p.src=ev.target.result;p.style.display='block';}; r.readAsDataURL(f);} else p.style.display='none';
    });
    document.getElementById('rearImgInput').addEventListener('change', function(e) {
        var f = e.target.files[0], p = document.getElementById('rearPreview');
        if (f) { var r = new FileReader(); r.onload = function(ev){p.src=ev.target.result;p.style.display='block';}; r.readAsDataURL(f);} else p.style.display='none';
    });
    // Toggle edit forms
    document.querySelectorAll('.editBtn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.getAttribute('data-id');
            document.getElementById('view_'+id).style.display='none';
            document.getElementById('edit_'+id).style.display='block';
        });
    });
    document.querySelectorAll('.cancelEdit').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.getAttribute('data-id');
            document.getElementById('edit_'+id).style.display='none';
            document.getElementById('view_'+id).style.display='block';
        });
    });
</script>
