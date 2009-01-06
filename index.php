<?
$globvar = array();
$globvar['title'] = 'IMGhost'; 
$globvar['2ndtitle'] = 'Host you images';
$globvar['imagesupport'] = 'jpg, png, gif';
$globvar['maxsize'] = '1536'; // Angabe in Kilobyte
$globvar['thumbwidth43'] = 180;  	/* neu resize */
$globvar['thumbheight43'] = 135; 	/* für 16/9  */
$globvar['thumbwidth169'] = 240; 	/* und 4/3 	*/
$globvar['thumbheight169'] = 135; 
$globvar['visiblecopyright'] = '<a href="http://www.somegas.de">Sascha Ohms</a>';
$globvar['language'] = 'DE';
$globvar['metadescription'] = 'Ein kostenloser Image-Hoster. Zeige deine Bilder, Freunden, in Foren oder irgendwo im Internet.';
$globvar['use_randomname'] = true; // Zufallsname oder alten Dateinamen übernehmen
$globvar['twitter'] = true; // schaltet die Ausgabe des Twitterlinks an / aus

ob_start();
include('tpl/header.tpl.php');

if(isset($_GET['s'])) {
	if(empty($_GET['s'])) {
		print '<p><img src="inc/img/zeichen.png" alt=""/> Kein Bild angegeben.</p>';
	} elseif(!file_exists('./i/' .$_GET['s']) || is_valid_filename($_GET['s'])) {
		echo '<p><img src="inc/img/zeichen.png" alt=""/> Datei existiert nicht oder ist unzul&auml;ssig.</p>';
	} else {
		$img = $_GET['s'];
		$imgausgabe = './i/' .$img;
		$clickfile = 'clicks/' .$img. '.cl';
		
		if(!file_exists($clickfile)) 
			$count = 1;
		else
			$count = file_get_contents($clickfile)+1;
			
		$fp = @fopen($clickfile, 'w');
		@fwrite($fp, $count);
		@fclose($fp);
		$clicks = 'Hits: ' .$count;
		
		include('tpl/einzel.tpl.php');
	}		
} else {
	include('tpl/index.tpl.php');
		
	if(isset($_POST['nsubmit'])) {
		$tempname = $_FILES['nfile']['tmp_name']; 
		$type = $_FILES['nfile']['type'];		
		$thename = $_FILES['nfile']['name'];
		
		$endung = substr($thename, -4);
		
		if($globvar['use_randomname'] == true)
			$name = substr(md5(uniqid(rand(), true)), 0, 12).$endung; 
		else 
			$name = $thename;
		
		$size = $_FILES['nfile']['size']; 
		$size = round($size / 1024, 2);
		
		if($size > $globvar['maxsize']) {
			echo '<p><img src="./inc/img/zeichen.png" alt=""/> Das Bild ist gr&ouml;&szlig;er als ' .$globvar['maxsize']. ' kb</p>';
		} elseif($type !== 'image/png' && $type !== 'image/x-png' && $type !== 'image/jpeg' && $type !== 'image/pjpeg' && $type !== 'image/gif') {
			echo '<p><img src="./inc/img/zeichen.png" alt=""/> Dateityp wird nicht unterst&uuml;tzt</p>';
		} else {	
			$thumbdir = './i/t/';						
			$thumb_width = $globvar['thumbwidth43'];
			$thumb_height = $globvar['thumbheight43'];
			
			move_uploaded_file($tempname, './i/' .$name);
			$imginfo = getimagesize('./i/' .$name);
			$height = $imginfo[1];
			$width = $imginfo[0];

			if($imginfo[2] == 2) {
				$src = imagecreatefromjpeg('i/' .$name);
				$typeausgabe = '.jpg';
			} elseif($imginfo[2] == 3) {
				$src = imagecreatefrompng('i/' .$name);
				$typeausgabe = '.png';
			} elseif($imginfo[2] == 1) {
				$src = imagecreatefromgif('i/' . $name);
				$typeausgabe = '.gif';
			} else {
				print '<p><img src="inc/img/zeichen.png" alt=""/> Dateityp wird nicht unterst&uuml;tzt.</p>';
				unlink($name);
				unset($src);
				include('tpl/footer.tpl.php');
				exit;				
			}
			
			/* 16/9 check und wenn kleiner als thumb resulotion kein resize */
			if($width / $height >= 16 / 9) {
				$thumb_width = $globvar['thumbwidth169'];
				$thumb_height = $globvar['thumbheight169'];
			} elseif($width <= $globvar['thumbwidth169'] && $width <= $globvar['thumbwidth169']) {
				$thumb_width = $width;
				$thumb_height = $height;
			} 
			
			if($width <= $globvar['thumbwidth43'] && $width <= $globvar['thumbwidth43']) {
				$thumb_width = $width;
				$thumb_height = $height;
			}
			
			$thumb = imagecreatetruecolor($thumb_width, $thumb_height);
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
				
			if($imginfo[2] == 2) 
				imagejpeg($thumb, 'i/t/' .$name);
			elseif($imginfo[2] == 3) 
				imagepng($thumb, 'i/t/' .$name);
			elseif($imginfo[2] == 1) 
				imagegif($thumb, 'i/t/' .$name);							
			
			chmod('i/' .$name, 0644);
			chmod('i/t/' .$name, 0644);
			
			if(dirname($_SERVER['REQUEST_URI']) == "/")
				$serverurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
			else
				$serverurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/";
			
			if($globvar['twitter'] == true) {
				$isgdlink = isgd('http://' .$serverurl. 'i/' .$name);
				if($isgdlink != "error")
					$twitterausgabe = 'http://twitter.com/home?status=' .$isgdlink. ' - ' .$thename;
				else
					$twitterausgabe = 'http://twitter.com/home/?status=http://' .$serverurl. 'i/' .$name;
			}
				
			$htmlcodeausgabe = '<img src="http://' .$serverurl. 'i/' .$name. '" alt="" />';
			$bbcodeausgabe = '[img]http://' .$serverurl. 'i/' .$name. '[/img]';
			$fullausgabe = 'http://' .$serverurl. 'i/' .$name;
			$thumbausgabe = 'http://' .$serverurl. 'i/t/' .$name;	
			$htmlcodeausgabethumb = '<a href="http://' .$serverurl. 'i/' .$name. '" target="_blank"><img src="http://' .$serverurl. 'i/t/' .$name. '" alt="" /></a>';
			$bbcodeausgabethumb = '[url=http://' .$serverurl. 'i/' .$name. '][img]http://' .$serverurl. 'i/t/' .$name. '[/img][/url]';
			$fullausgabeclick = 'http://' .$serverurl. '?s=' .$name;

			include('tpl/output.tpl.php');
		}
	}
}
include('tpl/footer.tpl.php');

function is_valid_filename($filename, $extensions=array('jpg', 'jpeg', 'gif', 'png')) {
    $regex = '/^\w\.(' .implode('|', $extensions). ')$/';
    return preg_match($regex, $filename);
}

function isgd($link) {
        $fp = fsockopen("www.is.gd", 80, $errno, $errstr, 30);
	if (!$fp) {
		echo '<p><img src="./inc/img/zeichen.png" alt=""/> Fehler beim erstellen des <a href="http://is.gd"><u>is.gd</u></a> Links. Nutze normalen Link.<br/></p>';
		return "error";
        } else {
	        $out = "GET /api.php?longurl=$link HTTP/1.1\r\n";
       		$out .= "Host: www.is.gd\r\n";
        	$out .= "Connection: Close\r\n\r\n";
        	fwrite($fp, $out);
		
		while (!feof($fp)) {
 	       		return substr(strstr(fread($fp, 300), 'http://'), 0, -5);
        	}
		fclose($fp);
	}
}
?>
