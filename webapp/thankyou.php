<?php
	include_once 'header.php';
	unset_session['cart'];
	session_destroy();
?>
	<div class="clear"></div>
	<div style="background:#f6f6f6;">
		<section class="pb-5">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 mt-5 text-left">
						<h2 class="">Thank you</h2>
						<p>Your appointment booked successfully.</p>
					</div>
				</div>
			</div>
		</section>
	</div>
<?php
	include_once 'footer.php';
?>