<?php global $artifact;?>

<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>V · <?php echo ucfirst($artifact->attributes['name']);?></title>

	<meta name="viewport" content="width=device-width, initial-scale=0.8">

	<meta property="og:url" content="https://v-os.ca/">
	<meta property="og:title" content="V-OS">
	<meta property="og:type" content="website">
	<meta property="og:description" content="The Vi Wiki">
	<meta property="og:image" content="https://v-os.ca/images/v-os/1.png">

	<meta name="twitter:url" content="https://v-os.ca/">
	<meta name="twitter:title" content="V-OS">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:description" content="The Vi Wiki">
	<meta name="twitter:image" content="https://v-os.ca/images/v-os/1.png">

	<meta name="description" content="The V Wiki">
	<meta name="keywords" content="Digital, Art, Design, Videogames, Games, Music, Portfolio, Montreal">
	<meta name="author" content="Victor Ivanov">
	<link rel='icon' href='https://v-os.ca/assets/ui/v_ico.ico' type='image/x-icon'>

	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Merriweather:400,400i,900|Roboto:400,400i,700|Roboto+Mono">
	<link rel="stylesheet" type="text/css" href="assets/styles/style.css?ver=<?php echo filemtime('assets/styles/style.css');?>">
</head>

<body>
	<a href="home">
		<canvas id="userCanvas" width="400" height="400">
			This feature (html canvas) does not work on your browser.
		</canvas>
	</a>
	
	<div id="header">
		<div class="header-image" style="background-image: url(<?php echo addslashes($artifact->attributes['image']);?>)">
			<div class="header-content">
				<span class="header-title"><?php echo $artifact->attributes['image name'];?></span>
			</div>
		</div>
	</div>

	<div id="no-page-header"></div>

	<div id="body">
		<div id="full-container">
			<div id="sidebar">
				<?php
				if ($artifact->path) {
					echo '<div class="path-container">';
					echo $artifact->path;
					echo '</div>';
				}
				?>

				<div id="cli">
					<div id="cli-output">
						<span id="lo-cli-output"></span>
					</div>
					<div id="cli-input">
						<span id="cli-symbol">></span><input type="text" id="lo-cli-input" value="" onkeypress="guide(event)"></input>
					</div>
				</div>

				<?php
				echo '<div class="side-divider"></div>';

				if ($data = getLogData()) {
					echo '<a class="side-box" href="https://log.v-os.ca/'. $artifact->attributes['name'] .'">';
					echo $data;
					echo '</a>';
					echo '<div class="side-divider"></div>';
				}
				?>

				<?php
				if ($artifact->links) {
					for ($i = 0; $i < sizeof($artifact->links); $i++) {
						echo $artifact->links[$i];
					}
					echo '<div class="side-divider" style="margin-top: 16px"></div>';
				}
				?>

				<?php
				if ($related = getRelated($artifact, false, null, 'sidebar-related-title', 'sidebar-related-same')) {
					$dir = $artifact->brokenPath[sizeof($artifact->brokenPath) - 2];
					echo '<span class="side-title"><a href="'. $dir .'">'. ucfirst($dir) .'</a></span>';
					echo $related;
					echo '<div class="side-divider"></div>';
				}
				?>

				<?php
				if ($artifact->formattedTags) {
					for ($i = 0; $i < sizeof($artifact->formattedTags); $i++) {
						echo $artifact->formattedTags[$i];
					}
					echo '<div class="side-divider" style="margin-top: 16px"></div>';
				}
				?>

				<a class="neutral-link" href="https://twitter.com/v_exec">Twitter</a> ·
				<a class="neutral-link" href="https://github.com/v-exec">Github</a> ·
				<a class="neutral-link" href="https://v-exec.bandcamp.com/releases">Bandcamp</a>
				<br>
				<br>
				<a class="neutral-link" href="https://soundcloud.com/v_exec">Soundcloud</a> ·
				<a class="neutral-link" href="https://www.instagram.com/v_exec/">Instagram</a> ·
				<a class="neutral-link" href="https://www.linkedin.com/in/victor-ivanov/">LinkedIn</a>
			</div><div id="body-content">

				<div id="mobile-path">
					<?php echo $artifact->path;?>
				</div>

				<div id="title">
					<h1><?php echo $artifact->attributes['title']?></h1>
				</div>

				<div class="divider"></div>

				<div id="content">
					<?php echo $artifact->attributes['content'];?>
				</div>

			</div>
		</div>
	</div>

	<div id="footer">
		<div id="footer-content">
			<div class="footer-left">
				<span class="footer-text">
					<a class="neutral-link" href="https://v-os.ca/victor">Victor Ivanov</a>
					<br>
					<a class="neutral-link" href="https://v-os.ca/home">V-OS</a> · <a class="neutral-link" href="https://log.v-os.ca">LOG</a>
				</span>
			</div>

			<a class="footer-center neutral-link" href='http://webring.xxiivv.com/#random' target='_blank'><img src='https://v-os.ca/assets/ui/ring.svg'></img></a>

			<div class="footer-right">
				<span class="footer-text">
					<?php echo '<a href="https://log.v-os.ca" class="neutral-link">'.getAllDays(null, null);?> days</a><br>
					<a href="https://log.v-os.ca" class="neutral-link">updated
					<?php echo getLastUpdate(null, null);?>
					</a>
				</span>
				<span class="footer-text">
					<?php echo '<a href="https://log.v-os.ca" class="neutral-link">'.getAllHours(null, null);?> hours</a><br>
					<?php echo '<a href="https://log.v-os.ca" class="neutral-link">'.getAllLogs(null, null);?> logs</a>
				</span>
			</div>
		</div>
	</div>

<script src="assets/scripts/logo.js"></script>
<script src="assets/scripts/cli.js"></script>
<script src="assets/scripts/requestscript.js"></script>

<?php checkWhite();?>
<?php checkImage();?>

</body>
</html>