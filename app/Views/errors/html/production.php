<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title>Ops!</title>

	<style type="text/css">
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
	</style>
</head>
<body>

	<div class="container text-center">

		<h1 class="headline">Ops!</h1>

		<p class="lead">Parece que houve um problema com a página que você esta tentando acessar, tente novamente mais tarde...</p>

	</div>

</body>

</html>
