<?php
namespace random_crosswords_generator;

session_start();

require_once('Page.php');
$page = new Page("Through the Looking-Glass", ["looking-glass"]);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Looking-Glass Random Crosswords Generator – Ralf’s Portfolio</title>
		<meta name="description" content="A PHP Model-View-Crontroller random crosswords generator, which creates a different crossword every time the page is loaded."/>

		<link rel="stylesheet" href="/alice.css">

		<link rel="canonical" href="ralf.infinityfreeapp.com/random-crosswords-generator/through-the-looking-glass.php">

		<meta property="og:type" content="website">
		<meta property="og:title" content="Looking-Glass Random Crosswords Generator – Ralf’s Portfolio">
		<meta property="og:image" content="thumbnail.jpg" alt="Presentation of the website ralf.infinityfreeapp.com">
		<meta property="og:description" content="A PHP Model-View-Crontroller random crosswords generator, which creates a different crossword every time the page is loaded.">
		<meta property="og:url" content="ralf.infinityfreeapp.com/random-crosswords-generator/through-the-looking-glass.php">
	</head>

	<body>
		<header>
			<a id="logo" href="/">
				<span>Ralf’s Portfolio</span>
				<span><b>Programming projects</b> developed by <b>Ralf Gunter Rotstein</b> (and a poem).</span>
			</a>
		</header>
		
		<div id="intro">
			<h1>Looking-Glass Random Crosswords</h1>
			<p>This is a minimalist example of a <b>PHP</b> Model-View-Crontroller random crosswords generator, which creates a different crossword from a set of possible answers and questions every time the page is loaded, and a <b>JS</b> script that interacts with the generated crossword. <b>Refresh the page to get a different crossword</b>, or try to solve the current one. (Code available on <a href="https://github.com/RalfGunterRotstein/RandomCrosswordsGenerator">GitHub</a>.)</p>
		</div>

		<main>
			<?php $page->display(); ?>

			<div id="return-link__div"><a href="/">back to Ralf’s Portfolio</a></div>
        </main>
		
		<div class="quote">
			<p class="quote__text">It takes all the running you can do, to keep in the same place. If you want to get somewhere else, you must run at least twice as fast as that!</p>
			
			<span class="quote__author">Lewis Carroll</span>
		</div>

		<footer>
			<a href="/">Ralf’s Portfolio</a>
		</footer>
	</body>
</html>