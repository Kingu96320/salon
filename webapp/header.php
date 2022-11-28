<?php session_start(); ?>
<?php include '../includes/db_include.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?= systemname_app(1) ?></title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/modern-business.css?ver=<?= rand(00,99) ?>" rel="stylesheet">
  <script src="https://kit.fontawesome.com/f851861c02.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar  navbar-expand-lg navbar-dark  ">
    <div class="container">
      <a class="navbar-brand" href="<?= BASE_URL.'webapp' ?>" style="margin:0px auto;">
		<img src="<?php echo BASE_URL.systemlogo($conn); ?>" class="img-fluid" style="max-width:150px;">
	  </a>
    </div>
  </nav>