<?php
session_start();
include_once("navbar.php");
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email'])) {
    $email = mysqli_real_escape_string($con, $_SESSION['email']);

    $selectUserIdQuery = "SELECT id FROM user WHERE email = '$email'";
    $result = mysqli_query($con, $selectUserIdQuery);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $userId = $row['id'];

            // Get shipping details from the form
            $country = mysqli_real_escape_string($con, $_POST['country']);
            $state = mysqli_real_escape_string($con, $_POST['state']);
            $zipCode = mysqli_real_escape_string($con, $_POST['zip']);

            // Insert shipping details into the shippingdetails table
            $insertShippingDetailsQuery = "INSERT INTO shippingdetails (user_id, Country, State, Zip) VALUES ('$userId', '$country', '$state', '$zipCode')";
            
            if (mysqli_query($con, $insertShippingDetailsQuery)) {
                echo '<script>alert("Shipping details saved successfully!");</script>';
            } else {
                echo '<script>alert("Error saving shipping details: ' . mysqli_error($con) . '");</script>';
            }
        } else {
            echo '<script>alert("User not found.");</script>';
        }
    } else {
        echo '<script>alert("Error: ' . $selectUserIdQuery . '<br>' . mysqli_error($con) . '");</script>';
    }
}
?>


<!-- END nav -->

<div class="hero-wrap hero-bread" style="background-image: url('images/bg_1.jpg');">
	<div class="container">
		<div class="row no-gutters slider-text align-items-center justify-content-center">
			<div class="col-md-9 ftco-animate text-center">
				<p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Cart</span></p>
				<h1 class="mb-0 bread">My Cart</h1>
			</div>
		</div>
	</div>
</div>

<section class="ftco-section ftco-cart">
	<div class="container">
		<div class="row">
			<div class="col-md-12 ftco-animate">
				<div class="cart-list">
					<table class="table">
						<thead class="thead-primary">
							<tr class="text-center">
								<th>Product Image</th>
								<th>Product name</th>
								<th>Price</th>
								<th>Quantity</th>
								<th>Total</th>
								<th>Remove</th>
							</tr>
						</thead>
						<tbody>
							<?php
							require_once("db.php");

							if (isset($_SESSION['email'])) {
								$email = $_SESSION['email'];
								$selectUserIdQuery = "SELECT id FROM user WHERE email = '$email'";
								$result = mysqli_query($con, $selectUserIdQuery);

								if ($result) {
									$row = mysqli_fetch_assoc($result);

									if ($row) {
										$userId = $row['id'];

										// Retrieve aggregated cart items for the logged-in user
										$selectCartItemsQuery = "SELECT productname, SUM(quantity) as totalQuantity, price, SUM(price * quantity) as totalPrice FROM cart WHERE user_id = '$userId' GROUP BY productname";
										$cartItemsResult = mysqli_query($con, $selectCartItemsQuery);

										if ($cartItemsResult) {
											while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
												echo '<tr class="text-center">';
												echo '<td class="image-prod"><div class="img" style="background-image:url(./images/category-3.jpg)"></div></td>
												</td>';
												echo '<td class="product-name"><h2 class="h5 text-black">' . $cartItem['productname'] . '</h2></td>';
												echo '<td class="price">$' . $cartItem['price'] . '</td>';
												echo '<td>';
												echo '<input type="text" class="form-control text-center quantity-amount" value="' . $cartItem['totalQuantity'] . '" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1" disabled>';
												echo '</div>';
												echo '</td>';
												echo '<td>$' . $cartItem['totalPrice'] . '</td>';
												echo '<td><a href="#" class="btn fw-bold btn-sm" onclick="removeItem(\'' . $cartItem['productname'] . '\')">X</a></td>';
												echo '</tr>';
											}
										} else {
											echo 'Error fetching cart items: ' . mysqli_error($con);
										}
									} else {
										echo 'User not found.';
									}
								} else {
									echo 'Error: ' . $selectUserIdQuery . mysqli_error($con);
								}
							} else {
								echo 'User is not logged in.';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row justify-content-end">
			<div class="col-lg-7 mt-5 cart-wrap ftco-animate">
				<div class="cart-total mb-3">
					<h3>Estimate shipping and tax</h3>
					<p>Enter your destination to get a shipping estimate</p>
					<form action="#" method="post" class="info">
						<div class="form-group">
							<label for="country">Country</label>
							<input type="text" class="form-control text-left px-3" name="country" placeholder="">
						</div>
						<div class="form-group">
							<label for="state">State/Province</label>
							<input type="text" class="form-control text-left px-3" name="state" placeholder="">
						</div>
						<div class="form-group">
							<label for="zip">Zip/Postal Code</label>
							<input type="text" class="form-control text-left px-3" name="zip" placeholder="">
						</div>
						<button type="submit" class="btn btn-primary py-3 px-4 d-block m-auto" style="color: white;">Save Shipping Details</button>
					</form>
				</div>
			</div>
			<div class="col-lg-4 mt-5 cart-wrap ftco-animate">
				<div class="cart-total mb-3">
					<h3>Cart Totals</h3>
					<?php
					require_once("db.php");

					if (isset($_SESSION['email'])) {
						$email = $_SESSION['email'];
						$selectUserIdQuery = "SELECT id FROM user WHERE email = '$email'";
						$result = mysqli_query($con, $selectUserIdQuery);

						if ($result) {
							$row = mysqli_fetch_assoc($result);

							if ($row) {
								$userId = $row['id'];

								// Retrieve cart items for the logged-in user
								$selectCartItemsQuery = "SELECT productname, price, quantity FROM cart WHERE user_id = '$userId'";
								$cartItemsResult = mysqli_query($con, $selectCartItemsQuery);

								if ($cartItemsResult) {
									$subtotal = 0;

									while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
										$subtotal += ($cartItem['price'] * $cartItem['quantity']);
									}

									$total = $subtotal; // In this example, total is the same as subtotal. You can modify this based on your requirements.

									echo '<div class="row mb-3">';
									echo '<div class="col-md-6">';
									echo '<span class="text-black">Subtotal</span>';
									echo '</div>';
									echo '<div class="col-md-6 text-right">';
									echo '<strong class="text-black">$' . number_format($subtotal, 2) . '</strong>';
									echo '</div>';
									echo '</div>';

									echo '<div class="row mb-5">';
									echo '<div class="col-md-6">';
									echo '<span class="text-black">Total</span>';
									echo '</div>';
									echo '<div class="col-md-6 text-right">';
									echo '<strong class="text-black">$' . number_format($total, 2) . '</strong>';
									echo '</div>';
									echo '</div>';
								} else {
									echo 'Error fetching cart items: ' . mysqli_error($con);
								}
							} else {
								echo 'User not found.';
							}
						} else {
							echo 'Error: ' . $selectUserIdQuery . '<br>' . mysqli_error($con);
						}
					} else {
						echo 'User is not logged in.';
					}
					?>
				</div>
				<p><a href="checkout.php" class="btn btn-primary py-3 px-4 d-block m-auto">Proceed to Checkout</a></p>
			</div>
		</div>
	</div>
	</div>
</section>

<section class="ftco-section ftco-no-pt ftco-no-pb py-5 bg-light">
	<div class="container py-4">
		<div class="row d-flex justify-content-center py-5">
			<div class="col-md-6">
				<h2 style="font-size: 22px;" class="mb-0">Subcribe to our Newsletter</h2>
				<span>Get e-mail updates about our latest shops and special offers</span>
			</div>
			<div class="col-md-6 d-flex align-items-center">
				<form action="#" class="subscribe-form">
					<div class="form-group d-flex">
						<input type="text" class="form-control" placeholder="Enter email address">
						<input type="submit" value="Subscribe" class="submit px-3">
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<footer class="ftco-footer ftco-section">
	<div class="container">
		<div class="row">
			<div class="mouse">
				<a href="#" class="mouse-icon">
					<div class="mouse-wheel"><span class="ion-ios-arrow-up"></span></div>
				</a>
			</div>
		</div>
		<div class="row mb-5">
			<div class="col-md">
				<div class="ftco-footer-widget mb-4">
					<h2 class="ftco-heading-2">Vegefoods</h2>
					<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia.</p>
					<ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
						<li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
						<li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
						<li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
					</ul>
				</div>
			</div>
			<div class="col-md">
				<div class="ftco-footer-widget mb-4 ml-md-5">
					<h2 class="ftco-heading-2">Menu</h2>
					<ul class="list-unstyled">
						<li><a href="#" class="py-2 d-block">Shop</a></li>
						<li><a href="#" class="py-2 d-block">About</a></li>
						<li><a href="#" class="py-2 d-block">Journal</a></li>
						<li><a href="#" class="py-2 d-block">Contact Us</a></li>
					</ul>
				</div>
			</div>
			<div class="col-md-4">
				<div class="ftco-footer-widget mb-4">
					<h2 class="ftco-heading-2">Help</h2>
					<div class="d-flex">
						<ul class="list-unstyled mr-l-5 pr-l-3 mr-4">
							<li><a href="#" class="py-2 d-block">Shipping Information</a></li>
							<li><a href="#" class="py-2 d-block">Returns &amp; Exchange</a></li>
							<li><a href="#" class="py-2 d-block">Terms &amp; Conditions</a></li>
							<li><a href="#" class="py-2 d-block">Privacy Policy</a></li>
						</ul>
						<ul class="list-unstyled">
							<li><a href="#" class="py-2 d-block">FAQs</a></li>
							<li><a href="#" class="py-2 d-block">Contact</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md">
				<div class="ftco-footer-widget mb-4">
					<h2 class="ftco-heading-2">Have a Questions?</h2>
					<div class="block-23 mb-3">
						<ul>
							<li><span class="icon icon-map-marker"></span><span class="text">203 Fake St. Mountain View, San Francisco, California, USA</span></li>
							<li><a href="#"><span class="icon icon-phone"></span><span class="text">+2 392 3929 210</span></a></li>
							<li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@yourdomain.com</span></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-center">

				<p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
					Copyright &copy;<script>
						document.write(new Date().getFullYear());
					</script> All rights reserved | This template is made with <i class="icon-heart color-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
					<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
				</p>
			</div>
		</div>
	</div>
</footer>



<!-- loader -->
<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
		<circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
		<circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" />
	</svg></div>


<script src="js/jquery.min.js"></script>
<script src="js/jquery-migrate-3.0.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/jquery.waypoints.min.js"></script>
<script src="js/jquery.stellar.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/aos.js"></script>
<script src="js/jquery.animateNumber.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/scrollax.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
<script src="js/google-map.js"></script>
<script src="js/main.js"></script>

<script>
	function removeItem(itemName) {
		$.ajax({
			type: 'POST',
			url: 'remove_item.php',
			data: {
				itemName: itemName
			},
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					alert(response.message);
					location.reload();
				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function(xhr, status, error) {
				alert('Error: ' + error);
			}
		});
	}
</script>


</body>

</html>