<?php
session_start();

include_once("navbar.php");
?>
<?php
require_once("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_SESSION['email'])) {
		$email = $_SESSION['email'];

		$selectUserIdQuery = "SELECT id FROM user WHERE email = ?";

		$stmt = mysqli_prepare($con, $selectUserIdQuery);
		mysqli_stmt_bind_param($stmt, "s", $email);
		mysqli_stmt_execute($stmt);

		$result = mysqli_stmt_get_result($stmt);

		if ($result) {
			$row = mysqli_fetch_assoc($result);

			if ($row) {
				$userId = $row['id'];

				// Capture and validate billing details from the form
				$country = validateInput($_POST['c_country']);
				$fname = validateInput($_POST['c_fname']);
				$lname = validateInput($_POST['c_lname']);
				$address = validateInput($_POST['c_address']);
				$state_country = validateInput($_POST['c_state_country']);
				$postal_zip = validateInput($_POST['c_postal_zip']);
				$email_address = validateInput($_POST['c_email_address']);
				$phone = validateInput($_POST['c_phone']);
				// Insert billing details into the shippingdetails table
				$insertBillingQuery = "INSERT INTO billingdetails (user_id, fname, lname, country, streetaddress, town, zipcode, phone, email)
                                       VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";

				$stmt = mysqli_prepare($con, $insertBillingQuery);
				mysqli_stmt_bind_param($stmt, "issssssss", $userId, $fname, $lname, $country, $address, $state_country, $postal_zip, $email_address, $phone);

				if (mysqli_stmt_execute($stmt)) {
					echo '<script>alert("Billing details saved successfully.")</script>';
				} else {
					echo 'Error: ' . $insertBillingQuery . '<br>' . mysqli_error($con);
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
}

function validateInput($input)
{
	// Perform additional validation if needed
	$validatedInput = trim($input);
	$validatedInput = stripslashes($validatedInput);
	$validatedInput = htmlspecialchars($validatedInput);

	return $validatedInput;
}
?>

<!-- END nav -->

<div class="hero-wrap hero-bread" style="background-image: url('images/bg_1.jpg');">
	<div class="container">
		<div class="row no-gutters slider-text align-items-center justify-content-center">
			<div class="col-md-9 ftco-animate text-center">
				<p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Checkout</span></p>
				<h1 class="mb-0 bread">Checkout</h1>
			</div>
		</div>
	</div>
</div>

<section class="ftco-section">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-7 ftco-animate">
				<h2 class="h3 mb-3 text-black">Billing Details</h2>

				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<div class="form-group">
						<label for="c_country" class="text-black">Country <span class="text-danger">*</span></label>
						<select id="c_country" class="form-control" name="c_country">
							<option value="1">Select a country</option>
							<option value="2">bangladesh</option>
							<option value="3">Algeria</option>
							<option value="4">Afghanistan</option>
							<option value="5">Ghana</option>
							<option value="6">Albania</option>
							<option value="7">Bahrain</option>
							<option value="8">Colombia</option>
							<option value="9">Dominican Republic</option>
						</select>
					</div>
					<div class="form-group row">
						<div class="col-md-6">
							<label for="c_fname" class="form-label text-black">First Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_fname" name="c_fname" required>
						</div>
						<div class="col-md-6">
							<label for="c_lname" class="form-label text-black">Last Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_lname" name="c_lname" required>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-12">
							<label for="c_address" class="form-label text-black">Address <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_address" name="c_address" placeholder="Street address" required>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-6">
							<label for="c_state_country" class="form-label text-black">State / Country <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_state_country" name="c_state_country" required>
						</div>
						<div class="col-md-6">
							<label for="c_postal_zip" class="form-label text-black">Posta / Zip <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_postal_zip" name="c_postal_zip" required>
						</div>
					</div>

					<div class="form-group row mb-5">
						<div class="col-md-6">
							<label for="c_email_address" class="form-label text-black">Email Address <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_email_address" name="c_email_address" required>
						</div>
						<div class="col-md-6">
							<label for="c_phone" class="form-label text-black">Phone <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="c_phone" name="c_phone" placeholder="Phone Number" required>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-lg btn-block d-block m-auto mt-3" id="saveBillingBtn">Save Billing
							Details</button>
					</div>
				</form>
			</div>


				<div class="col-md-12">
					<h2 class="h3 mb-3 text-black">Your Order</h2>
					<form id="orderForm">

						<div class="p-3 p-lg-5 border bg-white">
							<table class="table site-block-order-table mb-5">
								<thead>
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
												$selectCartItemsQuery = "SELECT productname, price, SUM(quantity) as totalQuantity FROM cart WHERE user_id = '$userId' GROUP BY productname";
												$cartItemsResult = mysqli_query($con, $selectCartItemsQuery);

												if ($cartItemsResult) {
													$subtotal = 0;

													echo '<tbody>';

													while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
														$productName = $cartItem['productname'];
														$quantity = $cartItem['totalQuantity'];
														$price = $cartItem['price'] * $quantity;
														$subtotal += $price;

														echo '<tr>';
														echo '<td>' . $productName . ' <strong class="mx-2">x</strong> ' . $quantity . '</td>';
														echo '<td>$' . number_format($price, 2) . '</td>';
														echo '</tr>';
													}

													echo '<tr>';
													echo '<td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>';
													echo '<td class="text-black">$' . number_format($subtotal, 2) . '</td>';
													echo '</tr>';

													echo '<tr>';
													echo '<td class="text-black font-weight-bold"><strong>Order Total</strong></td>';
													echo '<td class="text-black font-weight-bold"><strong>$' . number_format($subtotal, 2) . '</strong></td>';
													echo '</tr>';

													echo '</tbody>';
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

									</tbody>
							</table>
							<div class="form-group">
								<button id="placeOrderBtn" class="btn btn-primary" onclick="placeOrder()">Place Order</button>
							</div>

						</div>
					</form>
			</div>

		</div> <!-- .col-md-8 -->
	</div>
</section> <!-- .section -->

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
	$(document).ready(function() {

		var quantitiy = 0;
		$('.quantity-right-plus').click(function(e) {

			// Stop acting like a button
			e.preventDefault();
			// Get the field name
			var quantity = parseInt($('#quantity').val());

			// If is not undefined

			$('#quantity').val(quantity + 1);


			// Increment

		});

		$('.quantity-left-minus').click(function(e) {
			// Stop acting like a button
			e.preventDefault();
			// Get the field name
			var quantity = parseInt($('#quantity').val());

			// If is not undefined

			// Increment
			if (quantity > 0) {
				$('#quantity').val(quantity - 1);
			}
		});

	});
</script>

</body>

</html>