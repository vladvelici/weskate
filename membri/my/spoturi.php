<?php
require_once "../../mainfile.php";
$CuloarePagina = "mov";
if (!iMEMBER) { redirect("../conectare.php?redirto=".urlencode(PAGE_REQUEST)); }
require_once SCRIPTS."header.php";

if (!isset($_GET['key']) || $_GET['key'] != $_SESSION['user_key']) { redirect("/index.php"); }
$key = "?key=".$_SESSION['user_key'];

add_to_head("<link rel='stylesheet' href='http://weskate.ro/membri/my/my.css' type='text/css' media='screen' />");

if (isset($_GET['edit']) && isnum($_GET['edit']) && checkMyAccess("S",$_GET['edit'])) {
	$id = $_GET['edit'];
	if (isset($_GET['delete'])) {
		$result = dbquery("SELECT s.spot_title,s.spot_thumb,c.city_name FROM ".DB_SPOT_ALBUMS." s
				LEFT JOIN ".DB_CITIES." c ON s.spot_city=c.city_id
				WHERE spot_id='$id'");
		$data = dbarray($result);
		$check = md5($data['spot_title'].$data['spot_thumb']);
		if ($check!=$_GET['delete']) {
			redirect(PAGE_SELF."?edit=$id&msg=1");
		} else {
			//delete photo files
			$folder = IMAGES."spoturi/".urltext($data['city_name'])."/";
			$result = dbquery("SELECT photo_file FROM ".DB_SPOT_PHOTOS." WHERE photo_spot='$id'");
			while ($data=dbarray($result)) {
				@unlink($folder.$data['photo_file']);
				@unlink($folder."thumbs/".$data['photo_file']);
			}
			//delete photos form datebase
			$result=dbquery("DELETE FROM ".DB_SPOT_PHOTOS." WHERE photo_spot='$id'");
			//delete spot from db
			$result=dbquery("DELETE FROM ".DB_SPOT_ALBUMS." WHERE spot_id='$id'");
			//delete "s" comments and ratings
			cleanup("S",$id);
			//redirect to "add spots" page and show the "deleted successfully" message:
			redirect(PAGE_SELF."?msg=2");
		}
	}
} else {
	$id = false;
}
$save = false;
if (isset($_GET['poze']) || isset($_POST['poze'])) {
	$step = 2;
} else if (isset($_GET['incarca']) || isset($_POST['incarca'])) {
	$step = 3;
} else {
	$step = 1;
}

//save spot general info
if (isset($_POST['savespot'])) {
	$error = false;
	if (!isset($_POST['title']) || strlen(trim($_POST['title']))<=3) {
		$error = 1;
	}
	if (!isset($_POST['city']) || !isnum($_POST['city']) || !dbcount("(city_id)",DB_CITIES,"city_id='".$_POST['city']."'")) {
		$error = 2;
	}
	if (!isset($_POST['description']) || strlen(trim($_POST['description']))<=3) {
		$error = 3;
	}
	if (!isset($_POST['adress']) || strlen(trim($_POST['adress']))<=3) {
		$error = 4;
	}
	if (!$error) {
		if (isset($_POST['coords']) && isnum(str_replace(array(",",".","-"),"",$_POST['coords']))) {
			$coords = $_POST['coords'];
		} else {
			$coords = 0;
		}
		if (isset($_POST['zoomlvl']) && isnum($_POST['zoomlvl']) && $_POST['zoomlvl'] > 16) {
			$zoomlvl = $_POST['zoomlvl'];
		} else {
			$zoomlvl = 16;
		}
		$title = trim(htmlsafe($_POST['title']));
		$city = $_POST['city'];
		$adress = trim(htmlsafe($_POST['adress']));
		$description = trim(htmlsafe($_POST['description']));
		$coords = ($coords ? $coords.",".$zoomlvl : 0);

		if ($id) {
			$result = dbquery("UPDATE ".DB_SPOT_ALBUMS." SET spot_title='$title', spot_city='$city', spot_adress='$adress', spot_description='$description', spot_coords='$coords' WHERE spot_id='$id'");
			if ($result) { 
				$saved = true;
				$cityname = dbarray(dbquery("SELECT city_name FROM ".DB_CITIES." WHERE city_id=$city"));
				$cityname = urltext($cityname['city_name']);
				indexItem($id,"S",killRoChars($title),killRoChars($adress.". ".$description),keywordize($title),$city,0,0,"/prin-tara/$cityname.$city/".urltext($title).".$id");
				echo "salvat";
			} else {
				$saved = false;
				echo "eroare query";
			}
		} else {
			$timestamp = time();
			$result = dbquery("INSERT INTO ".DB_SPOT_ALBUMS." (spot_title,spot_city,spot_adress,spot_description,spot_coords,spot_datestamp,spot_user) VALUES ('$title', '$city', '$adress', '$description', '$coords', '$timestamp', '".$userdata['user_id']."')");
			if ($result) {
				$getId = dbarray(dbquery("SELECT spot_id FROM ".DB_SPOT_ALBUMS." WHERE spot_datestamp = '$timestamp' AND spot_title='$title'"));
				$id = $getId['spot_id'];
				$cityname = dbarray(dbquery("SELECT city_name FROM ".DB_CITIES." WHERE city_id=$city"));
				$cityname = urltext($cityname['city_name']);
				indexItem($id,"S",killRoChars($title),killRoChars($adress.". ".$description),keywordize($title),$city,0,$timestamp,"/prin-tara/$cityname.$city/".urltext($title).".$id");
				$saved = true;
			} else {
				$saved = false;
				echo "eroere query";
			}
		}
	} else {
		echo "eroare big".$error;
	}
}

//upload photo to spot


$spoturi = dbcount("(spot_id)",DB_SPOT_ALBUMS,"spot_user='".$userdata['user_id']."'");

echo "<table cellpadding='4' cellspacing='0' width='100%'><tr>";
if ($spoturi) echo "<td id='myContainerB'><a href='javascript:toggleMyContainer();' id='myContainerBa'>&darr;</a></td>";
echo "<td style='border-bottom:2px solid #999;'><span class='capmain_color' style='font-size:30px;padding-bottom:7px;'>";

if ($id) {
	$result = dbquery("SELECT s.*,c.city_name FROM ".DB_SPOT_ALBUMS." s
			LEFT JOIN ".DB_CITIES." c ON s.spot_city=c.city_id
			WHERE spot_id='$id'");
	$data = dbarray($result);
	if ($data['spot_coords']) {
		list($lat,$lng,$zoom) = explode(",",$data['spot_coords']);
	} else {
		$lat = false; $lng = false; $zoom = false;
	}
	$title = $data['spot_title'];
	$adress = $data['spot_adress'];
	$description = $data['spot_description'];
	$city = $data['spot_city'];
	$city_txt = $data['city_name'];
	$thumb = $data['spot_thumb'];
	echo $title;
} else {
	echo "Adauga un loc de skate";
}

echo "</span></td>
<td align='right' class='my-navigationtd' style='border-bottom:2px solid #999;'>
<a href='/membri/my' class='my-albastru'>tot</a> <strong>-</strong> 
<a href='stiri.php$key' class='my-oranj'>&#351;tiri</a> <strong>-</strong> 
<a href='articole.php$key' class='my-galben'>articole</a> <strong>-</strong> 
<span class='my-mov'>locuri de skate</span> <strong>-</strong> 
<a href='video.php$key' class='my-rosu'>video</a></td>
</tr></table>";

//myContainer panel
if ($spoturi) {
	add_to_head("<script type='text/javascript' src='spoturi.js'></script>");
	echo "<div id='myContainer'>";
	echo "<div class='flright' style='font-size:13px;display:inline;padding:5px;'>Ai adaugat <strong>$spoturi</strong> loc".($spoturi > 1 ?  "uri" : "")." de skate pana acum.</div>";
	echo "<a href='".PAGE_SELF."$key' style='font-size:13px;display:inline-block;padding:5px 5px 5px 24px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3% 50%;' class='spoturi my-navlink'>Loc de skate nou</a>";
	echo "<div class='vizibil' id='mySpots'>";

	$result = dbquery("SELECT s.spot_id,s.spot_title,s.spot_thumb,s.spot_city,c.city_name FROM ".DB_SPOT_ALBUMS." s
			LEFT JOIN ".DB_CITIES." c ON c.city_id = s.spot_city
			WHERE spot_user='".$userdata['user_id']."' ORDER BY spot_title ASC, spot_datestamp DESC");
	
	while ($data=dbarray($result)) {
		echo "<div><a href='".PAGE_SELF."$key&amp;edit=".$data['spot_id']."' class='spoturi' title='Editeaza: ".$data['spot_title']."'".($id == $data['spot_id'] ? " style='border-color:#555;'" : "")."><strong>".trimlink($data['spot_title'],32)."</strong>".($data['spot_thumb'] ? "<img src='http://img.weskate.ro/spoturi/".urltext($data['city_name'])."/thumbs/".$data['spot_thumb']."' alt='".$data['spot_title']."' /><br />" : "<span>fara pictograma</span><br />")."<small>in ".$data['city_name']."</small></a></div>";
	}

	echo "</div>";
	echo "</div>";
}

if (isset($_GET['msg'])) {
	if ($_GET['msg'] == 1) {
		echo "<div class='notered'>Stergerea locului de skate a esuat.</div>";
	} else {
		echo "<div class='notegreen'>Locul de skate a fost sters cu succes.</div>";
	}
}

if ($step == 2 && $id) {

	echo "<div class='flright' style='display:block;width:170px;'>";
	if ($thumb) {
	echo "<div style='text-align:center;'><img src='http://img.weskate.ro/spoturi/".urltext($city_txt)."/thumbs/".$thumb."' style='margin:7px;' alt='pictograma' /></div>";
	}
	echo "<a href='spoturi.php$key&amp;edit=$id' class='my-navlink spoturi'><span>&rsaquo;</span> Info generale</a>";
	echo "<span class='lightonhoverF' style='display:block;font-size:14px;font-weight:bold;padding:4px;'>&rsaquo; Poze</span>";
	echo "<a href='spoturi.php$key&amp;edit=$id&amp;incarca' class='my-navlink spoturi'><span>&rsaquo;</span> &Icirc;ncarca o poza</a>";
	echo "<a href='spoturi.php$key&amp;edit=$id&amp;delete=".md5($title.$thumb)."' onclick='return confirm(\"Esti sigur ca vrei sa stergi acest loc de skate?\\n Nu va mai putea fi recuperat.\")' class='my-navlink spoturi'><span>&rsaquo;</span> Sterge</a>";
	echo "</div>";

	$myPhotos = dbcount("(photo_id)",DB_SPOT_PHOTOS,"photo_spot='$id' AND photo_user='".$userdata['user_id']."'");

	if ($myPhotos) {
		echo "<div style='margin-top:7px;'>";
		$result = dbquery("SELECT * FROM ".DB_SPOT_PHOTOS." WHERE photo_spot='$id' AND photo_user='".$userdata['user_id']."' ORDER BY photo_datestamp DESC");
		echo "<span style='font-size:16px;display:block;'>Pozele adaugate de mine:</span>";
		echo "<table cellpadding='4' cellspacing='3' align='center'><tr>";
		$i=1;
		require_once SCRIPTS."photo_functions_include.php";
		while ($data = dbarray($result)) {
			$photo_id=$data['photo_id'];
			if ($i%5==0 && $i!=$myPhotos) { echo "</tr><tr>"; }
			echo "<td style='text-align:center;' width='130' id='photo$photo_id'>";
			echo "<div style='padding:4px;position:absolute;display:none;width:160px;white-space:nowrap;border:2px solid #ece;background-color:#eee;' id='changeName$photo_id'>
			<input type='text' value='".$data['photo_title']."' style='width:140px;' name='c_title' id='c_title$photo_id' /><br />
			<input type='button' value='Salveaza' onclick='savePhotoTitle($photo_id,\"".$_SESSION['user_key']."\");' />
			[<a href='javascript:togglePhotoTitle($photo_id);'>inchide</a>]  <span id='changeNameS$photo_id'></span>
			</div>";
			echo "<a href='javascript:togglePhotoTitle($photo_id);' title='Schimba titlul pozei' id='photoTitle$photo_id'>";
			echo ($data['photo_title'] ? "<strong>".$data['photo_title']."</strong>" : "<em>fara titlu</em>");
			echo "</a><br />";
			if ($data['photo_file'] && file_exists(IMAGES."spoturi/".urltext($city_txt)."/thumbs/".$data['photo_file'])) {
				echo "<img src='http://img.weskate.ro/spoturi/".urltext($city_txt)."/thumbs/".$data['photo_file']."' style='border:2px solid #ccc;' alt='".($data['photo_title'] ? $data['photo_title'] : $title)."' /><br />";
			} elseif (file_exists(IMAGES."spoturi/".urltext($city_txt)."/".$data['photo_file'])) {
				$imagefile = @getimagesize(IMAGES."spoturi/".urltext($city_txt)."/".$data['photo_file']);
				createFixedThumb($imagefile[2], IMAGES."spoturi/".urltext($city_txt)."/", IMAGES."spoturi/".urltext($city_txt)."/thumbs/".$data['photo_file'], 100, 100);
			} else {
				$result2 = dbquery("DELETE FROM ".DB_SPOT_PHOTOS." WHERE photo_id='".$data['photo_id']."'");
			}
			echo "<a href='javascript:deletePhoto($photo_id,\"".$_SESSION['user_key']."\");' class='side lightonhoverF' style='padding:3px;'><img src='http://img.weskate.ro/uncheck.gif' style='border:0pt none;vertical-align:middle;' alt='sterge poza'/>Sterge</a>";
			echo "</td>";
			$i++;
		}
		echo "</tr></table>";
		echo "</div>";
	} else {
		echo "<span style='font-weight:bold;font-size:16px;display:block;text-align:center;padding:20px;'>Nu ai adaugat nici o poza din acest loc inca.</span>";
	}
} else if ($step == 3 && $id) {
	if (isset($_POST['savephoto'])) {
		if (isset($_FILES['photo_file']) && is_uploaded_file($_FILES['photo_file']['tmp_name'])) {
			$pic_types = array(".gif",".jpg",".jpeg",".png");
			$photo_file = $_FILES['photo_file'];
			$spot_ext = strtolower(strrchr($photo_file['name'],"."));
			if (!preg_match("/^[-0-9A-Z_\.\[\]\s]+$/i", $photo_file['name'])) {
				echo "<div class='notered'>Nume fisier imagine invalid. Redenumeste imaginea la un nume simplu, de genul \"scari.jpg\".</div>";
				$error = true;
			} elseif ($photo_file['size'] > $setari['photo_max_b']) {
				echo "<div class='notered'>Fisierul imaginii este prea mare (".parsebytesize($photo_file['size'])."). Limita este de ".parsebytesize($setari['photo_max_b'])."</div>";
				$error = true;
			} elseif (!in_array($spot_ext, $pic_types)) {
				echo "<div class='notered'>Tip fisier invalid. Incercati un fisier JPG, JPEG, PNG sau GIF.</div>";
				$error = true;
			} else {
				$folder = IMAGES."spoturi/".urltext($city_txt)."/";
				require_once SCRIPTS."photo_functions_include.php";
				@unlink($folder."temp".$spot_ext);
				move_uploaded_file($photo_file['tmp_name'], $folder."temp".$spot_ext);
				chmod($folder."temp".$spot_ext, 0644);
				$imagefile = @getimagesize($folder."temp".$spot_ext);
				$spot_filename = image_exists($folder, md5($photo_file['name']).$spot_ext);
				createFixedThumb($imagefile[2], $folder."temp".$spot_ext, $folder."thumbs/".$spot_filename, 100, 100);
				createthumbnail($imagefile[2], $folder."temp".$spot_ext, $folder.$spot_filename, 1024, 768, true);
				@unlink($folder."temp".$spot_ext);
				$error = false;
			}
			if (!$error) {
				if ($_POST['photo_title']) {
					$titlu = trim(htmlsafe($_POST['photo_title']));
				} else {
					$titlu = "";
				}
				$result = dbquery("INSERT INTO ".DB_SPOT_PHOTOS." (photo_spot, photo_title, photo_file, photo_user, photo_datestamp) VALUES ('$id', '$titlu', '$spot_filename', '".$userdata['user_id']."', '".time()."')");
				if ($result) {
					echo "<div class='notegreen'>Imagine salvata cu succes.";
					if (!$thumb || !file_exists($folder."thumbs/$thumb")) {
						$result2 = dbquery("UPDATE ".DB_SPOT_ALBUMS." SET spot_thumb='$spot_filename' WHERE spot_id='$id'");
						echo " Pictograma locului de skate salvata.";
					}
					echo "</div>";
				} else {
					echo "<div class='notered'>Eroare la adaugarea pozei in baza de date.</div>";
					@unlink($folder."thumbs/".$spot_filename);
					@unlink($folder.$spot_filename);
				}
			}
		} else {
			echo "<div class='notered'>Eroare la incarcarea fisierului. Incearca din nou.</div>";
		}
	}

	echo "<div class='flright' style='display:block;width:170px;'>";
	if ($thumb) {
	echo "<div style='text-align:center;'><img src='http://img.weskate.ro/spoturi/".urltext($city_txt)."/thumbs/".$thumb."' style='margin:7px;' alt='pictograma' /></div>";
	}
	echo "<a href='spoturi.php$key&amp;edit=$id' class='my-navlink spoturi'><span>&rsaquo;</span> Info generale</a>";
	echo "<a href='spoturi.php$key&amp;edit=$id&amp;poze' class='my-navlink spoturi'><span>&rsaquo;</span> Poze</a>";
	echo "<span class='lightonhoverF' style='display:block;font-size:14px;font-weight:bold;padding:4px;'>&rsaquo; &Icirc;ncarca o poza</span>";
	echo "<a href='spoturi.php$key&amp;edit=$id&amp;delete=".md5($title.$thumb)."' onclick='return confirm(\"Esti sigur ca vrei sa stergi acest loc de skate?\\n Nu va mai putea fi recuperat.\")' class='my-navlink spoturi'><span>&rsaquo;</span> Sterge</a>";
	echo "</div>";

	echo "<form name='inputform' method='post' action='spoturi.php$key&amp;edit=$id&amp;incarca'  enctype='multipart/form-data'>\n";
	echo "<span style='font-size:16px;font-weight:bold;padding:7px;display:block;'>&Icirc;ncarca o poza</span>";
	echo "<strong>Titlu :</strong><br />";
	echo "<input name='photo_title' type='text' style='width:200px;' /><br />";
	echo "<strong>* Fisier :</strong><br />";
	echo "<input name='photo_file' type='file' style='width:200px;' /><br /><br />";
	echo "<input type='submit' name='savephoto' value='&Icirc;ncarca' />";
	echo "<br /><br /><strong>* Obligatoriu. Fisierul trebuie sa fie PNG, GIF, JPG sau JPEG, avand maxim ".parsebytesize($setari['photo_max_b']).".</strong>";
	echo "</form>";
} else {
	if ($id) {
		echo "<div class='flright' style='display:block;width:170px;'>";
		if ($thumb) {
		echo "<div style='text-align:center;'><img src='http://img.weskate.ro/spoturi/".urltext($city_txt)."/thumbs/".$thumb."' style='margin:7px;' alt='pictograma' /></div>";
		}
		echo "<span class='lightonhoverF' style='display:block;font-size:14px;font-weight:bold;padding:4px;'>&rsaquo; Info generale</span>";
		echo "<a href='spoturi.php$key&amp;edit=$id&amp;poze' class='my-navlink spoturi'><span>&rsaquo;</span> Poze</a>";
		echo "<a href='spoturi.php$key&amp;edit=$id&amp;incarca' class='my-navlink spoturi'><span>&rsaquo;</span> &Icirc;ncarca o poza</a>";
		echo "<a href='spoturi.php$key&amp;edit=$id&amp;delete=".md5($title.$thumb)."' onclick='return confirm(\"Esti sigur ca vrei sa stergi acest loc de skate?\\n Nu va mai putea fi recuperat.\")' class='my-navlink spoturi'><span>&rsaquo;</span> Sterge</a>";

		echo "</div>";
	} else {
		$lat=false; $lng=false; $zoom=false;
		$title = "";
		$adress = "";
		$description = "";
		$city = (isset($_GET['oras']) && isnum($_GET['oras']) ? $_GET['oras'] : 0);
		echo "<div class='flright' style='display:block;width:170px;'>";
		echo "<span style='display:block;font-size:14px;font-weight:bold;padding:4px;' class='lightonhover'>&rsaquo; Info generale</span>";
		echo "<a href='javascript:void(0);' onclick='alert(\"Pozele pot fi incarcate dupa ce au fost salvate informatiile generale.\")' class='my-navlink spoturi'><span>x</span> Poze</a>";
		echo "<a href='javascript:void(0);' onclick='alert(\"Pozele pot fi incarcate dupa ce au fost salvate informatiile generale.\")' class='my-navlink spoturi'><span>x</span> &Icirc;ncarca o poza</a>";
		echo "</div>";
	}
	add_to_head("<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false&amp;key=ABQIAAAABfEHkWtkodBRXBgue5mirRRzWlYxbL7sM4tcK2PJevmgd4TgEBRa7mPh1VFZEQZqVV7EW82FU8uq5g'></script>\n
<script type='text/javascript' src='gmap.js'></script>");

	echo "<form method='post' action='".PAGE_REQUEST."$key'>";
	echo "<input name='coords' id='coords' type='hidden'".($lat ? " value='$lat,$lng'" : "")." />";
	echo "<input name='zoomlvl' id='zoomlvl' type='hidden'".($zoom ? " value='$zoom'" : "")." />";
	if (!$id) echo "<input name='incarca' type='hidden' value='true' />";
	echo "<table cellpadding='5' cellspacing='3'>";
	echo "<tr>";
	echo "<td>";
	echo "<span style='font-weight:bold;font-size:20px;'>Denumirea locului de skate:</span><br />";
	echo "<input name='title' type='text' value='$title' class='my-textboxBig'/>";
	echo "</td>";

	$result = dbquery("SELECT city_id,city_name FROM ".DB_CITIES." WHERE city_type=0 ORDER BY city_name");
	$city_opts = ""; $sel = "";
	if (dbrows($result)) {
		while ($data = dbarray($result)) {
			$city_opts .= "<option value='".$data['city_id']."'".($data['city_id'] == $city ? " selected='selected'" : "").">".$data['city_name']."</option>\n";
		}
	}	
	echo "<td>";
	echo "<span style='font-weight:bold;font-size:20px;'>Oras:</span><br />";
	echo "<select name='city' class='my-textboxBig'>$city_opts</select>";
	echo "</td>";
	echo "</tr><tr>";
	echo "<td>";
	echo "<span style='font-weight:bold;font-size:20px;'>Descriere loc de skate :</span><br />";
	echo "<textarea name='description' cols='48' rows='4' class='my-textboxBig'>$description</textarea>";
	echo "</td>";
	echo "<td>";
	echo "<span style='font-weight:bold;font-size:20px;'>Locatie, adresa :</span><br />";
	echo "<textarea name='adress' cols='48' rows='4' class='my-textboxBig'>$adress</textarea>";
	echo "</td>";
	echo "</tr><tr>";
	echo "<td colspan='2'>";

	echo "<span style='float:right;'>";
	echo "<a href='javascript:toggleCM();' class='spoturi header-link-m' style='display:inline-block;padding:5px;' title='Daca nu gasesti locatia corecta, mai bine nu alege nimic' id='darklayer_b'>Nu aleg locatia pe harta</a>";
	echo "<a href='javascript:void(0);' onclick='return toggleMapHelp(document.getElementById(\"maphelpdiv\"));' class='spoturi header-link-m' style='display:inline-block;padding:5px;margin-right:0px;' title='Ajutor harta'>?</a>";
	echo "</span>";

	echo "<span style='font-weight:bold;font-size:20px;'>Alege locatia pe harta</span><br />";

	echo "<div style='position:relative;width:100%;height:400px;border:1px solid #aaa;'>";

	echo "<div style='display:none;position:absolute;right:3px;top:3px;width:300px;overflow:auto;height:auto;border:2px solid #999;background-color:#ccc;background-image:url(http://t.img.weskate.ro/degradeu.png);background-repeat:repeat-x;z-index:3;' id='maphelpdiv'>
	<div style='display:block;color:#fff;font-weight:bold;border-bottom:2px solid #e9e;padding:4px;' class='bara spacer'><a href='javascript:void(0);' onclick='return toggleMapHelp(document.getElementById(\"maphelpdiv\"));' style='padding:3px;' class='lightonhoverD flright'>x</a>Ajutor harta</div>
	<div style='padding:5px;'>Pozitioneaza locul de skate tragand marker-ul de pe harta la locatia dorita.<br /><br />Puteti plasa marker-ul si apasand click dreapta. Acest lucru este util cand ati ajuns la un nivel mare de zoom si nu mai puteti trage marker-ul fara sa pierdeti imaginea obtinuta.<br /><br />Nivelul de zoom este important, el fiind salvat impreuna cu coordonatele marker-ului pentru a genera ulterior harta care este afisata tuturor vizitatorilor, asa ca alege cel mai bun nivel de zoom.<br /><br /><em><strong>Nota:</strong> Nu uitati ca aceasta harta trebuie sa ajute skaterii interesati sa ajunga la locuri de skate din alte orase, asa ca fiti atenti cand alegeti o locatie!</em></div></div>";

	echo "<div style='position:absolute;width:100%;height:100%;background-color:#000;opacity:0.8;filter:alpha(opacity=80);display:none;z-index:2;' id='darklayer'></div>";

	echo "<div id='locationMap' style='width:100%;height:400px;'></div>";

	echo "</div>";
	echo "</td>";
	echo "</tr>";
	echo "<tr><td colspan='2'><input type='submit' name='savespot' value='Salvează ".($id ? "modificările" : "și apoi încarcă poze")."' />".($id ? "&nbsp;&nbsp;<a href='".PAGE_SELF."$key'>Renunță</a>" : "")."</td></tr>";
	echo "</table>";

	echo "</form>";
}

echo "<div style='clear:both;'></div>";
require_once SCRIPTS."footer.php";
?>
