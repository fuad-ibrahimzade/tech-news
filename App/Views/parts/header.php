<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <base href="/">
    <meta name="description" content="Technews">
    <meta name="author" content="Fuad Ibrahimzade">
	  <title><?= $title ?></title>
    <noscript>Please Enable JavaScript and refresh in order to view page.</noscript>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <meta name="login-status" content="<?php echo \App\Controllers\Admin::hasLoggedIn() ?>">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>


    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

    
    <!-- home head componentden -->
    <link type="text/css" rel="stylesheet" href="/assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/style.css"/>
    <link type="text/css" rel="stylesheet" href="/assets/css/fullscreen_spiner.css"/>

    <!-- bu 4si dashboard componentden -->
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="/assets/admin/css/bootstrap.min.css">
    <!-- MetisMenu CSS -->
    <link href="/assets/admin/js/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/admin/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="/assets/admin/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">


    <!-- <script type="module" src="/assets/ngfiles/runtime-es5.js"></script>
    <script type="module" src="/assets/ngfiles/polyfills-es5.js"></script>
    <script type="module" src="/assets/ngfiles/styles-es5.js"></script>
    <script type="module" src="/assets/ngfiles/vendor-es5.js"></script>
    <script type="module" src="/assets/ngfiles/main-es5.js"></script> -->

    <script type="module" src="/assets/ngfiles/runtime-es5.e8a2810b3b08d6a1b6aa.js"></script>
    <script type="module" src="/assets/ngfiles/polyfills-es5.2b9ce34c123ac007a8ba.js"></script>
    <script type="module" src="/assets/ngfiles/main-es5.12f4b04d34bc2272c3ab.js"></script>
	
    <link rel="icon" type="image/x-icon" href="assets/ngfiles/favicon.ico">
</head>
<body>