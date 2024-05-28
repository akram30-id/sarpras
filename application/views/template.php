<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?= $module ?? 'Home' ?> - <?= $title ?? 'Title' ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?= base_url() ?>assets/img/favicon.png" rel="icon">
  <link href="<?= base_url() ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= base_url() ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url() ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url() ?>assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?= base_url() ?>assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?= base_url() ?>assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?= base_url() ?>assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?= base_url() ?>assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?= base_url() ?>assets/css/style.css" rel="stylesheet">

	<!-- JQUERY AUTOCOMPLETE CSS -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

	<!-- JQUERY -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

	<!-- DATATABLES CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">

	<!-- DATATABLES JS -->
	<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>

	<!-- JQUERY AUTOCOMPLETE JS -->
	<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

	<!-- FULLCALENDAR JS -->
	<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="" class="logo d-flex align-items-center">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?= $this->session->user->photo ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?= $this->session->user->username; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?= $this->session->user->name; ?></h6>
              <span><?= $this->session->user->role_name; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?= base_url('auth/logout/' . $this->session->user->username) ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index.html">
          <i class="bi bi-grid"></i>
          <span><?= $module ?></span>
        </a>
      </li><!-- End Dashboard Nav -->

			<li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#area-nav" data-bs-toggle="collapse" href="#">
					<i class="bi bi-building"></i><span>Area</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="area-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= base_url('area/master') ?>">
              <i class="bi bi-circle"></i><span>Master Area</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('area/add') ?>">
              <i class="bi bi-circle"></i><span>Buat Area Baru</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('area/assign') ?>">
              <i class="bi bi-circle"></i><span>Assign PIC Area</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('area/book') ?>">
              <i class="bi bi-circle"></i><span>Booking Area</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('area/schedule') ?>">
              <i class="bi bi-circle"></i><span>Jadwal Booking Area</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('area/approve') ?>">
              <i class="bi bi-circle"></i><span>Approval Booking Area</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('area/checkout') ?>">
              <i class="bi bi-circle"></i><span>Checkout</span>
            </a>
          </li>
        </ul>
      </li><!-- End Area Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#inventory-nav" data-bs-toggle="collapse" href="#">
					<i class="bi bi-projector"></i><span>Inventaris</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="inventory-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= base_url('item/master') ?>">
              <i class="bi bi-circle"></i><span>Master Item</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('item/master?area=1') ?>">
              <i class="bi bi-circle"></i><span>Master Item Area</span>
            </a>
          </li>
					<?php if (in_array($this->session->user->role, [1,2])){ ?>
						<li>
							<a href="<?= base_url('item/add') ?>">
								<i class="bi bi-circle"></i><span>Tambah Item</span>
							</a>
						</li>
					<?php } ?>
					<li>
            <a href="<?= base_url('item/request') ?>">
              <i class="bi bi-circle"></i><span>Request Pinjam Item</span>
            </a>
          </li>
					<li>
            <a href="<?= base_url('item/show_report') ?>">
              <i class="bi bi-circle"></i><span>Report Item Hilang/Rusak</span>
            </a>
          </li>
					<li>
						<a href="<?= base_url('item/return') ?>">
							<i class="bi bi-circle"></i><span>Pengembalian Item</span>
						</a>
					</li>
					<?php if (in_array($this->session->user->role, [1,2])){ ?>
						<li>
							<a href="<?= base_url('item/approve') ?>">
								<i class="bi bi-circle"></i><span>Approval Item Pinjaman</span>
							</a>
						</li>
					<?php } ?>
					<?php if ($this->session->user->role == "1"){ ?>
						<li>
							<a href="<?= base_url('item/destroy') ?>">
								<i class="bi bi-circle"></i><span>Destroy Item</span>
							</a>
						</li>
					<?php } ?>
        </ul>
      </li><!-- End Inventaris Nav -->

			<li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#ekskul-nav" data-bs-toggle="collapse" href="#">
					<i class="bi bi-dribbble"></i><span>Ekstrakurikuler</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="ekskul-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Master Eksul</span>
            </a>
          </li>
					<li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Assign PIC Ekskul</span>
            </a>
          </li>
					<li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Jadwal Ekskul</span>
            </a>
          </li>
        </ul>
      </li><!-- End Inventaris Nav -->

			<li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#user-nav" data-bs-toggle="collapse" href="#">
					<i class="bi bi-person"></i><span>Master User</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="user-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
					<li>
            <a href="<?= base_url('user/master') ?>">
              <i class="bi bi-circle"></i><span>Data User</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('user/register') ?>">
              <i class="bi bi-circle"></i><span>Registrasi User</span>
            </a>
          </li>
					<li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Reset Password User</span>
            </a>
          </li>
        </ul>
      </li><!-- End Master User Nav -->

			<li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#usersettings-nav" data-bs-toggle="collapse" href="#">
					<i class="bi bi-gear"></i><span>Account Settings</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="usersettings-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Ganti Password</span>
            </a>
          </li>
					<li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Update Profil</span>
            </a>
          </li>
        </ul>
      </li><!-- End User Settings Nav -->

    </ul>

  </aside><!-- End Sidebar-->

	<main id="main" class="main">
	<div class="pagetitle">
      <h1><?= $module ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html"><?= $module ?></a></li>
        </ol>
      </nav>
	</div><!-- End Page Title -->

		<?= $content ?? '<h1>Hello World</h1>' ?>
	</main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Holy Faithful Obedient</span></strong>. All Rights Reserved
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?= base_url() ?>assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/chart.js/chart.umd.js"></script>
  <script src="<?= base_url() ?>assets/vendor/echarts/echarts.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/quill/quill.js"></script>
  <script src="<?= base_url() ?>assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?= base_url() ?>assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="<?= base_url() ?>assets/js/main.js"></script>
  <script src="<?= base_url() ?>js/validation.js"></script>

</body>

</html>
