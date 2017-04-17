<!DOCTYPE HTML>
<html lang=en>
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<link rel="shortcut icon" href="biological-safety-16-57073.png" type="image/png">
		<link href="/css/main.css" media="screen" type="text/css" rel="stylesheet">
		<link href="/css/kdo_style.css" media="screen" type="text/css" rel="stylesheet">
		<link href="/css/print.css" media="print" type="text/css" rel="stylesheet">
		<link href="/css/jquery-ui.css" type="text/css" rel="stylesheet">		
		<link charset="utf-8" href="css/font.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="/jquery/jquery-2.2.0.min.js"></script>
		<script type="text/javascript" src="/jquery/jquery.maskedinput.js"></script>
		<script type="text/javascript" src="/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/js.js"></script>
		<script type="text/javascript" src="/js/kdo_js.js"></script>
	</head>

	<body>
		<div class="wrapper">			

			<nav>
				<a href='/' <?=$active_log;?>>Логи</a>
				<a href='/search_address.php' <?=$active_search;?>>Поиск</a>
			</nav>
			<div class="header"></div>

			<div class="main"><?php include_once($body) ?></div>
		</div>
    </body> 
</html>