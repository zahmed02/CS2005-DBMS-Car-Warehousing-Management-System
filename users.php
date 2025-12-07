<?php
include('connect.php');
include('header.php');

// Handle POST for adding a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'add') {
        $fullName = $conn->real_escape_string($_POST['FullName']);
        $email = $conn->real_escape_string($_POST['Email']);
        $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
        $phoneNumber = $conn->real_escape_string($_POST['PhoneNumber']);
        $address = $conn->real_escape_string($_POST['Address']);
        $createdAt = date('Y-m-d H:i:s');

        // Handle image upload
        $userImage = '';
        if (isset($_FILES['UserImage']) && $_FILES['UserImage']['error'] == 0) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES["UserImage"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            // Check file type
            if (in_array($imageFileType, $allowedTypes)) {
                move_uploaded_file($_FILES["UserImage"]["tmp_name"], $targetFile);
                $userImage = $conn->real_escape_string($targetFile);
            }
        }

        $sql = "INSERT INTO users (FullName, Email, Password, PhoneNumber, Address, CreatedAt, UserImage) VALUES
            ('$fullName', '$email', '$password', '$phoneNumber', '$address', '$createdAt', '$userImage')";
        $conn->query($sql);
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'update') {
        $userID = (int)$_POST['UserID'];
        $fullName = $conn->real_escape_string($_POST['FullName']);
        $email = $conn->real_escape_string($_POST['Email']);
        $phoneNumber = $conn->real_escape_string($_POST['PhoneNumber']);
        $address = $conn->real_escape_string($_POST['Address']);

        // Handle image upload
        $userImage = '';
        if (isset($_FILES['UserImage']) && $_FILES['UserImage']['error'] == 0) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES["UserImage"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            // Check file type
            if (in_array($imageFileType, $allowedTypes)) {
                move_uploaded_file($_FILES["UserImage"]["tmp_name"], $targetFile);
                $userImage = $conn->real_escape_string($targetFile);
            }
        }

        $sql = "UPDATE users SET
            FullName='$fullName',
            Email='$email',
            PhoneNumber='$phoneNumber',
            Address='$address'" . ($userImage ? ", UserImage='$userImage'" : "") . "
         WHERE UserID=$userID";
        $conn->query($sql);
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $userID = (int)$_POST['UserID'];
        $conn->query("DELETE FROM users WHERE UserID = $userID");
    }
}

// Fetch users from the database
$res = $conn->query("SELECT * FROM users ORDER BY FullName ASC");
?>

<style>
    /* Bright white text with improved contrast */
    body {
        color: #ffffff;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .table th, .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }
    
    .table th {
        background-color: rgba(0, 0, 0, 0.2);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .impl_heading h1 {
        color: #ffffff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    
    #addUser Form, #addUser Form input, #addUser Form select {
        color: #ffffff;
    }
    
    #addUser Form label {
        color: #ffffff;
        display: block;
        margin-top: 10px;
        opacity: 0.9;
    }
    
    #addUser Form input[type="text"],
    #addUser Form input[type="email"],
    #addUser Form input[type="password"],
    #addUser Form input[type="datetime-local"],
    #addUser Form select {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        padding: 8px 12px;
        width: 100%;
        max-width: 400px;
        margin-bottom: 10px;
    }
    
    .user-image {
        display: none;
        position: absolute;
        z-index: 10;
        width: 100px;
        height: auto;
        border-radius: 5px;
    }

    .user-name:hover .user-image {
        display: block;
    }
</style>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading"><h1>User Management</h1></div>
                <button id="addUser Btn" class="impl_btn">Add User</button>
            </div>

            <div id="addUser Form" style="display:none; width:100%; padding: 20px; background-color: rgba(0, 0, 0, 0.2); border-radius: 8px; margin: 20px 0;">
                <h2 style="color: #ffffff; margin-bottom: 20px;">Add New User</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <label>Full Name:</label>
                    <input type="text" name="FullName" required>
                    <label>Email:</label>
                    <input type="email" name="Email" required>
                    <label>Password:</label>
                    <input type="password" name="Password" required>
                    <label>Phone Number:</label>
                    <input type="text" name="PhoneNumber" required>
                    <label>Address:</label>
                    <input type="text" name="Address" required>
                    <label>User Image:</label>
                    <input type="file" name="UserImage" accept="image/*">
                    <input type="submit" value="Add User" class="impl_btn">
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>UserID</th>
                            <th>Image</th>
                            <th>FullName</th>
                            <th>Email</th>
                            <th>PhoneNumber</th>
                            <th>Address</th>
                            <th>CreatedAt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                                <td class="user-image-cell">
                                    <?php if (!empty($row['UserImage'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['UserImage']); ?>" 
                                             alt="Profile photo of <?php echo htmlspecialchars($row['FullName']); ?>" 
                                             style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                                    <?php else: ?>
                                        <img src="https://placehold.co/50x50?text=No+Image" 
                                             alt="Default profile placeholder"
                                             style="width:50px; height:50px; border-radius:50%;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                                <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                                <td><?php echo htmlspecialchars($row['Address']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['CreatedAt'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <button class="impl_btn editBtn" data-id="<?php echo $row['UserID']; ?>">Edit</button>
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="UserID" value="<?php echo $row['UserID']; ?>">
                                            <button class="impl_btn">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <div id="edit_<?php echo $row['UserID']; ?>" style="display:none; width:100%;">
                                <h3>Edit User #<?php echo $row['UserID']; ?></h3>
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="UserID" value="<?php echo $row['UserID']; ?>">
                                    <label>Full Name:</label>
                                    <input type="text" name="FullName" value="<?php echo htmlspecialchars($row['FullName']); ?>" required>
                                    <label>Email:</label>
                                    <input type="email" name="Email" value="<?php echo htmlspecialchars($row['Email']); ?>" required>
                                    <label>Phone Number:</label>
                                    <input type="text" name="PhoneNumber" value="<?php echo htmlspecialchars($row['PhoneNumber']); ?>" required>
                                    <label>Address:</label>
                                    <input type="text" name="Address" value="<?php echo htmlspecialchars($row['Address']); ?>" required>
                                    <label>User Image:</label>
                                    <input type="file" name="UserImage" accept="image/*">
                                    <button type="submit" class="impl_btn">Save</button>
                                    <button type="button" class="impl_btn cancelEdit" data-id="<?php echo $row['UserID']; ?>">Cancel</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
document.getElementById('addUser Btn').onclick = () => {
    document.getElementById('addUser Form').style.display = 'block';
};

document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
        const id = btn.dataset.id;
        document.getElementById('edit_' + id).style.display = 'block';
    };
});

document.querySelectorAll('.cancelEdit').forEach(btn => {
    btn.onclick = () => {
        const id = btn.dataset.id;
        document.getElementById('edit_' + id).style.display = 'none';
    };
});
</script>
