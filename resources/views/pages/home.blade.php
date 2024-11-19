 <!-- resources/views/home.blade.php -->
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Home Page</title>
		<link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Include CSS -->
	</head>
	<body>
		<header>
			<h1>Welcome to Laravel Home Page</h1>
			<nav>
				<ul>
					<li><a href="#">Home</a></li>
					<li><a href="#">About</a></li>
					<li><a href="#">Contact</a></li>
				</ul>
			</nav>
		</header>
		
		<main>
			<section>
				<h2>About Us</h2>
				<p>This is the home page of your Laravel application.</p>
			</section>
		</main>
		
		<footer>
			<p>&copy; 2024 Your Laravel App</p>
		</footer>
	</body>
	</html>
