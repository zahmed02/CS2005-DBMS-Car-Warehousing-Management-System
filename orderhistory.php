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
    $res = $conn->query("SELECT ModelID, ModelName, PriceRange FROM carmodel ORDER BY ModelName ASC");
    while ($row = $res->fetch_assoc()) {
        $sel = ($row['ModelID'] == $selected) ? 'selected' : '';
        $options .= "<option value='{$row['ModelID']}' data-price='{$row['PriceRange']}' $sel>" . htmlspecialchars($row['ModelName']) . "</option>";
    }
    return $options;
}

function computeTotal($conn, $modelID, $qty) {
    $stmt = $conn->prepare("SELECT PriceRange FROM carmodel WHERE ModelID = ?");
    $stmt->bind_param('i', $modelID);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();
    return $price * $qty;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['OrderID'];
        $conn->query("DELETE FROM orderhistory WHERE OrderID = $id");
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'update') {
        $id       = (int)$_POST['OrderID'];
        $userID   = (int)$_POST['UserID'];
        $modelID  = (int)$_POST['ModelID'];
        $qty      = (int)$_POST['Quantity'];
        $orderDate= $conn->real_escape_string($_POST['OrderDate']);
        $status   = $conn->real_escape_string($_POST['Status']);
        $total    = computeTotal($conn, $modelID, $qty);

        $sql = "UPDATE orderhistory SET
            UserID=$userID,
            ModelID=$modelID,
            OrderDate='$orderDate',
            Quantity=$qty,
            TotalPrice=$total,
            Status='$status'
         WHERE OrderID=$id";
        $conn->query($sql);
    } else {
        // Add
        $userID   = (int)$_POST['UserID'];
        $modelID  = (int)$_POST['ModelID'];
        $qty      = (int)$_POST['Quantity'];
        $orderDate= $conn->real_escape_string($_POST['OrderDate']);
        $status   = $conn->real_escape_string($_POST['Status']);
        $total    = computeTotal($conn, $modelID, $qty);

        $sql = "INSERT INTO orderhistory (UserID, ModelID, OrderDate, Quantity, TotalPrice, Status) VALUES
            ($userID, $modelID, '$orderDate', $qty, $total, '$status')";
        $conn->query($sql);
    }
}
?>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading"><h1>Order History</h1></div>
                <button id="addBtn" class="impl_btn">Add Order</button>
            </div>

            <div id="addForm" style="display:none; width:100%;">
                <h2>Add New Order</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <label>User:</label>
                    <select name="UserID" id="addUser" required>
                        <?php echo getUsers($conn); ?>
                    </select>
                    <label>Model:</label>
                    <select name="ModelID" id="addModel" required>
                        <?php echo getModels($conn); ?>
                    </select>
                    <label>OrderDate:</label>
                    <input type="datetime-local" name="OrderDate" id="addDate" required>
                    <label>Quantity:</label>
                    <input type="number" name="Quantity" id="addQty" min="1" value="1" required>
                    <label>Status:</label>
                    <input type="text" name="Status" id="addStatus" required>
                    <label>TotalPrice:</label>
                    <input type="number" step="0.01" name="TotalPrice" id="addTotal" readonly>
                    <input type="submit" value="Add Order" class="impl_btn">
                </form>
            </div>

            <?php
            $res = $conn->query("SELECT * FROM orderhistory");
            while ($row = $res->fetch_assoc()):
                $id = $row['OrderID'];
            ?>
                <div class="col-lg-4 col-md-6" id="box_<?php echo $id; ?>">
                    <div class="impl_fea_car_box" id="view_<?php echo $id; ?>">
                        <div class="impl_fea_car_data">
                            <h2>Order #<?php echo $id; ?></h2>
                            <ul>
                                <li><span class="impl_fea_title">User</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['UserID']); ?></span></li>
                                <li><span class="impl_fea_title">Model</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['ModelID']); ?></span></li>
                                <li><span class="impl_fea_title">OrderDate</span><span class="impl_fea_name"><?php echo date('Y-m-d H:i', strtotime($row['OrderDate'])); ?></span></li>
                                <li><span class="impl_fea_title">Quantity</span><span class="impl_fea_name"><?php echo $row['Quantity']; ?></span></li>
                                <li><span class="impl_fea_title">TotalPrice</span><span class="impl_fea_name"><?php echo number_format($row['TotalPrice'],2); ?></span></li>
                                <li><span class="impl_fea_title">Status</span><span class="impl_fea_name"><?php echo htmlspecialchars($row['Status']); ?></span></li>
                            </ul>
                            <button class="impl_btn editBtn" data-id="<?php echo $id; ?>">Edit</button>
                            <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="OrderID" value="<?php echo $id; ?>"><button class="impl_btn">Delete</button></form>
                        </div>
                    </div>
                    <div id="edit_<?php echo $id; ?>" style="display:none; width:100%;">
                        <h3>Edit Order #<?php echo $id; ?></h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="OrderID" value="<?php echo $id; ?>">
                            <label>User:</label>
                            <select name="UserID" id="editUser_<?php echo $id; ?>" required><?php echo getUsers($conn,$row['UserID']); ?></select>
                            <label>Model:</label>
                            <select name="ModelID" id="editModel_<?php echo $id; ?>" required><?php echo getModels($conn,$row['ModelID']); ?></select>
                            <label>OrderDate:</label>
                            <input type="datetime-local" name="OrderDate" value="<?php echo date('Y-m-dTH:i', strtotime($row['OrderDate'])); ?>" required>
                            <label>Quantity:</label>
                            <input type="number" name="Quantity" id="editQty_<?php echo $id; ?>" min="1" value="<?php echo $row['Quantity']; ?>" required>
                            <label>Status:</label>
                            <input type="text" name="Status" value="<?php echo htmlspecialchars($row['Status']); ?>" required>
                            <label>TotalPrice:</label>
                            <input type="number" step="0.01" name="TotalPrice" id="editTotal_<?php echo $id; ?>" readonly>
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
// Recalculate
function recalcForm(modelSel, qtyInput, totalInput) {
    const price = parseFloat(modelSel.selectedOptions[0].dataset.price) || 0;
    const qty = parseInt(qtyInput.value) || 0;
    totalInput.value = (price * qty).toFixed(2);
}

// Add form listeners
const addModel = document.getElementById('addModel');
const addQty   = document.getElementById('addQty');
const addTotal = document.getElementById('addTotal');
addModel.onchange = () => recalcForm(addModel, addQty, addTotal);
addQty.oninput   = () => recalcForm(addModel, addQty, addTotal);
addModel.dispatchEvent(new Event('change'));

// Edit forms
<?php
$res = $conn->query("SELECT OrderID FROM orderhistory");
while($r = $res->fetch_assoc()): ?>
(function(){
    const id = <?php echo $r['OrderID']; ?>;
    const mSel = document.getElementById('editModel_'+id);
    const qIn  = document.getElementById('editQty_'+id);
    const tOut = document.getElementById('editTotal_'+id);
    mSel.onchange = () => recalcForm(mSel, qIn, tOut);
    qIn.oninput   = () => recalcForm(mSel, qIn, tOut);
    mSel.dispatchEvent(new Event('change'));
})();
<?php endwhile; ?>

// Toggle
document.getElementById('addBtn').onclick = ()=>{
    const f=document.getElementById('addForm');
    f.style.display = f.style.display==='none'?'block':'none';
};
document.querySelectorAll('.editBtn').forEach(btn=>btn.onclick=()=>{
    const id=btn.dataset.id;
    document.getElementById('view_'+id).style.display='none';
    document.getElementById('edit_'+id).style.display='block';
});
document.querySelectorAll('.cancelEdit').forEach(btn=>btn.onclick=()=>{
    const id=btn.dataset.id;
    document.getElementById('edit_'+id).style.display='none';
    document.getElementById('view_'+id).style.display='block';
});
</script>
