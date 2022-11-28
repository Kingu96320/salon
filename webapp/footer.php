
</section>
<div class="clear"></div>
		<div class="clear"></div>
  <?php $system = query_by_id("SELECT * from `system` where active='0'",["id"=>$id],$conn)[0]; ?>
  <!-- Footer -->
  <footer class="py-5 ">
    <div class="container">
  	  <div class="row mt-5 mb-5 text-center">
  			<div class="col-lg-12 mt-3 mb-3">
  				<h4><i class="fas fa-map-marker-alt"></i> <?= systemname_app(1) ?></h4>
  				<p><?= $system['address'] ?></p>
  			</div>
  			<div class="col-lg-12 mt-3 mb-3">
  				<h5 class="mt-0"><i class="fas fa-phone"></i> <?= $system['phone'] ?> </h5>
  				<h5 class="mt-3"><i class="fas fa-envelope"></i> <?= $system['email'] ?> </h5>
  			</div>
  		</div>
      <p class="m-0 text-center ">Copyright &copy; <?= ucfirst(strtolower(stripslashes(systemname_app(1)))) ?>  <?php echo date('Y') ?></p>
    </div>
    <!-- /.container -->  
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <script type="text/javascript" src="js/datetimepicker.js"></script>
  <script type="text/javascript" src="js/script.js?var=<?= rand(00,99) ?>"></script>
</body>

</html>
