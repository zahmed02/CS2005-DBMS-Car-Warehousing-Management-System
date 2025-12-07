<?php
session_start(); // Start the session at the beginning of your PHP file

// Check if there is a logout message
if (isset($_SESSION['logout_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['logout_message'] . "</div>";
    unset($_SESSION['logout_message']); // Unset the message after displaying it
}

// Debug mode - comment out in production
// print_r($_SESSION); // This will show the session data for debugging
?>

<?php
    include('header.php');
    include('connect.php');
?>

    <!------ Breadcrumbs Start ------>
    <div class="impl_bread_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <h1>contact</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">contact</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!------ Contact Wrapper Start ------>
    <div class="impl_contact_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-md-12 offset-lg-1">
                    <div class="impl_con_form">
                        <div class="contact_map">
                            <!-- Map container - will be populated by JavaScript -->
                            <div id="contact_map"></div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <h1>get in touch</h1>
                        </div>
                        <form id="contactForm" method="POST">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <input type="text" name="email" class="form-control require" placeholder="YOUR EMAIL" data-valid="email" data-error="Email should be valid." required>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="PASSWORD" required>
                                </div>
                            </div>
                            <div class="response"></div>
                            <div class="col-lg-12 col-md-12">
                                <input type="hidden" name="login" value="1">
                                <button type="submit" class="impl_btn">post comment</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="impl_contact_info">
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <div class="impl_contact_box">
                                    <div class="impl_con_data">
                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                        <h2>phone</h2>
                                        <p>+1-202-555-0137</p>
                                        <p>+1-202-555-0189</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="impl_contact_box">
                                    <div class="impl_con_data">
                                        <i class="fa fa-home" aria-hidden="true"></i>
                                        <h2>address</h2>
                                        <p>514 S. Magnolia St.<br>Orlando , US</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="impl_contact_box">
                                    <div class="impl_con_data">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <h2>E - mail</h2>
                                        <p><a href="#">dummymail@mail.com</a></p>
                                        <p><a href="#">yourmail@mail.com</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include('footer.php');
?>
    
<!-- Map Configuration -->
<script>
    // Get API key from config (replace with your method)
    // Recommended: Load from a separate config file or environment variable
    var GOOGLE_MAPS_API_KEY = ''; // Add your key here or load from config
    
    // Alternative: Use Leaflet.js (OpenStreetMap) - free and no API key needed
    function initOpenStreetMap() {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            // Load Leaflet CSS and JS dynamically
            var leafletCSS = document.createElement('link');
            leafletCSS.rel = 'stylesheet';
            leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(leafletCSS);
            
            var leafletJS = document.createElement('script');
            leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            leafletJS.onload = function() {
                createOSMMap();
            };
            document.head.appendChild(leafletJS);
        } else {
            createOSMMap();
        }
    }
    
    function createOSMMap() {
        // Coordinates for Orlando, Florida
        var map = L.map('contact_map').setView([28.5383, -81.3792], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add a marker
        L.marker([28.5383, -81.3792])
            .addTo(map)
            .bindPopup('514 S. Magnolia St.<br>Orlando, FL')
            .openPopup();
    }
    
    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Option 1: Use OpenStreetMap (recommended - free, no API key)
        initOpenStreetMap();
        
        // Option 2: Use Google Maps if you have a valid API key
        // if (GOOGLE_MAPS_API_KEY) {
        //     initGoogleMap();
        // } else {
        //     initOpenStreetMap();
        // }
    });
</script>

<!-- Form Submission Script -->
<script>
    $(document).ready(function () {
        $('#contactForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            
            // Basic form validation
            var email = $('input[name="email"]').val();
            var password = $('input[name="password"]').val();
            
            if (!email || !password) {
                alert("Please fill in all required fields.");
                return false;
            }
            
            // Email validation
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }
            
            // Show loading state
            var submitBtn = $(this).find('.impl_btn');
            var originalText = submitBtn.text();
            submitBtn.text('Processing...').prop('disabled', true);
            
            $.ajax({
                url: "inc/process.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json"
            }).done(function (data) {
                if (data.msg_code == 0) {
                    alert(data.msg);
                    // Redirect on successful login
                    window.location.replace("http://localhost/hr/dashboard.php");
                } else {
                    alert(data.msg || "An error occurred. Please try again.");
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                alert("Request failed: " + textStatus + "\nError: " + errorThrown);
                console.error("AJAX Error:", jqXHR.responseText);
            }).always(function () {
                // Restore button state
                submitBtn.text(originalText).prop('disabled', false);
            });
        });
    });
</script>

<!-- Alternative: Add this to your config.php file -->
<!-- 
<?php
// config.php - ADD THIS FILE TO .gitignore!
// define('GOOGLE_MAPS_API_KEY', 'YOUR_ACTUAL_KEY_HERE');
?>
-->

</body>
</html>