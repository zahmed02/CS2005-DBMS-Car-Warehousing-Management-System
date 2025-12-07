<?php
// Include database connection
include('../connect.php');

// Add this function to handle file uploads with transactions
function handleFileUploadWithTransaction($conn, $file, $uploadDir, $currentFile = '') {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($file['name']);
        $uploadPath = $uploadDir . $filename;
        
        if (getimagesize($file['tmp_name']) && move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Delete old file if exists
            if (!empty($currentFile) && file_exists($currentFile)) {
                unlink($currentFile);
            }
            return $uploadPath;
        }
    }
    return $currentFile; // Return current file if upload fails
}

if (isset($_POST['login'])) {

    if (isset($_POST['Email']) && isset($_POST['Password'])) {
        $email = $conn->real_escape_string(trim($_POST['Email']));
        $password = $_POST['Password'];

        $query = "SELECT UserID AS user_id, FullName, Email, Password FROM users 
                  WHERE Email = '$email'";

        $result = $conn->query($query);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['Password']) {
                unset($user['Password']);
                $response = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data' => $user
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Invalid password'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Email not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Email and Password are required'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}



if (isset($_POST['users'])) {
    // Sanitize and validate input data
    $userId = intval($_POST['users']);
    $fullName = $conn->real_escape_string(trim($_POST['FullName']));
    $email = $conn->real_escape_string(trim($_POST['Email']));
    $password = $conn->real_escape_string($_POST['Password']);
    $phoneNumber = $conn->real_escape_string(trim($_POST['PhoneNumber']));
    $address = $conn->real_escape_string(trim($_POST['Address']));
    
    // For creation time, use current timestamp for new records
    $createdAt = ($userId === 0) ? date('Y-m-d H:i:s') : null;
    
    if ($userId === 0) {
        // INSERT operation
        // Check if email already exists
        $checkEmail = $conn->query("SELECT UserID FROM users WHERE Email = '$email'");
        
        if ($checkEmail->num_rows > 0) {
            $response = [
                'status' => 'error',
                'message' => 'Email already exists in the system'
            ];
        } else {
            // Insert new user
            $insertQuery = "INSERT INTO users (
                FullName, 
                Email, 
                Password, 
                PhoneNumber, 
                Address, 
                CreatedAt
            ) VALUES (
                '$fullName', 
                '$email', 
                '$password', 
                '$phoneNumber', 
                '$address', 
                '$createdAt'
            )";
            
            if ($conn->query($insertQuery)) {
                $response = [
                    'status' => 'success',
                    'message' => 'User  created successfully',
                    'user_id' => $conn->insert_id
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Error creating user: ' . $conn->error
                ];
            }
        }
    } else {
        // UPDATE operation
        // Check if email is being changed to one that already exists
        $checkEmail = $conn->query("SELECT UserID FROM users WHERE Email = '$email' AND UserID != $userId");
        
        if ($checkEmail->num_rows > 0) {
            $response = [
                'status' => 'error',
                'message' => 'Email already exists for another user'
            ];
        } else {
            $updateQuery = "UPDATE users SET
                FullName = '$fullName',
                Email = '$email',
                Password = '$password',
                PhoneNumber = '$phoneNumber',
                Address = '$address'
                WHERE UserID = $userId";
            
            if ($conn->query($updateQuery)) {
                $response = [
                    'status' => 'success',
                    'message' => 'User  updated successfully'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Error updating user: ' . $conn->error
                ];
            }
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Optional: Add GET endpoint for retrieving user data
if (isset($_GET['users'])) {
    $userId = intval($_GET['users']);
    
    if ($userId > 0) {
        $query = "SELECT 
            UserID as user_id,
            FullName,
            Email,
            PhoneNumber,
            Address,
            CreatedAt
            FROM users 
            WHERE UserID = $userId";
        
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            $response = [
                'status' => 'success',
                'data' => $userData
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'User  not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid user ID'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['carbrand'])) {
    // Sanitize and validate input data
    $brandId = intval($_POST['carbrand']);
    $brandName = $conn->real_escape_string(trim($_POST['BrandName']));
    $logoImage = $conn->real_escape_string(trim($_POST['LogoImage']));
    $foundedYear = $conn->real_escape_string(trim($_POST['FoundedYear']));
    $country = $conn->real_escape_string(trim($_POST['Country']));
    $ceo = $conn->real_escape_string(trim($_POST['CEO']));
    $affiliations = $conn->real_escape_string(trim($_POST['Affiliations']));
    
    if ($brandId === 0) {
        // INSERT operation
        $insertQuery = "INSERT INTO carbrand (
            BrandName, 
            LogoImage, 
            FoundedYear, 
            Country, 
            CEO, 
            Affiliations
        ) VALUES (
            '$brandName', 
            '$logoImage', 
            '$foundedYear', 
            '$country', 
            '$ceo', 
            '$affiliations'
        )";
        
        if ($conn->query($insertQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Car brand created successfully',
                'brand_id' => $conn->insert_id
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error creating car brand: ' . $conn->error
            ];
        }
    } else {
        // UPDATE operation
        $updateQuery = "UPDATE carbrand SET
            BrandName = '$brandName',
            LogoImage = '$logoImage',
            FoundedYear = '$foundedYear',
            Country = '$country',
            CEO = '$ceo',
            Affiliations = '$affiliations'
            WHERE BrandID = $brandId";
        
        if ($conn->query($updateQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Car brand updated successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error updating car brand: ' . $conn->error
            ];
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Optional: Add GET endpoint for retrieving car brand data
if (isset($_GET['carbrand'])) {
    $brandId = intval($_GET['carbrand']);
    
    if ($brandId > 0) {
        $query = "SELECT 
            BrandID as brand_id,
            BrandName,
            LogoImage,
            FoundedYear,
            Country,
            CEO,
            Affiliations
            FROM carbrand 
            WHERE BrandID = $brandId";
        
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $brandData = $result->fetch_assoc();
            $response = [
                'status' => 'success',
                'data' => $brandData
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Car brand not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid brand ID'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Car specifications management
if (isset($_POST['carspecifications'])) {
    // Sanitize and validate input data
    $specId = intval($_POST['carspecifications']);
    $modelId = intval($_POST['ModelID']);
    $engineType = $conn->real_escape_string(trim($_POST['EngineType']));
    $fuelType = $conn->real_escape_string(trim($_POST['FuelType']));
    $transmission = $conn->real_escape_string(trim($_POST['Transmission']));
    $driveType = $conn->real_escape_string(trim($_POST['DriveType']));
    $bodyType = $conn->real_escape_string(trim($_POST['BodyType']));
    $topSpeed = $conn->real_escape_string(trim($_POST['TopSpeed']));
    $fuelCapacity = $conn->real_escape_string(trim($_POST['FuelCapacity']));
    $batteryCapacity = $conn->real_escape_string(trim($_POST['BatteryCapacity']));
    $warranty = $conn->real_escape_string(trim($_POST['Warranty']));
    $designedBy = $conn->real_escape_string(trim($_POST['DesignedBy']));
    $manufacturedIn = $conn->real_escape_string(trim($_POST['ManufacturedIn']));
    $launchDate = $conn->real_escape_string(trim($_POST['LaunchDate']));

    if ($specId === 0) {
        // INSERT operation
        $insertQuery = "INSERT INTO carspecifications (
            ModelID, 
            EngineType, 
            FuelType, 
            Transmission, 
            DriveType, 
            BodyType, 
            TopSpeed, 
            FuelCapacity, 
            BatteryCapacity, 
            Warranty, 
            DesignedBy, 
            ManufacturedIn, 
            LaunchDate
        ) VALUES (
            $modelId, 
            '$engineType', 
            '$fuelType', 
            '$transmission', 
            '$driveType', 
            '$bodyType', 
            '$topSpeed', 
            '$fuelCapacity', 
            '$batteryCapacity', 
            '$warranty', 
            '$designedBy', 
            '$manufacturedIn', 
            '$launchDate'
        )";
        
        if ($conn->query($insertQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Car specifications created successfully',
                'spec_id' => $conn->insert_id
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error creating car specifications: ' . $conn->error
            ];
        }
    } else {
        // UPDATE operation
        $updateQuery = "UPDATE carspecifications SET
            ModelID = $modelId,
            EngineType = '$engineType',
            FuelType = '$fuelType',
            Transmission = '$transmission',
            DriveType = '$driveType',
            BodyType = '$bodyType',
            TopSpeed = '$topSpeed',
            FuelCapacity = '$fuelCapacity',
            BatteryCapacity = '$batteryCapacity',
            Warranty = '$warranty',
            DesignedBy = '$designedBy',
            ManufacturedIn = '$manufacturedIn',
            LaunchDate = '$launchDate'
            WHERE SpecID = $specId";
        
        if ($conn->query($updateQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Car specifications updated successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error updating car specifications: ' . $conn->error
            ];
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Optional: Add GET endpoint for retrieving car specifications data
if (isset($_GET['carspecifications'])) {
    $specId = intval($_GET['carspecifications']);
    
    if ($specId > 0) {
        $query = "SELECT 
            SpecID as spec_id,
            ModelID,
            EngineType,
            FuelType,
            Transmission,
            DriveType,
            BodyType,
            TopSpeed,
            FuelCapacity,
            BatteryCapacity,
            Warranty,
            DesignedBy,
            ManufacturedIn,
            LaunchDate
            FROM carspecifications 
            WHERE SpecID = $specId";
        
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $specData = $result->fetch_assoc();
            $response = [
                'status' => 'success',
                'data' => $specData
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Car specifications not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid specifications ID'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['orderhistory'])) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Sanitize and validate input data
        $orderId = intval($_POST['orderhistory']);
        $userId = intval($_POST['UserID']);
        $modelId = intval($_POST['ModelID']);
        $quantity = intval($_POST['Quantity']);
        $status = $conn->real_escape_string(trim($_POST['Status']));
        
        // Get car price and check availability
        $carQuery = "SELECT PriceRange, AvailableQty FROM carmodel WHERE ModelID = $modelId FOR UPDATE";
        $carResult = $conn->query($carQuery);
        
        if ($carResult->num_rows === 0) {
            throw new Exception("Car model not found");
        }
        
        $carData = $carResult->fetch_assoc();
        $price = $carData['PriceRange'];
        $availableQty = $carData['AvailableQty'];
        
        // Check if enough stock is available
        if ($availableQty < $quantity) {
            throw new Exception("Insufficient stock. Only $availableQty available.");
        }
        
        $totalPrice = $price * $quantity;
        $orderDate = date('Y-m-d H:i:s');
        
        if ($orderId === 0) {
            // INSERT operation
            $insertQuery = "INSERT INTO orderhistory (UserID, ModelID, OrderDate, Quantity, TotalPrice, Status) VALUES ($userId, $modelId, '$orderDate', $quantity, $totalPrice, '$status')";
            
            if (!$conn->query($insertQuery)) {
                throw new Exception("Error creating order: " . $conn->error);
            }
            
            $orderId = $conn->insert_id;
            
            // Update car quantity
            $updateQtyQuery = "UPDATE carmodel SET AvailableQty = AvailableQty - $quantity WHERE ModelID = $modelId";
            if (!$conn->query($updateQtyQuery)) {
                throw new Exception("Error updating car quantity: " . $conn->error);
            }
            
        } else {
            // UPDATE operation - handle quantity changes
            $oldOrderQuery = "SELECT Quantity, ModelID FROM orderhistory WHERE OrderID = $orderId";
            $oldOrderResult = $conn->query($oldOrderQuery);
            
            if ($oldOrderResult->num_rows > 0) {
                $oldOrder = $oldOrderResult->fetch_assoc();
                $oldQuantity = $oldOrder['Quantity'];
                $quantityDiff = $quantity - $oldQuantity;
                
                // Update order
                $updateQuery = "UPDATE orderhistory SET UserID = $userId, ModelID = $modelId, OrderDate = '$orderDate', Quantity = $quantity, TotalPrice = $totalPrice, Status = '$status' WHERE OrderID = $orderId";
                
                if (!$conn->query($updateQuery)) {
                    throw new Exception("Error updating order: " . $conn->error);
                }
                
                // Update car quantity based on the difference
                if ($quantityDiff != 0) {
                    $updateQtyQuery = "UPDATE carmodel SET AvailableQty = AvailableQty - $quantityDiff WHERE ModelID = $modelId";
                    if (!$conn->query($updateQtyQuery)) {
                        throw new Exception("Error updating car quantity: " . $conn->error);
                    }
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        $response = [
            'status' => 'success',
            'message' => 'Order processed successfully',
            'order_id' => $orderId
        ];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_GET['orderhistory'])) {
    $orderId = intval($_GET['orderhistory']);

    if ($orderId > 0) {
        $query = "SELECT 
            o.OrderID as order_id,
            o.UserID,
            u.FullName as UserName,
            o.ModelID,
            cm.ModelName,
            cm.PriceRange,
            cm.AvailableQty,
            o.OrderDate,
            o.Quantity,
            o.TotalPrice,
            o.Status
            FROM orderhistory o
            JOIN users u ON o.UserID = u.UserID
            JOIN carmodel cm ON o.ModelID = cm.ModelID
            WHERE o.OrderID = $orderId";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $orderData = $result->fetch_assoc();
            $response = [
                'status' => 'success',
                'data' => $orderData
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid order ID'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['reviews'])) {
    // Sanitize and validate input data
    $reviewId = intval($_POST['reviews']);
    $userId = intval($_POST['UserID']);
    $modelId = intval($_POST['ModelID']);
    $rating = intval($_POST['Rating']);
    $comment = $conn->real_escape_string(trim($_POST['Comment']));
    $reviewDate = date('Y-m-d H:i:s'); // Current timestamp

    if ($reviewId === 0) {
        // INSERT operation
        $insertQuery = "INSERT INTO reviews (
            UserID, 
            ModelID, 
            Rating, 
            Comment, 
            ReviewDate
        ) VALUES (
            $userId, 
            $modelId, 
            $rating, 
            '$comment', 
            '$reviewDate'
        )";

        if ($conn->query($insertQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Review created successfully',
                'review_id' => $conn->insert_id
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error creating review: ' . $conn->error
            ];
        }
    } else {
        // UPDATE operation
        $updateQuery = "UPDATE reviews SET
            UserID = $userId,
            ModelID = $modelId,
            Rating = $rating,
            Comment = '$comment',
            ReviewDate = '$reviewDate'
            WHERE ReviewID = $reviewId";

        if ($conn->query($updateQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Review updated successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error updating review: ' . $conn->error
            ];
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// GET endpoint for retrieving review data
if (isset($_GET['reviews'])) {
    $reviewId = intval($_GET['reviews']);

    if ($reviewId > 0) {
        $query = "SELECT 
            r.ReviewID as review_id,
            r.UserID,
            u.FullName as UserName,
            r.ModelID,
            cm.ModelName,
            r.Rating,
            r.Comment,
            r.ReviewDate
            FROM reviews r
            JOIN users u ON r.UserID = u.UserID
            JOIN carmodel cm ON r.ModelID = cm.ModelID
            WHERE r.ReviewID = $reviewId";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $reviewData = $result->fetch_assoc();
            $response = [
                'status' => 'success',
                'data' => $reviewData
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Review not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid review ID'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['testdrivebookings'])) {
    // Sanitize and validate input data
    $bookingId = intval($_POST['testdrivebookings']);
    $userId = intval($_POST['UserID']);
    $modelId = intval($_POST['ModelID']);
    $preferredDate = $conn->real_escape_string(trim($_POST['PreferredDate']));
    $status = $conn->real_escape_string(trim($_POST['Status']));
    $bookingDate = date('Y-m-d H:i:s'); // Current timestamp

    if ($bookingId === 0) {
        // INSERT operation
        $insertQuery = "INSERT INTO testdrivebookings (
            UserID, 
            ModelID, 
            PreferredDate, 
            Status, 
            BookingDate
        ) VALUES (
            $userId, 
            $modelId, 
            '$preferredDate', 
            '$status', 
            '$bookingDate'
        )";

        if ($conn->query($insertQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Test drive booking created successfully',
                'booking_id' => $conn->insert_id
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error creating booking: ' . $conn->error
            ];
        }
    } else {
        // UPDATE operation
        $updateQuery = "UPDATE testdrivebookings SET
            UserID = $userId,
            ModelID = $modelId,
            PreferredDate = '$preferredDate',
            Status = '$status'
            WHERE BookingID = $bookingId";

        if ($conn->query($updateQuery)) {
            $response = [
                'status' => 'success',
                'message' => 'Test drive booking updated successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error updating booking: ' . $conn->error
            ];
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// GET endpoint for retrieving booking data
if (isset($_GET['testdrivebookings'])) {
    $bookingId = intval($_GET['testdrivebookings']);

    if ($bookingId > 0) {
        $query = "SELECT 
            b.BookingID as booking_id,
            b.UserID,
            u.FullName as UserName,
            b.ModelID,
            cm.ModelName,
            b.PreferredDate,
            b.Status,
            b.BookingDate
            FROM testdrivebookings b
            JOIN users u ON b.UserID = u.UserID
            JOIN carmodel cm ON b.ModelID = cm.ModelID
            WHERE b.BookingID = $bookingId";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $bookingData = $result->fetch_assoc();
            $response = [
                'status' => 'success',
                'data' => $bookingData
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Booking not found'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid booking ID'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

?>