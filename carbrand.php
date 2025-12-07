<?php
include('connect.php');
include('header.php');

// Create extended manufacturer details table if it doesn't exist
$createManufacturerDetailsTable = "
CREATE TABLE IF NOT EXISTS manufacturer_details (
    DetailID INT(11) PRIMARY KEY AUTO_INCREMENT,
    BrandID INT(11) NOT NULL,
    HeadquartersAddress TEXT,
    ParentCompany VARCHAR(255),
    Subsidiaries TEXT,
    KeyExecutives TEXT,
    AnnualProduction INT(11),
    AnnualRevenue DECIMAL(15,2),
    ManufacturingPlants TEXT,
    ResearchCenters TEXT,
    MarketCap DECIMAL(15,2),
    EmployeeCount INT(11),
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (BrandID) REFERENCES carbrand(BrandID) ON DELETE CASCADE,
    UNIQUE KEY unique_brand_detail (BrandID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($createManufacturerDetailsTable);

// Create affiliations table if it doesn't exist
$createAffiliationTable = "
CREATE TABLE IF NOT EXISTS brand_affiliations (
    AffiliationID INT(11) PRIMARY KEY AUTO_INCREMENT,
    BrandID INT(11) NOT NULL,
    AffiliatedBrandID INT(11) NOT NULL,
    AffiliationType VARCHAR(50) DEFAULT 'partnership',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BrandID) REFERENCES carbrand(BrandID) ON DELETE CASCADE,
    FOREIGN KEY (AffiliatedBrandID) REFERENCES carbrand(BrandID) ON DELETE CASCADE,
    UNIQUE KEY unique_affiliation (BrandID, AffiliatedBrandID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($createAffiliationTable);

// Helper function to get manufacturer details
function getManufacturerDetails($conn, $brandId) {
    $sql = "SELECT * FROM manufacturer_details WHERE BrandID = $brandId";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Helper function to get all brands for dropdown (excluding current brand)
function getAllBrands($conn, $excludeId = null, $selectedIds = []) {
    $options = "";
    $sql = "SELECT BrandID, BrandName FROM carbrand";
    if ($excludeId) {
        $sql .= " WHERE BrandID != $excludeId";
    }
    $sql .= " ORDER BY BrandName ASC";
    
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $selected = in_array($row['BrandID'], $selectedIds) ? 'selected' : '';
        $options .= "<option value='{$row['BrandID']}' $selected>{$row['BrandName']}</option>";
    }
    return $options;
}

// Helper function to get brand's affiliations
function getBrandAffiliations($conn, $brandId) {
    $affiliations = [];
    $sql = "SELECT b.BrandID, b.BrandName, ba.AffiliationType
            FROM brand_affiliations ba
            JOIN carbrand b ON ba.AffiliatedBrandID = b.BrandID
            WHERE ba.BrandID = $brandId
            ORDER BY b.BrandName";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $affiliations[] = $row;
    }
    return $affiliations;
}

// Helper function to get brands affiliated with this brand (reverse lookup)
function getAffiliatedToBrand($conn, $brandId) {
    $affiliatedTo = [];
    $sql = "SELECT b.BrandID, b.BrandName, ba.AffiliationType
            FROM brand_affiliations ba
            JOIN carbrand b ON ba.BrandID = b.BrandID
            WHERE ba.AffiliatedBrandID = $brandId
            ORDER BY b.BrandName";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $affiliatedTo[] = $row;
    }
    return $affiliatedTo;
}

// Helper function to get brand's car models with specifications
function getBrandModelsWithDetails($conn, $brandId) {
    $models = [];
    $sql = "SELECT 
                cm.ModelID,
                cm.ModelName,
                cm.ModelYear,
                cm.PriceRange,
                cm.ManufacturePlace,
                vt.TypeName,
                cs.EngineType,
                cs.FuelType,
                cs.Transmission,
                cs.TopSpeed
            FROM carmodel cm
            LEFT JOIN vehicletype vt ON cm.TypeID = vt.TypeID
            LEFT JOIN carspecifications cs ON cm.ModelID = cs.ModelID
            WHERE cm.BrandID = $brandId
            ORDER BY cm.ModelName ASC";
    
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $models[] = $row;
        }
    }
    return $models;
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['BrandID'];
    // Get logo path to delete file
    $res = $conn->query("SELECT LogoImage FROM carbrand WHERE BrandID = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['LogoImage']) && file_exists($row['LogoImage'])) {
            unlink($row['LogoImage']);
        }
    }
    // Delete manufacturer details first
    $conn->query("DELETE FROM manufacturer_details WHERE BrandID = $id");
    // Delete affiliations
    $conn->query("DELETE FROM brand_affiliations WHERE BrandID = $id OR AffiliatedBrandID = $id");
    // Delete record
    $conn->query("DELETE FROM carbrand WHERE BrandID = $id");
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['BrandID'];
    $brandName = $conn->real_escape_string(trim($_POST['BrandName']));
    $foundedYear = $conn->real_escape_string(trim($_POST['FoundedYear']));
    $country = $conn->real_escape_string(trim($_POST['Country']));
    $ceo = $conn->real_escape_string(trim($_POST['CEO']));
    $affiliationText = $conn->real_escape_string(trim($_POST['Affiliations']));
    
    // Manufacturer details fields
    $headquarters = $conn->real_escape_string(trim($_POST['HeadquartersAddress'] ?? ''));
    $parentCompany = $conn->real_escape_string(trim($_POST['ParentCompany'] ?? ''));
    $subsidiaries = $conn->real_escape_string(trim($_POST['Subsidiaries'] ?? ''));
    $keyExecutives = $conn->real_escape_string(trim($_POST['KeyExecutives'] ?? ''));
    $annualProduction = (int)($_POST['AnnualProduction'] ?? 0);
    $annualRevenue = (float)($_POST['AnnualRevenue'] ?? 0);
    $manufacturingPlants = $conn->real_escape_string(trim($_POST['ManufacturingPlants'] ?? ''));
    $researchCenters = $conn->real_escape_string(trim($_POST['ResearchCenters'] ?? ''));
    $marketCap = (float)($_POST['MarketCap'] ?? 0);
    $employeeCount = (int)($_POST['EmployeeCount'] ?? 0);
    
    // Get selected affiliation brands
    $selectedAffiliations = isset($_POST['AffiliationBrands']) ? $_POST['AffiliationBrands'] : [];

    // Process new logo if uploaded
    $logoImage = '';
    if (isset($_FILES['LogoImage']) && $_FILES['LogoImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/carbrand_logos/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['LogoImage']['name']);
        $uploadPath = $uploadDir . $filename;
        $check = getimagesize($_FILES['LogoImage']['tmp_name']);
        if ($check !== false && move_uploaded_file($_FILES['LogoImage']['tmp_name'], $uploadPath)) {
            $logoImage = $uploadPath;
            // delete old logo
            $old = $conn->query("SELECT LogoImage FROM carbrand WHERE BrandID = $id");
            if ($old && $o = $old->fetch_assoc()) {
                if (!empty($o['LogoImage']) && file_exists($o['LogoImage'])) unlink($o['LogoImage']);
            }
            $logoSql = ", LogoImage = '$logoImage'";
        }
    }

    // Update carbrand record
    $updateSQL = "UPDATE carbrand SET
        BrandName = '$brandName',
        FoundedYear = '$foundedYear',
        Country = '$country',
        CEO = '$ceo',
        Affiliations = '$affiliationText'" .
        (isset($logoSql) ? $logoSql : '') .
        " WHERE BrandID = $id";

    $conn->query($updateSQL);
    
    // Update or insert manufacturer details
    $checkDetails = $conn->query("SELECT DetailID FROM manufacturer_details WHERE BrandID = $id");
    if ($checkDetails && $checkDetails->num_rows > 0) {
        // Update existing
        $updateDetails = "UPDATE manufacturer_details SET
            HeadquartersAddress = '$headquarters',
            ParentCompany = '$parentCompany',
            Subsidiaries = '$subsidiaries',
            KeyExecutives = '$keyExecutives',
            AnnualProduction = $annualProduction,
            AnnualRevenue = $annualRevenue,
            ManufacturingPlants = '$manufacturingPlants',
            ResearchCenters = '$researchCenters',
            MarketCap = $marketCap,
            EmployeeCount = $employeeCount
            WHERE BrandID = $id";
    } else {
        // Insert new
        $updateDetails = "INSERT INTO manufacturer_details (
            BrandID, HeadquartersAddress, ParentCompany, Subsidiaries, KeyExecutives,
            AnnualProduction, AnnualRevenue, ManufacturingPlants, ResearchCenters,
            MarketCap, EmployeeCount
        ) VALUES (
            $id, '$headquarters', '$parentCompany', '$subsidiaries', '$keyExecutives',
            $annualProduction, $annualRevenue, '$manufacturingPlants', '$researchCenters',
            $marketCap, $employeeCount
        )";
    }
    $conn->query($updateDetails);
    
    // Update affiliations in the affiliations table
    // First, delete existing affiliations
    $conn->query("DELETE FROM brand_affiliations WHERE BrandID = $id");
    
    // Then insert new affiliations
    foreach ($selectedAffiliations as $affiliatedBrandId) {
        $affiliatedBrandId = (int)$affiliatedBrandId;
        if ($affiliatedBrandId && $affiliatedBrandId != $id) {
            // Insert both directions to show mutual affiliation
            $conn->query("INSERT IGNORE INTO brand_affiliations (BrandID, AffiliatedBrandID) VALUES ($id, $affiliatedBrandId)");
            $conn->query("INSERT IGNORE INTO brand_affiliations (BrandID, AffiliatedBrandID) VALUES ($affiliatedBrandId, $id)");
        }
    }
}

// Handle add request
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $brandName = $conn->real_escape_string(trim($_POST['BrandName']));
    $foundedYear = $conn->real_escape_string(trim($_POST['FoundedYear']));
    $country = $conn->real_escape_string(trim($_POST['Country']));
    $ceo = $conn->real_escape_string(trim($_POST['CEO']));
    $affiliationText = $conn->real_escape_string(trim($_POST['Affiliations']));
    
    // Manufacturer details fields
    $headquarters = $conn->real_escape_string(trim($_POST['HeadquartersAddress'] ?? ''));
    $parentCompany = $conn->real_escape_string(trim($_POST['ParentCompany'] ?? ''));
    $subsidiaries = $conn->real_escape_string(trim($_POST['Subsidiaries'] ?? ''));
    $keyExecutives = $conn->real_escape_string(trim($_POST['KeyExecutives'] ?? ''));
    $annualProduction = (int)($_POST['AnnualProduction'] ?? 0);
    $annualRevenue = (float)($_POST['AnnualRevenue'] ?? 0);
    $manufacturingPlants = $conn->real_escape_string(trim($_POST['ManufacturingPlants'] ?? ''));
    $researchCenters = $conn->real_escape_string(trim($_POST['ResearchCenters'] ?? ''));
    $marketCap = (float)($_POST['MarketCap'] ?? 0);
    $employeeCount = (int)($_POST['EmployeeCount'] ?? 0);
    
    $logoImage = '';

    if (isset($_FILES['LogoImage']) && $_FILES['LogoImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/carbrand_logos/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['LogoImage']['name']);
        $uploadPath = $uploadDir . $filename;
        $check = getimagesize($_FILES['LogoImage']['tmp_name']);
        if ($check !== false) {
            move_uploaded_file($_FILES['LogoImage']['tmp_name'], $uploadPath);
            $logoImage = $uploadPath;
        }
    }

    $insert_q = "INSERT INTO carbrand (BrandName, LogoImage, FoundedYear, Country, CEO, Affiliations) VALUES (
        '$brandName', '$logoImage', '$foundedYear', '$country', '$ceo', '$affiliationText')";
    $conn->query($insert_q);
    
    $newBrandId = $conn->insert_id;
    
    // Insert manufacturer details
    if ($newBrandId) {
        $insertDetails = "INSERT INTO manufacturer_details (
            BrandID, HeadquartersAddress, ParentCompany, Subsidiaries, KeyExecutives,
            AnnualProduction, AnnualRevenue, ManufacturingPlants, ResearchCenters,
            MarketCap, EmployeeCount
        ) VALUES (
            $newBrandId, '$headquarters', '$parentCompany', '$subsidiaries', '$keyExecutives',
            $annualProduction, $annualRevenue, '$manufacturingPlants', '$researchCenters',
            $marketCap, $employeeCount
        )";
        $conn->query($insertDetails);
    }
    
    // Process affiliations if any selected
    if (isset($_POST['AffiliationBrands'])) {
        foreach ($_POST['AffiliationBrands'] as $affiliatedBrandId) {
            $affiliatedBrandId = (int)$affiliatedBrandId;
            if ($affiliatedBrandId) {
                // Insert both directions for mutual affiliation
                $conn->query("INSERT IGNORE INTO brand_affiliations (BrandID, AffiliatedBrandID) VALUES ($newBrandId, $affiliatedBrandId)");
                $conn->query("INSERT IGNORE INTO brand_affiliations (BrandID, AffiliatedBrandID) VALUES ($affiliatedBrandId, $newBrandId)");
            }
        }
    }
}
?>

<style>
.affiliation-badge {
    display: inline-block;
    background: #f0f0f0;
    color: #333;
    padding: 3px 8px;
    margin: 2px;
    border-radius: 3px;
    font-size: 12px;
    border: 1px solid #ddd;
}
.affiliation-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.affiliation-title {
    font-weight: bold;
    color: #666;
    margin-bottom: 5px;
    font-size: 14px;
}
.affiliation-list {
    min-height: 30px;
}
.multiselect-container {
    width: 100%;
    max-width: 400px;
    margin-bottom: 10px;
}
.multiselect-container select {
    width: 100%;
    height: 120px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: white;
}
.multiselect-container select option {
    padding: 5px;
}
.manufacturer-details-section {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
    margin-top: 15px;
    border: 1px solid #e0e0e0;
}
.manufacturer-detail-item {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px dashed #ddd;
}
.manufacturer-detail-label {
    font-weight: bold;
    color: #444;
    display: inline-block;
    width: 160px;
}
.manufacturer-detail-value {
    color: #666;
}
.models-section {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 2px solid #007bff;
}
.model-item {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 10px;
}
.model-name {
    font-weight: bold;
    color: #333;
    font-size: 16px;
}
.model-details {
    font-size: 13px;
    color: #666;
}
.model-detail-item {
    display: inline-block;
    margin-right: 15px;
}
.toggle-details-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    margin-top: 10px;
    font-size: 12px;
}
.toggle-details-btn:hover {
    background: #0056b3;
}
.hidden-details {
    display: none;
    margin-top: 15px;
}
</style>

<div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading">
                    <h1>Brand Affiliations & Manufacturer Details</h1>
                </div>
                <button id="addBrandBtn" class="impl_btn">Add Car Brand</button>
            </div>

            <div id="addBrandForm" style="display:none; width:100%;">
                <h2>Add New Car Brand</h2>
                <form method="POST" enctype="multipart/form-data">
                    <h3>Basic Information</h3>
                    <label for="BrandName">Brand Name:</label>
                    <input type="text" id="BrandName" name="BrandName" required>

                    <label for="LogoImage">Logo Image:</label>
                    <input type="file" id="LogoImage" name="LogoImage" accept="image/*" required>
                    <img id="imagePreview" class="preview-img" src="#" alt="Logo Preview" style="display:none; max-width:100px;">

                    <label for="FoundedYear">Founded Year:</label>
                    <input type="datetime-local" id="FoundedYear" name="FoundedYear" required>

                    <label for="Country">Country:</label>
                    <input type="text" id="Country" name="Country" required>

                    <label for="CEO">CEO:</label>
                    <input type="text" id="CEO" name="CEO" required>

                    <label for="Affiliations">Affiliations (Text - Legacy):</label>
                    <input type="text" id="Affiliations" name="Affiliations" placeholder="Text description (optional)">

                    <h3>Manufacturer Details</h3>
                    <label for="HeadquartersAddress">Headquarters Address:</label>
                    <textarea id="HeadquartersAddress" name="HeadquartersAddress" rows="2" placeholder="Full address of headquarters"></textarea>

                    <label for="ParentCompany">Parent Company:</label>
                    <input type="text" id="ParentCompany" name="ParentCompany" placeholder="If part of a larger group">

                    <label for="Subsidiaries">Subsidiaries:</label>
                    <textarea id="Subsidiaries" name="Subsidiaries" rows="2" placeholder="List of subsidiary brands/companies"></textarea>

                    <label for="KeyExecutives">Key Executives:</label>
                    <textarea id="KeyExecutives" name="KeyExecutives" rows="2" placeholder="Names of key executives (comma separated)"></textarea>

                    <label for="AnnualProduction">Annual Production (units):</label>
                    <input type="number" id="AnnualProduction" name="AnnualProduction" min="0" placeholder="Estimated annual vehicle production">

                    <label for="AnnualRevenue">Annual Revenue ($):</label>
                    <input type="number" id="AnnualRevenue" name="AnnualRevenue" min="0" step="0.01" placeholder="Annual revenue in USD">

                    <label for="ManufacturingPlants">Manufacturing Plants:</label>
                    <textarea id="ManufacturingPlants" name="ManufacturingPlants" rows="2" placeholder="Locations of manufacturing plants"></textarea>

                    <label for="ResearchCenters">Research & Development Centers:</label>
                    <textarea id="ResearchCenters" name="ResearchCenters" rows="2" placeholder="Locations of R&D centers"></textarea>

                    <label for="MarketCap">Market Capitalization ($):</label>
                    <input type="number" id="MarketCap" name="MarketCap" min="0" step="0.01" placeholder="Market cap in USD">

                    <label for="EmployeeCount">Employee Count:</label>
                    <input type="number" id="EmployeeCount" name="EmployeeCount" min="0" placeholder="Total number of employees">

                    <h3>Brand Affiliations</h3>
                    <label for="AffiliationBrands">Select Affiliated Brands:</label>
                    <div class="multiselect-container">
                        <select id="AffiliationBrands" name="AffiliationBrands[]" multiple>
                            <?php echo getAllBrands($conn); ?>
                        </select>
                        <small>Hold Ctrl (Cmd on Mac) to select multiple brands</small>
                    </div>

                    <input type="submit" value="Add Car Brand" class="impl_btn" style="margin-top: 20px;">
                </form>
            </div>

            <?php
            $query = "SELECT * FROM carbrand ORDER BY BrandName ASC";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['BrandID'];
                $brandName = $row['BrandName'];
                $logo = $row['LogoImage'];
                $year = $row['FoundedYear'];
                $country = $row['Country'];
                $ceo = $row['CEO'];
                $affiliationText = $row['Affiliations'];
                
                // Get manufacturer details
                $manufacturerDetails = getManufacturerDetails($conn, $id);
                
                // Get affiliations from new table
                $affiliations = getBrandAffiliations($conn, $id);
                $affiliatedTo = getAffiliatedToBrand($conn, $id);
                
                // Get brand models with details
                $brandModels = getBrandModelsWithDetails($conn, $id);
                
                // Get selected affiliation IDs for edit form
                $selectedAffiliationIds = [];
                foreach ($affiliations as $aff) {
                    $selectedAffiliationIds[] = $aff['BrandID'];
                }
            ?>
                <div class="col-lg-4 col-md-6" id="brandBox_<?php echo $id; ?>">
                    <!-- Display Box -->
                    <div class="impl_fea_car_box" id="viewBox_<?php echo $id; ?>">
                        <div class="impl_fea_car_img">
                            <img src="<?php echo $logo; ?>" alt="<?php echo $brandName; ?>" class="img-fluid brand-logo-img" />
                        </div>
                        <div class="impl_fea_car_data">
                            <h2><?php echo $brandName; ?></h2>
                            <ul>
                                <li><span class="impl_fea_title">Founded</span><span class="impl_fea_name"><?php echo date('Y', strtotime($year)); ?></span></li>
                                <li><span class="impl_fea_title">Country</span><span class="impl_fea_name"><?php echo $country; ?></span></li>
                                <li><span class="impl_fea_title">CEO</span><span class="impl_fea_name"><?php echo $ceo; ?></span></li>
                                <?php if ($affiliationText): ?>
                                <li><span class="impl_fea_title">Affiliations (Text)</span><span class="impl_fea_name"><?php echo htmlspecialchars($affiliationText); ?></span></li>
                                <?php endif; ?>
                            </ul>
                            
                            <!-- Display affiliations -->
                            <?php if (!empty($affiliations) || !empty($affiliatedTo)): ?>
                            <div class="affiliation-section">
                                <?php if (!empty($affiliations)): ?>
                                <div class="affiliation-list">
                                    <div class="affiliation-title">This brand is affiliated with:</div>
                                    <?php foreach ($affiliations as $aff): ?>
                                        <span class="affiliation-badge"><?php echo htmlspecialchars($aff['BrandName']); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($affiliatedTo)): ?>
                                <div class="affiliation-list">
                                    <div class="affiliation-title">Brands affiliated with this brand:</div>
                                    <?php foreach ($affiliatedTo as $aff): ?>
                                        <span class="affiliation-badge"><?php echo htmlspecialchars($aff['BrandName']); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Manufacturer Details (Hidden by default) -->
                            <button type="button" class="toggle-details-btn" onclick="toggleManufacturerDetails(<?php echo $id; ?>)">
                                Show Manufacturer Details
                            </button>
                            
                            <div id="manufacturerDetails_<?php echo $id; ?>" class="hidden-details">
                                <?php if ($manufacturerDetails): ?>
                                <div class="manufacturer-details-section">
                                    <h4>Manufacturer Details</h4>
                                    <?php if (!empty($manufacturerDetails['HeadquartersAddress'])): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Headquarters:</span>
                                        <span class="manufacturer-detail-value"><?php echo htmlspecialchars($manufacturerDetails['HeadquartersAddress']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($manufacturerDetails['ParentCompany'])): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Parent Company:</span>
                                        <span class="manufacturer-detail-value"><?php echo htmlspecialchars($manufacturerDetails['ParentCompany']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($manufacturerDetails['Subsidiaries'])): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Subsidiaries:</span>
                                        <span class="manufacturer-detail-value"><?php echo htmlspecialchars($manufacturerDetails['Subsidiaries']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($manufacturerDetails['KeyExecutives'])): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Key Executives:</span>
                                        <span class="manufacturer-detail-value"><?php echo htmlspecialchars($manufacturerDetails['KeyExecutives']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($manufacturerDetails['AnnualProduction'] > 0): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Annual Production:</span>
                                        <span class="manufacturer-detail-value"><?php echo number_format($manufacturerDetails['AnnualProduction']); ?> units</span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($manufacturerDetails['AnnualRevenue'] > 0): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Annual Revenue:</span>
                                        <span class="manufacturer-detail-value">$<?php echo number_format($manufacturerDetails['AnnualRevenue'], 2); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($manufacturerDetails['MarketCap'] > 0): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Market Cap:</span>
                                        <span class="manufacturer-detail-value">$<?php echo number_format($manufacturerDetails['MarketCap'], 2); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($manufacturerDetails['EmployeeCount'] > 0): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Employees:</span>
                                        <span class="manufacturer-detail-value"><?php echo number_format($manufacturerDetails['EmployeeCount']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($manufacturerDetails['ManufacturingPlants'])): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Manufacturing Plants:</span>
                                        <span class="manufacturer-detail-value"><?php echo htmlspecialchars($manufacturerDetails['ManufacturingPlants']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($manufacturerDetails['ResearchCenters'])): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">R&D Centers:</span>
                                        <span class="manufacturer-detail-value"><?php echo htmlspecialchars($manufacturerDetails['ResearchCenters']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($manufacturerDetails['UpdatedAt']): ?>
                                    <div class="manufacturer-detail-item">
                                        <span class="manufacturer-detail-label">Last Updated:</span>
                                        <span class="manufacturer-detail-value"><?php echo date('M d, Y', strtotime($manufacturerDetails['UpdatedAt'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <div class="manufacturer-details-section">
                                    <p>No manufacturer details available. Add details in edit mode.</p>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Brand Models Section -->
                                <?php if (!empty($brandModels)): ?>
                                <div class="models-section">
                                    <h4>Models by <?php echo $brandName; ?></h4>
                                    <?php foreach ($brandModels as $model): ?>
                                    <div class="model-item">
                                        <div class="model-name"><?php echo htmlspecialchars($model['ModelName']); ?></div>
                                        <div class="model-details">
                                            <span class="model-detail-item">Year: <?php echo date('Y', strtotime($model['ModelYear'])); ?></span>
                                            <span class="model-detail-item">Type: <?php echo htmlspecialchars($model['TypeName']); ?></span>
                                            <span class="model-detail-item">Price: $<?php echo number_format($model['PriceRange']); ?></span>
                                            <?php if (!empty($model['EngineType'])): ?>
                                            <span class="model-detail-item">Engine: <?php echo htmlspecialchars($model['EngineType']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($model['Transmission'])): ?>
                                            <span class="model-detail-item">Trans: <?php echo htmlspecialchars($model['Transmission']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($model['TopSpeed'])): ?>
                                            <span class="model-detail-item">Top Speed: <?php echo htmlspecialchars($model['TopSpeed']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="models-section">
                                    <p>No models found for this brand.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <button class="impl_btn editBtn" data-id="<?php echo $id; ?>">Edit</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="BrandID" value="<?php echo $id; ?>">
                                <button type="submit" class="impl_btn" onclick="return confirm('Delete this brand and all its affiliations?')">Delete</button>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div id="editForm_<?php echo $id; ?>" style="display:none; width:100%; padding: 20px; background: #f9f9f9; border-radius: 5px; margin-top: 10px;">
                        <h3>Edit <?php echo $brandName; ?></h3>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="BrandID" value="<?php echo $id; ?>">

                            <h4>Basic Information</h4>
                            <label>Brand Name:</label>
                            <input type="text" name="BrandName" value="<?php echo htmlspecialchars($brandName); ?>" required style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Logo Image:</label>
                            <input type="file" name="LogoImage" accept="image/*" style="margin-bottom:10px;">
                            <img src="<?php echo $logo; ?>" alt="Current Logo" style="max-width:100px; display:block; margin-bottom:10px;">

                            <label>Founded Year:</label>
                            <input type="datetime-local" name="FoundedYear" value="<?php echo date('Y-m-d\TH:i', strtotime($year)); ?>" required style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Country:</label>
                            <input type="text" name="Country" value="<?php echo htmlspecialchars($country); ?>" required style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>CEO:</label>
                            <input type="text" name="CEO" value="<?php echo htmlspecialchars($ceo); ?>" required style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Affiliations (Text - Legacy):</label>
                            <input type="text" name="Affiliations" value="<?php echo htmlspecialchars($affiliationText); ?>" placeholder="Text description (optional)" style="width:100%; max-width:400px; margin-bottom:10px;">

                            <h4>Manufacturer Details</h4>
                            <?php
                            // Pre-fill manufacturer details if they exist
                            $hq = $manufacturerDetails['HeadquartersAddress'] ?? '';
                            $parent = $manufacturerDetails['ParentCompany'] ?? '';
                            $subs = $manufacturerDetails['Subsidiaries'] ?? '';
                            $execs = $manufacturerDetails['KeyExecutives'] ?? '';
                            $prod = $manufacturerDetails['AnnualProduction'] ?? 0;
                            $rev = $manufacturerDetails['AnnualRevenue'] ?? 0;
                            $plants = $manufacturerDetails['ManufacturingPlants'] ?? '';
                            $research = $manufacturerDetails['ResearchCenters'] ?? '';
                            $cap = $manufacturerDetails['MarketCap'] ?? 0;
                            $emp = $manufacturerDetails['EmployeeCount'] ?? 0;
                            ?>
                            
                            <label>Headquarters Address:</label>
                            <textarea name="HeadquartersAddress" rows="2" style="width:100%; max-width:400px; margin-bottom:10px;"><?php echo htmlspecialchars($hq); ?></textarea>

                            <label>Parent Company:</label>
                            <input type="text" name="ParentCompany" value="<?php echo htmlspecialchars($parent); ?>" style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Subsidiaries:</label>
                            <textarea name="Subsidiaries" rows="2" style="width:100%; max-width:400px; margin-bottom:10px;"><?php echo htmlspecialchars($subs); ?></textarea>

                            <label>Key Executives:</label>
                            <textarea name="KeyExecutives" rows="2" style="width:100%; max-width:400px; margin-bottom:10px;"><?php echo htmlspecialchars($execs); ?></textarea>

                            <label>Annual Production (units):</label>
                            <input type="number" name="AnnualProduction" value="<?php echo $prod; ?>" min="0" style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Annual Revenue ($):</label>
                            <input type="number" name="AnnualRevenue" value="<?php echo $rev; ?>" min="0" step="0.01" style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Manufacturing Plants:</label>
                            <textarea name="ManufacturingPlants" rows="2" style="width:100%; max-width:400px; margin-bottom:10px;"><?php echo htmlspecialchars($plants); ?></textarea>

                            <label>Research & Development Centers:</label>
                            <textarea name="ResearchCenters" rows="2" style="width:100%; max-width:400px; margin-bottom:10px;"><?php echo htmlspecialchars($research); ?></textarea>

                            <label>Market Capitalization ($):</label>
                            <input type="number" name="MarketCap" value="<?php echo $cap; ?>" min="0" step="0.01" style="width:100%; max-width:400px; margin-bottom:10px;">

                            <label>Employee Count:</label>
                            <input type="number" name="EmployeeCount" value="<?php echo $emp; ?>" min="0" style="width:100%; max-width:400px; margin-bottom:10px;">

                            <h4>Brand Affiliations</h4>
                            <label>Select Affiliated Brands:</label>
                            <div class="multiselect-container">
                                <select name="AffiliationBrands[]" multiple style="width:100%; height:120px;">
                                    <?php echo getAllBrands($conn, $id, $selectedAffiliationIds); ?>
                                </select>
                                <small>Hold Ctrl (Cmd on Mac) to select multiple brands</small>
                            </div>

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
    // Toggle Add Form
    document.getElementById('addBrandBtn').addEventListener('click', function() {
        const f = document.getElementById('addBrandForm');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    });

    // Image preview for Add Form
    document.getElementById('LogoImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else preview.style.display = 'none';
    });

    // Edit and Cancel buttons
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('viewBox_' + id).style.display = 'none';
            document.getElementById('editForm_' + id).style.display = 'block';
        });
    });
    
    document.querySelectorAll('.cancelEdit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('editForm_' + id).style.display = 'none';
            document.getElementById('viewBox_' + id).style.display = 'block';
        });
    });

    // Toggle manufacturer details visibility
    function toggleManufacturerDetails(brandId) {
        const detailsDiv = document.getElementById('manufacturerDetails_' + brandId);
        const button = document.querySelector(`[onclick="toggleManufacturerDetails(${brandId})"]`);
        
        if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
            detailsDiv.style.display = 'block';
            button.textContent = 'Hide Manufacturer Details';
        } else {
            detailsDiv.style.display = 'none';
            button.textContent = 'Show Manufacturer Details';
        }
    }
</script>