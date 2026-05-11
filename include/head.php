<!-- Basic Page Info -->
<meta charset="utf-8">
<title>

	<?php echo set("site_title"); ?>
</title>

<!-- Site favicon -->
<!-- <link rel="shortcut icon" href="images/favicon.ico"> -->

<!-- Mobile Specific Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<!-- Google Font -->
<!-- <link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
	rel="stylesheet"> -->

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap"
	rel="stylesheet">
<!-- CSS -->

<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/responsive.dataTables.css">

<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<!-- <script src="//code.jquery.com/jquery-3.6.0.min.js"></script> -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
	integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
	</script>

<link rel="canonical" href="https://aydinogullariysc.com/index.php?p=home" />

<!-- Global site tag (gtag.js) - Google Analytics -->
<link rel="stylesheet" href="vendors/styles/style.css?v=<?php echo filemtime('vendors/styles/style.css'); ?>">

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
<!-- manifest.json -->
<link rel="manifest" href="/manifest.json">

<!-- Styles -->
<script>
	window.dataLayer = window.dataLayer || [];

	function gtag() {
		dataLayer.push(arguments);
	}
	gtag('js', new Date());

	gtag('config', 'UA-119386393-1');
</script>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap');


</style>

<script>
	// Sayfa yüklenmeden önce localStorage'dan tema bilgisini al
	(function () {
		const theme = localStorage.getItem('theme');
		if (theme === 'dark') {
			document.documentElement.classList.add('dark-mode');
			document.addEventListener('DOMContentLoaded', function () {
				document.getElementById('dark-mode').style.display = 'block';
				document.getElementById('light-mode').style.display = 'none';
			});
		} else {
			document.documentElement.classList.remove('dark-mode');
			document.addEventListener('DOMContentLoaded', function () {
				document.getElementById('dark-mode').style.display = 'none';
				document.getElementById('light-mode').style.display = 'block';
			});
		}
	})();
</script>