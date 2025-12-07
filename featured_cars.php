<!------ Featured Cars Start ------>
    <div class="impl_featured_wrappar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="impl_heading">
                    <h1>Featured Cars</h1>
                </div>
            </div>

            <?php
            // Fetch featured cars from database
            $query = "SELECT * FROM carmodel WHERE Trending = 1 LIMIT 6";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $modelName = $row['ModelName'];
                $price = $row['PriceRange'];
                $year = date('Y', strtotime($row['ModelYear']));
                $place = $row['ManufacturePlace'];
                $status = $row['InStock'] ? 'new' : 'old';
                $qty = $row['AvailableQty'];
                $mainImage = $row['MainImage'];
                $hoverImage = $row['RearImage'];
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="impl_fea_car_box">
                        <div class="impl_fea_car_img">
                            <img src="<?php echo $mainImage; ?>" alt="" class="img-fluid impl_frst_car_img" />
                            <img src="<?php echo $hoverImage; ?>" alt="" class="img-fluid impl_hover_car_img" />
                            <span class="impl_img_tag" title="compare"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                        </div>
                        <div class="impl_fea_car_data">
                            <h2><a href="#"><?php echo $modelName; ?></a></h2>
                            <ul>
                                <li><span class="impl_fea_title">Year</span>
                                    <span class="impl_fea_name"><?php echo $year; ?></span></li>
                                <li><span class="impl_fea_title">Status</span>
                                    <span class="impl_fea_name"><?php echo $status; ?></span></li>
                                <li><span class="impl_fea_title">Made In</span>
                                    <span class="impl_fea_name"><?php echo $place; ?></span></li>
                                <li><span class="impl_fea_title">Stock</span>
                                    <span class="impl_fea_name"><?php echo $qty; ?> available</span></li>
                            </ul>
                            <div class="impl_fea_btn">
                                <button class="impl_btn">
                                    <span class="impl_doller">$ <?php echo number_format($price); ?> </span>
                                    <span class="impl_bnw">buy now</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>