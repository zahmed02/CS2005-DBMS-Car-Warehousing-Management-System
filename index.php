<?php
include('connect.php');
include('header.php');
include('slider.php');
include('search.php');
?>
<div class="impl_welcome_wrapper impl_bottompadder80">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                    <div class="impl_welcome_img">
    <img src="assets/images/bugatti.jpg" alt="Bugatti Car" class="img-responsive">
</div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
<div class="impl_welcome_text">
    <h1>Welcome to impel cars</h1>
    <div class="panel-group" id="accordion">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        Experience the perfect blend of style, comfort, and innovation.
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse show">
                <div class="panel-body">
                    With a dynamic suspension system and intuitive handling, this car offers a smooth ride in all conditions.
                    Premium materials and smart tech elevate your driving experience.
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                        Designed for performance, built for everyday luxury.
                    </a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse show">
                <div class="panel-body">
                    Built to handle sharp turns and long drives with equal grace, its precision-tuned mechanics and aerodynamic design redefine comfort on the road.
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                        Sleek design meets advanced engineering in every drive.
                    </a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse show">
                <div class="panel-body">
                    From its responsive engine to its seamless infotainment system, every detail is crafted to give you control, confidence, and style behind the wheel.
                </div>
            </div>
        </div>

    </div>
</div>

            </div>
        </div>
    </div>

<?php
include('featured_cars.php');
include('footer.php');
?>