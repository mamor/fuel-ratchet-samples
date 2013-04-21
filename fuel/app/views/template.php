<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $title; ?> | FuelPHP x Ratchet Demo</title>

<meta name="description" content="FuelPHP x Ratchet Demo">
<meta name="author" content="mamor">

<!--og-->
<meta property="og:title" content="FuelPHP x Ratchet Demo"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="http://fuelratchet.madroom.org/"/>
<meta property="og:image" content="http://madroom.org/assets/img/wolf.png"/>
<meta property="og:site_name" content="FuelPHP x Ratchet Demo"/>
<meta property="fb:admins" content="1821765896"/>
<meta property="og:description" content="FuelPHP x Ratchet Demo"/>
<!--/og-->

<?php echo Asset::render('global'); ?>

<?php if (($webfont = Config::get('app.webfont', false)) !== false): ?>
<link href="http://fonts.googleapis.com/css?family=<?php echo urlencode($webfont); ?>" rel="stylesheet" type="text/css">
<style>
body { font-family: "<?php echo $webfont; ?>", sans-serif; }
</style>
<?php endif; ?>

<?php echo Asset::render('local'); ?>

</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
<div class="navbar-inner">
<div class="container">

<a class="brand" href="<?php echo Uri::base(); ?>">FuelPHP x Ratchet Demo</a>
<ul class="nav">
<li><a href="https://github.com/mp-php/fuel-ratchet-samples/issues" target="_blank">Issues</a></li>
</ul>

</div><!--/container-fluid-->
</div><!--/navbar-inner-->
</div><!--/navbar-->

<div class="container">

<?php echo $content; ?>

<hr />

<footer>

	<div class="pull-left">
		Developed by <a target="_blank" href="http://madroom-project.blogspot.jp/">madroom project</a>
		<span id="icons">
			<a href="https://github.com/mp-php" target="_blank"><i class="icon-github icon-2x"></i></a>
			<a href="https://twitter.com/madmamor" target="_blank"><i class="icon-twitter icon-2x"></i></a>
			<a href="https://www.facebook.com/mamoru.otsuka" target="_blank"><i class="icon-facebook icon-2x"></i></a>
			<a href="https://plus.google.com/u/0/104213825825883199069/posts" target="_blank"><i class="icon-google-plus icon-2x"></i></a>
		</span>
	</div>
	
	<div class="pull-right">
		<div>
			<!-- https://dev.twitter.com/docs/tweet-button -->
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="<?php echo Uri::base(); ?>" data-text="FuelPHP x Ratchet Demo" data-hashtags="fuelphp">Tweet</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>

		<div>
			<!-- https://developers.google.com/+/plugins/+1button/?hl=ja -->
			<div class="g-plusone" data-size="medium" lang="en-US" href="<?php echo Uri::base(); ?>"></div>
			<script type="text/javascript">
				(function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				})();
			</script>
		</div>

		<div>
			<!-- https://developers.facebook.com/docs/reference/plugins/like/ -->
			<div id="fb-root"></div>
			<script>
				(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
			<div class="fb-like" data-href="<?php echo Uri::base(); ?>" data-send="false" data-layout="button_count" data-width="75" data-show-faces="true"></div>
		</div>
	</div><!--.pull-right-->
</footer>

</div>

<a target="_blank" href="https://github.com/mp-php/fuel-packages-ratchet"><img style="position: absolute; top: 0; right: 0; border: 0; z-index: 9999;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png" alt="Fork me on GitHub"></a>

</body>
</html>
