<?php
// Function to get price range label (keep as is)
function getPriceRangeLabel($price) {
    if ($price < 10000) return "Under $10,000";
    $rangeStart = floor($price / 10000) * 10000;
    $rangeEnd = $rangeStart + 10000;
    return "$".number_format($rangeStart)." - $".number_format($rangeEnd);
}

// Initialize arrays for filtering
$selectedBrand = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$selectedType = isset($_GET['type']) ? (int)$_GET['type'] : 0;
$selectedModel = isset($_GET['model']) ? (int)$_GET['model'] : 0;
$selectedPriceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : '';
$selectedYearFrom = isset($_GET['yearFrom']) ? (int)$_GET['yearFrom'] : 0;
$selectedYearTo = isset($_GET['yearTo']) ? (int)$_GET['yearTo'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #1a1a1a;
            color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #2d2d2d;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        th {
            background-color: #1a1a1a;
            color: #ffffff;
            font-weight: 600;
        }
        select, input {
            padding: 8px 12px;
            width: 100%;
            box-sizing: border-box;
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
            border-radius: 4px;
        }
        select:focus, input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        .small-img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .clear-btn {
            background-color: #6c757d;
            margin-left: 10px;
        }
        .clear-btn:hover {
            background-color: #5a6268;
        }
        .results-container {
            margin-top: 30px;
            background-color: #2d2d2d;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .results-title {
            color: #ffffff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        .filter-row {
            background-color: #333;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .filter-row td {
            border: none;
        }
        .results-table {
            background-color: #333;
        }
        .results-table th {
            background-color: #444;
        }
        .results-table tr:hover {
            background-color: #3a3a3a;
        }
        .year-inputs {
            display: flex;
            gap: 10px;
        }
        .year-inputs input {
            flex: 1;
        }
    </style>
</head>
<body>
    <h1 style="color: #ffffff;">Vehicle Search</h1>
    
    <form method="get" action="">
        <table class="filter-row">
            <tr>
                <th style="width: 20%;">Field</th>
                <th>Selection</th>
            </tr>
            
            <!-- Brand Filter -->
            <tr>
                <td><label for="brandSelect">Brand:</label></td>
                <td>
                    <select id="brandSelect" name="brand" onchange="updateTypes()">
                        <option value="">-- Any Brand --</option>
                        <?php
                        $brands = $conn->query("SELECT BrandID, BrandName FROM carbrand ORDER BY BrandName");
                        while ($brand = $brands->fetch_assoc()) {
                            $selected = ($selectedBrand == $brand['BrandID']) ? 'selected' : '';
                            echo "<option value='{$brand['BrandID']}' $selected>{$brand['BrandName']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <!-- Vehicle Type Filter (dynamically populated) -->
            <tr>
                <td><label for="typeSelect">Vehicle Type:</label></td>
                <td>
                    <select id="typeSelect" name="type" onchange="updateModels()" <?php echo ($selectedBrand == 0) ? 'disabled' : ''; ?>>
                        <option value="">-- Any Type --</option>
                        <?php
                        if ($selectedBrand > 0) {
                            $types = $conn->query("SELECT DISTINCT vt.TypeID, vt.TypeName 
                                                 FROM vehicletype vt 
                                                 JOIN carmodel cm ON vt.TypeID = cm.TypeID 
                                                 WHERE cm.BrandID = $selectedBrand 
                                                 ORDER BY vt.TypeName");
                            while ($type = $types->fetch_assoc()) {
                                $selected = ($selectedType == $type['TypeID']) ? 'selected' : '';
                                echo "<option value='{$type['TypeID']}' $selected>{$type['TypeName']}</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <!-- Model Filter (dynamically populated) -->
            <tr>
                <td><label for="modelSelect">Model:</label></td>
                <td>
                    <select id="modelSelect" name="model" <?php echo ($selectedBrand == 0 || $selectedType == 0) ? 'disabled' : ''; ?>>
                        <option value="">-- Any Model --</option>
                        <?php
                        if ($selectedBrand > 0 && $selectedType > 0) {
                            $models = $conn->query("SELECT ModelID, ModelName 
                                                  FROM carmodel 
                                                  WHERE BrandID = $selectedBrand 
                                                  AND TypeID = $selectedType 
                                                  ORDER BY ModelName");
                            while ($model = $models->fetch_assoc()) {
                                $selected = ($selectedModel == $model['ModelID']) ? 'selected' : '';
                                echo "<option value='{$model['ModelID']}' $selected>{$model['ModelName']}</option>";
                            }
                        } elseif ($selectedBrand > 0) {
                            $models = $conn->query("SELECT ModelID, ModelName 
                                                  FROM carmodel 
                                                  WHERE BrandID = $selectedBrand 
                                                  ORDER BY ModelName");
                            while ($model = $models->fetch_assoc()) {
                                $selected = ($selectedModel == $model['ModelID']) ? 'selected' : '';
                                echo "<option value='{$model['ModelID']}' $selected>{$model['ModelName']}</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <!-- Price Range Filter -->
            <tr>
                <td><label for="priceRange">Price Range:</label></td>
                <td>
                    <select id="priceRange" name="priceRange">
                        <option value="">-- Any Price --</option>
                        <?php
                        $prices = $conn->query("SELECT MIN(PriceRange) as minPrice, MAX(PriceRange) as maxPrice FROM carmodel");
                        $priceRange = $prices->fetch_assoc();
                        $minPrice = floor($priceRange['minPrice'] / 10000) * 10000;
                        $maxPrice = ceil($priceRange['maxPrice'] / 10000) * 10000;
                        
                        echo "<option value='0-10000'" . ($selectedPriceRange == '0-10000' ? ' selected' : '') . ">Under $10,000</option>";
                        
                        for ($i = $minPrice; $i <= $maxPrice; $i += 10000) {
                            $next = $i + 10000;
                            $current = number_format($i);
                            $nextFormatted = number_format($next);
                            $value = "$i-$next";
                            $selected = ($selectedPriceRange == $value) ? 'selected' : '';
                            echo "<option value='$value' $selected>$current - $nextFormatted</option>";
                        }
                        
                        echo "<option value='$maxPrice-'" . ($selectedPriceRange == "$maxPrice-" ? ' selected' : '') . ">Over $" . number_format($maxPrice) . "</option>";
                        ?>
                    </select>
                </td>
            </tr>
            
            <!-- Year Filter (NEW FEATURE) -->
            <tr>
                <td><label>Model Year:</label></td>
                <td>
                    <div class="year-inputs">
                        <input type="number" name="yearFrom" placeholder="From Year" 
                               min="1900" max="<?php echo date('Y'); ?>" 
                               value="<?php echo $selectedYearFrom > 0 ? $selectedYearFrom : ''; ?>">
                        <input type="number" name="yearTo" placeholder="To Year" 
                               min="1900" max="<?php echo date('Y'); ?>" 
                               value="<?php echo $selectedYearTo > 0 ? $selectedYearTo : ''; ?>">
                    </div>
                </td>
            </tr>
            
            <!-- Action Buttons -->
            <tr>
                <td colspan="2" style="text-align:center; padding-top: 20px;">
                    <button type="submit">Search Vehicles</button>
                    <button type="button" class="clear-btn" onclick="clearFilters()">Clear Filters</button>
                </td>
            </tr>
        </table>
    </form>

    <?php
    // Build the search query based on selected filters
    $query = "SELECT cm.*, cb.BrandName, vt.TypeName 
              FROM carmodel cm
              JOIN carbrand cb ON cm.BrandID = cb.BrandID
              JOIN vehicletype vt ON cm.TypeID = vt.TypeID
              WHERE 1=1";
    
    $conditions = array();
    
    // Add brand filter
    if ($selectedBrand > 0) {
        $conditions[] = "cm.BrandID = $selectedBrand";
    }
    
    // Add type filter
    if ($selectedType > 0) {
        $conditions[] = "cm.TypeID = $selectedType";
    }
    
    // Add model filter
    if ($selectedModel > 0) {
        $conditions[] = "cm.ModelID = $selectedModel";
    }
    
    // Add price range filter
    if (!empty($selectedPriceRange)) {
        $priceRange = explode('-', $selectedPriceRange);
        $min = (int)$priceRange[0];
        $max = isset($priceRange[1]) ? (int)$priceRange[1] : PHP_INT_MAX;
        
        $conditions[] = "cm.PriceRange >= $min";
        if ($max != PHP_INT_MAX) {
            $conditions[] = "cm.PriceRange <= $max";
        }
    }
    
    // Add year filter (NEW FEATURE)
    if ($selectedYearFrom > 0) {
        $conditions[] = "YEAR(cm.ModelYear) >= $selectedYearFrom";
    }
    if ($selectedYearTo > 0) {
        $conditions[] = "YEAR(cm.ModelYear) <= $selectedYearTo";
    }
    
    // Combine all conditions
    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }
    
    $query .= " ORDER BY cm.ModelName ASC";
    
    // Execute query
    $result = $conn->query($query);
    ?>
    
    <!-- Results Section -->
    <div class="results-container">
        <h2 class="results-title">
            Search Results 
            <?php if ($result->num_rows > 0): ?>
                <span style="font-size: 14px; color: #aaa;">(<?php echo $result->num_rows; ?> vehicles found)</span>
            <?php endif; ?>
        </h2>
        
        <?php if ($result->num_rows > 0): ?>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Model</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($vehicle = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($vehicle['MainImage'])): ?>
                                    <img src="<?php echo htmlspecialchars($vehicle['MainImage']); ?>" 
                                         class="small-img" 
                                         alt="<?php echo htmlspecialchars($vehicle['ModelName']); ?>"
                                         title="Click to view larger"
                                         onclick="showImageModal('<?php echo htmlspecialchars($vehicle['MainImage']); ?>')">
                                <?php else: ?>
                                    <div style="width:100px; height:75px; background:#444; display:flex; align-items:center; justify-content:center; border-radius:4px;">
                                        <span style="color:#777; font-size:12px;">No Image</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($vehicle['ModelName']); ?></strong></td>
                            <td><?php echo htmlspecialchars($vehicle['BrandName']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['TypeName']); ?></td>
                            <td><?php echo date('Y', strtotime($vehicle['ModelYear'])); ?></td>
                            <td style="color: #4CAF50; font-weight: bold;">$<?php echo number_format($vehicle['PriceRange']); ?></td>
                            <td>
                                <?php if ($vehicle['InStock'] && $vehicle['AvailableQty'] > 0): ?>
                                    <span style="color: #4CAF50;">In Stock (<?php echo $vehicle['AvailableQty']; ?>)</span>
                                <?php else: ?>
                                    <span style="color: #f44336;">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif (isset($_GET['brand']) || isset($_GET['type']) || isset($_GET['model']) || isset($_GET['priceRange']) || isset($_GET['yearFrom']) || isset($_GET['yearTo'])): ?>
            <div class="no-results">
                <h3>No vehicles found matching your criteria</h3>
                <p>Try adjusting your filters or <a href="javascript:void(0)" onclick="clearFilters()" style="color: #007bff;">clear all filters</a></p>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>Select filters to search for vehicles</h3>
                <p>Choose a brand, type, model, price range, or year to see matching vehicles</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Image Modal (hidden by default) -->
    <div id="imageModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:1000; justify-content:center; align-items:center;">
        <img id="modalImage" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
        <button onclick="closeImageModal()" style="position:absolute; top:20px; right:20px; background:#f44336; color:white; border:none; padding:10px 15px; border-radius:4px; cursor:pointer;">Close</button>
    </div>

    <script>
    // Function to update vehicle types based on selected brand
    function updateTypes() {
        const brandSelect = document.getElementById('brandSelect');
        const typeSelect = document.getElementById('typeSelect');
        const modelSelect = document.getElementById('modelSelect');
        const brandId = brandSelect.value;
        
        // Reset type and model dropdowns
        typeSelect.innerHTML = '<option value="">-- Any Type --</option>';
        modelSelect.innerHTML = '<option value="">-- Any Model --</option>';
        
        if (brandId) {
            // Enable type dropdown
            typeSelect.disabled = false;
            
            // Get types for this brand from database (via PHP or AJAX)
            // We'll use a page reload approach for simplicity
            const url = new URL(window.location);
            url.searchParams.set('brand', brandId);
            url.searchParams.delete('type');
            url.searchParams.delete('model');
            window.location.href = url.toString();
        } else {
            // Disable type and model dropdowns
            typeSelect.disabled = true;
            modelSelect.disabled = true;
            
            // Clear type and model from URL
            const url = new URL(window.location);
            url.searchParams.delete('brand');
            url.searchParams.delete('type');
            url.searchParams.delete('model');
            window.location.href = url.toString();
        }
    }
    
    // Function to update models based on selected brand and type
    function updateModels() {
        const brandSelect = document.getElementById('brandSelect');
        const typeSelect = document.getElementById('typeSelect');
        const modelSelect = document.getElementById('modelSelect');
        const brandId = brandSelect.value;
        const typeId = typeSelect.value;
        
        // Reset model dropdown
        modelSelect.innerHTML = '<option value="">-- Any Model --</option>';
        
        if (brandId && typeId) {
            // Enable model dropdown
            modelSelect.disabled = false;
            
            // Get models for this brand and type
            const url = new URL(window.location);
            url.searchParams.set('brand', brandId);
            url.searchParams.set('type', typeId);
            url.searchParams.delete('model');
            window.location.href = url.toString();
        } else if (brandId) {
            // Only brand selected, disable model dropdown
            modelSelect.disabled = true;
            
            const url = new URL(window.location);
            url.searchParams.set('brand', brandId);
            url.searchParams.delete('type');
            url.searchParams.delete('model');
            window.location.href = url.toString();
        }
    }
    
    // Function to clear all filters
    function clearFilters() {
        const url = new URL(window.location);
        url.searchParams.delete('brand');
        url.searchParams.delete('type');
        url.searchParams.delete('model');
        url.searchParams.delete('priceRange');
        url.searchParams.delete('yearFrom');
        url.searchParams.delete('yearTo');
        window.location.href = url.toString();
    }
    
    // Function to show image in modal
    function showImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modal.style.display = 'flex';
    }
    
    // Function to close image modal
    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
    
    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
    
    // Initialize dropdown states on page load
    document.addEventListener('DOMContentLoaded', function() {
        const brandSelect = document.getElementById('brandSelect');
        const typeSelect = document.getElementById('typeSelect');
        const modelSelect = document.getElementById('modelSelect');
        
        // Enable type dropdown if brand is selected
        if (brandSelect.value) {
            typeSelect.disabled = false;
        }
        
        // Enable model dropdown if both brand and type are selected
        if (brandSelect.value && typeSelect.value) {
            modelSelect.disabled = false;
        }
    });
    </script>

    <!-- Store data for JavaScript (if needed for more advanced features) -->
    <script id="vehicleData" type="application/json">
    <?php
    // Fetch all vehicle data for potential client-side filtering
    $allVehicles = $conn->query("SELECT 
        cm.ModelID, cm.ModelName, cm.BrandID, cm.TypeID, 
        cm.PriceRange, cm.ModelYear, cm.AvailableQty, cm.InStock,
        cb.BrandName, vt.TypeName
        FROM carmodel cm
        JOIN carbrand cb ON cm.BrandID = cb.BrandID
        JOIN vehicletype vt ON cm.TypeID = vt.TypeID");
    
    $vehicles = [];
    while($row = $allVehicles->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode($vehicles);
    ?>
    </script>
</body>
</html>