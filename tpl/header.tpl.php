<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de-DE">
<head>
	<title><?=$globvar['title']?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="3 days" />
	<meta name="language" content="<?=$globvar['language']?>" />
	<meta name="content-language" content="<?=$globvar['language']?>" />
	<meta name="description" content="<?=$globvar['metadescription']?>" />
	<link rel="stylesheet" href="inc/style.css" type="text/css" media="screen" />
	
	<script type="text/javascript" src="inc/mootools.js"></script>
	<script type="text/javascript">
	window.addEvent('domready', function(){
		var mySlide = new Fx.Slide('slideheader', {mode: 'horizontal'});
		
		mySlide.hide();
		mySlide.slideIn();
		
		var Tips2 = new Tips($$('.Tips2'), {
			initialize:function(){
				this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
			},
			onShow: function(toolTip) {
				this.fx.start(1);
			},
			onHide: function(toolTip) {
				this.fx.start(0);
			}
		});
		

	});
	</script>
</head>
<body>
<div id="container">
<div id="slideheader"><h1><a href="./"><?=$globvar['title']?></a> <small><?=$globvar['2ndtitle']?></small></h1></div>
