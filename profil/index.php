<?php
require_once "../mainfile.php";
$redir_subdomain = 1;
require_once SCRIPTS."sistem_prieteni/functii.php";
require_once BASEDIR."look/profile_look.php";
if (!isset($_GET['purl'])) { redirect("http://www.weskate.ro/index.php?err=InvalidName"); }

$_GET['purl'] = sqlsafe(trim($_GET['purl']));

$result = dbquery("SELECT * FROM ".DB_USERS." WHERE user_profileurl='".$_GET['purl']."' LIMIT 1");
if (dbrows($result)) {
	$user_data = dbarray($result);
} else {
	redirect("http://www.weskate.ro/index.php?err=NameNotFound&item=".$_GET['purl']);
}
$CuloarePagina = $user_data['user_culoarepagina'];

//user visibility check.
$ProfilVizibil = false;
		
if ($user_data['user_visibility'] == 1) { //daca-i public il vede oricine
	$ProfilVizibil = true; 
} else {
	if (iMEMBER) { //daca nu e public, minim trebuie sa fi conectat
		if ($user_data['user_id'] == $userdata['user_id']) { //daca e profilul lui il vede, normal.
			$ProfilVizibil = true;
		} elseif ($user_data['user_visibility'] == 2) { //daca e pentru membri il vede.
			$ProfilVizibil = true;
		} elseif ($user_data['user_visibility'] == 3 && friends($userdata['user_id'],$user_data['user_id'])) { 
			$ProfilVizibil = true; //daca e pt prieteni
		}
	}
}
//end user visibility check.
require_once SCRIPTS."header.php";

add_to_head("<link rel='stylesheet' href='http://weskate.ro/look/stilprofil.php?user_id=".$user_data['user_id']."' type='text/css' media='screen' />\n");
if (iMEMBER && $user_data['user_id'] == $userdata['user_id']) {
	add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/jscolor.js'></script>");
	add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/profil.js'></script>");
	add_to_head("<script type=\"text/javascript\" src=\"http://weskate.ro/scripts/js/slider/range.js\"></script>
<script type=\"text/javascript\" src=\"http://weskate.ro/scripts/js/slider/timer.js\"></script>
<script type=\"text/javascript\" src=\"http://weskate.ro/scripts/js/slider/slider.js\"></script>
<link type=\"text/css\" rel=\"stylesheet\" href=\"http://weskate.ro/scripts/js/slider/slide.css\" />");
}
if ($user_data['user_status'] > 0) {
	$ProfilVizibl = false; //bannned!
}


set_title("Profilul lui ".$user_data['user_name']." - we Skate");

if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {
	$culori = unserialize($user_data['user_colors']);
	echo "<input type='hidden' name='color_name' id='color_name' value='".(isset($culori['color_name']) ? $culori['color_name'] : "000000")."' onchange='showSaveTool();' />";
	echo "<input type='hidden' name='background_panel_title' id='background_panel_title' value='".(isset($culori['background_panel_title']) ? $culori['background_panel_title'] : "555555")."' onchange='showSaveTool();' />";
	echo "<input type='hidden' name='color_panel_title' id='color_panel_title' value='".(isset($culori['color_panel_title']) ? $culori['color_panel_title'] : "FFFFFF")."' onchange='showSaveTool();' />";
	echo "<input type='hidden' name='width_panel_border' id='width_panel_border' value='".(isset($culori['width_panel_border']) ? $culori['width_panel_border'] : "2")."' />";
	echo "<input type='hidden' name='color_panel_border' id='color_panel_border' onchange='colorBorder(this.value);showSaveTool();' value='".(isset($culori['color_panel_border']) ? $culori['color_panel_border'] : "CCCCCC")."' />";
	echo "<input type='hidden' name='background_panel_body' id='background_panel_body' value='".(isset($culori['background_panel_body']) ? $culori['background_panel_body'] : "F3F3F3")."' onchange='showSaveTool();' />";
	echo "<input type='hidden' name='color_menu_link' id='color_menu_link' value='".(isset($culori['color_menu_link']) ? $culori['color_menu_link'] : "000033")."' onchange='colorMenu(this.value);showSaveTool();' />";
}


echo "<table cellpadding='25' cellspacing='0' width='100%'><tr>

	<td align='left'>
	<span style='font-size:20px;text-transform:uppercase;'>Profil</span>";
	if (iMEMBER && $user_data['user_id'] == $userdata['user_id']) {
		echo "<span class='namecolor' id='namecolor'>".$user_data['user_name'];
		echo "<a href='javascript:void(0);' title='Schimba culoarea numelui de utilizator' id='namecolor_b'><img src='http://img.weskate.ro/color_picker.png' style='border: 0pt none ; vertical-align: middle;' alt='Schimba culoarea numelui de utilizator' /></a>";
		echo "</span>";
		echo "<script type='text/javascript'>
			var PickerNameColor = new jscolor.color(document.getElementById('namecolor_b'), {styleWhat:2, styleElement:'namecolor',valueElement:'color_name'});
			PickerNameColor.fromString('".$culori['color_name']."');
		      </script>";
	} else {
		echo "<span class='namecolor'>".$user_data['user_name']."</span>";
	}
	echo "</td>";

	echo "<td align='right' width='50%'><div class='MeniuRotunjit round'>
	<span style='border-left:0px;'>Profil</span>";
	if (!$user_data['user_blog'] || (iMEMBER && $userdata['user_id']==$user_data['user_id'])) {
		echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/blog' id='menuitem1'>Blog</a>";
	}
	echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/my' id='menuitem2'>My</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/favorite' id='menuitem3'>Favorite</a>";
	echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/prieteni' id='menuitem4'>Prieteni</a>";
	if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {
		echo "<a href='javascript:void(0);' title='Schimba culoarea de fundal' id='menuitems_color'><img src='http://img.weskate.ro/color_picker.png' style='border: 0pt none ; vertical-align: middle;' alt='Schimba culoarea linkurilor din acest meniu' /></a>\n";
		echo "<script type='text/javascript'>
			var pickerMenuColor = new jscolor.color(document.getElementById('menuitems_color'), {styleWhat:2,styleElement:'menuitem4',valueElement:'color_menu_link'});
			pickerMenuColor.fromString(document.getElementById('menuitem4').style.backgroundColor);
		      </script>";
	}


	echo "</div>
	</td>

	</tr></table>
	";
	if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {
		echo "<div style='display:none;border:1px solid #ddd;' class='spacer' id='saveTool'>";
		echo "<div style='background-color:#F7FFB0;padding:5px;font-size:13px;display:block;'><div class='flright' style='display:inline;'><a href='javascript:void(0)' onclick='saveColors();'>Salveaz&#259;</a> -- <a href='javascript:void(0);' onclick='setDefaults()'>Anuleaz&#259; modific&#259;rile</a></div>";
		echo "&#354;i-ai modificat profilul. Dore&#351;ti s&#259; salvezi modific&#259;rile?</div></div>";
		echo "<script type=\"text/javascript\">\n
		function setDefaults () {\n
			var areYouSure;
			areYouSure = confirm('Esti sigur ca renunti la acest design si il preferi pe cel vechi ? Odata ce ai sters o tematica nu o mai poti recupera.');

			if (areYouSure) {

				var color_name = '".(isset($culori['color_name']) ? $culori['color_name'] : "000000")."';
				var background_panel_title = '".(isset($culori['background_panel_title']) ? $culori['background_panel_title'] : "555555")."';
				var color_panel_title = '".(isset($culori['color_panel_title']) ? $culori['color_panel_title'] : "FFFFFF")."';
				var width_panel_border = '".(isset($culori['width_panel_border']) ? $culori['width_panel_border'] : "2")."'; 
				var color_panel_border = '".(isset($culori['color_panel_border']) ? $culori['color_panel_border'] : "CCCCCC")."';
				var background_panel_body = '".(isset($culori['background_panel_body']) ? $culori['background_panel_body'] : "F3F3F3")."';
				var color_menu_link = '".(isset($culori['color_menu_link']) ? $culori['color_menu_link'] : "000033")."';

				document.getElementById('color_name').value = color_name;
				document.getElementById('background_panel_title').value = background_panel_title;
				document.getElementById('color_panel_title').value = color_panel_title;
				document.getElementById('width_panel_border').value = width_panel_border;
				document.getElementById('color_panel_border').value = color_panel_border;
				document.getElementById('background_panel_body').value = background_panel_body;
				document.getElementById('color_menu_link').value = color_menu_link;

				colorMenu(color_menu_link);
				colorBorder(color_panel_border);

				document.getElementById('namecolor').style.color = '#' + color_name;
				document.getElementById('avatar-paneltop').style.backgroundColor = '#' + background_panel_title;
				document.getElementById('avatar-paneltopcolor').style.color = '#' + color_panel_title;
				document.getElementById('avatar-panel-tbl').style.borderColor = '#' + color_panel_border;
				document.getElementById('avatar-panel-tbl').style.borderWidth=width_panel_border+'px';
				document.getElementById('avatar-paneltop').style.borderWidth=(width_panel_border-1)+'px';
				document.getElementById('avatar-panelbody').style.backgroundColor='#'+background_panel_body;
				document.getElementById('menuitem4').style.color = '#' + color_menu_link;

				hideSaveTool();


			} else {
				return false;
			}

		}\n
		</script>\n";
	}

	echo "<table cellpadding='0' cellspacing='1' width='100%'>\n<tr valign='top'>\n<td width='200'>";

	//AVATAR PANEL :
	$changeCounter = 1;
	echo "<table cellpadding='0' cellspacing='0' width='200' class='profil-panel spacer' id='avatar-panel-tbl'><tr>";
	echo "<td class='profil-paneltop' id='avatar-paneltop'>";
	if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {
		echo "<div class='flright'>";
		echo "<a href='javascript:void(0);' title='Schimba culoarea de fundal a panourilor' id='panelcolor_a'><img src='http://img.weskate.ro/color_picker_bg.png' style='border: 0pt none ; vertical-align: middle;' alt='Schimba culoarea de fundal a panourilor' /></a><br />\n";
		echo "<a href='javascript:void(0);' title='Schimba culoarea textului din titlurile panourilor' id='panelcolor_b'><img src='http://img.weskate.ro/color_picker.png' style='border: 0pt none ; vertical-align: middle;' alt='Schimba culoarea textului din titlurile panourilor' /></a>\n";
		echo "</div>";
	}
	echo "<div id='avatar-paneltopcolor' class='profil-paneltop-color'>".$user_data['user_name']."</div>";
	if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {

		echo "<script type='text/javascript'>
			var pickerBgPanelTop = new jscolor.color(document.getElementById('panelcolor_a'), {styleElement:'avatar-paneltop',valueElement:'background_panel_title'})
			pickerBgPanelTop.fromString(document.getElementById('avatar-paneltop').style.backgroundColor)
		      </script>";
		echo "<script type='text/javascript'>
			var pickerPanelTop = new jscolor.color(document.getElementById('panelcolor_b'), {styleWhat:2, styleElement:'avatar-paneltopcolor',valueElement:'color_panel_title'});
			pickerPanelTop.fromString('".$culori['color_panel_title']."');
		</script>";
	}
	echo "</td></tr><tr>";
	echo "<td class='profil-panelbody' id='avatar-panelbody'>";
	echo "<div style='display:block;text-align:center;'>";

	echo showAvatar($user_data['user_avatar'],$user_data['user_email'],$user_data['user_yahoo']);

	if (!$user_data['user_hide_email'] || iADMIN) {
		echo hide_email($user_data['user_email'])."\n";
	}
	echo "</div>";
	echo "<p><strong>Înregistrat la</strong> : <br />\n";
	echo showdate("longdate", $user_data['user_joined'])."</p>\n";
	echo "<p><strong>Ultima vizită</strong> : <br />\n";
	echo ($user_data['user_lastvisit'] ? showdate("ago", $user_data['user_lastvisit']) : "niciodată")."</p>\n";
	if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {

		echo "<table cellpadding='0' cellspacing='0' width='100%'><tr><td>";

			echo "<div class='slider' id='slider-1'>";
			echo "<input class='slider-input' id='slider-input-1' name='slider-input-1' />";
			echo "</div>";
			echo "<script type='text/javascript'>
				var sliderone = new Slider(document.getElementById('slider-1'),
                   		document.getElementById('slider-input-1'));
				sliderone.setMinimum(1);
				sliderone.setMaximum(7);
				sliderone.onchange = function() {
					document.getElementById('avatar-panel-tbl').style.borderWidth=sliderone.getValue()+'px';
					document.getElementById('avatar-paneltop').style.borderWidth=(sliderone.getValue()-1)+'px';
					document.getElementById('width_panel_border').value = sliderone.getValue();
					showSaveTool();
				};
				sliderone.setValue(".(isset($culori['width_panel_border']) ? $culori['width_panel_border'] : "2")."); 
				hideSaveTool();
			</script>";
		echo "</td><td width='1%' style='white-space:nowrap;'>";

				echo "<a href='javascript:void(0);' title='Schimba culoarea de fundal' id='panelcolor_c'><img src='http://img.weskate.ro/color_picker_bg.png' style='border: 0pt none ; vertical-align: middle;' alt='Schimba culoarea de fundal' /></a>\n";
		echo "<script type='text/javascript'>
			var pickerBgPanelTop = new jscolor.color(document.getElementById('panelcolor_c'), {styleElement:'avatar-panelbody',valueElement:'background_panel_body'})
			pickerBgPanelTop.fromString(document.getElementById('avatar-panelbody').style.backgroundColor)
		      </script>";

				echo "<a href='javascript:void(0);' title='Schimba culoarea bordurii' id='panelcolor_d'><img src='http://img.weskate.ro/color_picker_br.png' style='border: 0pt none ; vertical-align: middle;' alt='Schimba culoarea bordurii' /></a>\n";
		echo "<script type='text/javascript'>
			var pickerBorder = new jscolor.color(document.getElementById('panelcolor_d'), {styleWhat:3, styleElement:'avatar-panel-tbl',valueElement:'color_panel_border'})
			pickerBorder.fromString(document.getElementById('avatar-panel-tbl').style.borderColor)
		      </script>";
		echo "</td></tr></table>";
	}
	echo "</td></tr></table>";

	// END AVATAR PANEL
	if (iMEMBER && $userdata['user_id'] != $user_data['user_id']) {
		$changeCounter ++;
		openProfilePanel("Opțiuni");
		if ($user_data['user_web']) {
			$urlprefix = !strstr($user_data['user_web'], "http://") ? "http://" : "";
			echo "<a href='".$urlprefix.$user_data['user_web']."' title='Viziteaz&#259; : ".$user_data['user_web']."' class='optnav' target='_blank'><span style='background-repeat:no-repeat;background-image:url(http://img.weskate.ro/link.png);padding-left:20px;'>Viziteaz&#259; pagina web</span></a>";
		}


		$relstatus = friendStatus($user_data['user_id'],$userdata['user_id']);
		if ($relstatus) {
			$relstatus = $relstatus - 1;
			if ($relstatus==1) {
				echo "<div id=\"friends\" style=\"display:block;\"><a href=\"javascript:void(0);\" onclick=\"ajaxpage('ajaxfriend.php?delete=".$user_data['user_id']."&amp;key=".$_SESSION['user_key']."','friends');\" title=\"&#350;terge prieten\" class=\"optnav\"><span style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend_remove.png);padding-left:20px;\">&#350;terge prieten</span></a></div>";
			} elseif ($relstatus==0) {
				$iAnswer = dbrows(dbquery("SELECT rel_id FROM ".DB_FRIENDS." WHERE friend_one='".$user_data['user_id']."' AND friend_two='".$userdata['user_id']."' LIMIT 1"));
				if (!$iAnswer) {
					echo "<div style='display:block;padding:4px;'><span style='background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend.png);padding-left:20px;'>A&#351;teptare r&#259;spuns...</span></div>";
				} else {
					echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/prieteni' title='Accept&#259; sau refuz&#259; cererea de prietenie de la ".$user_data['user_name']."' class='optnav'><span style='background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend.png);padding-left:20px;'>R&#259;spunde la cerere</span></a>";
				}
			} elseif ($relstatus==2) {
				echo "<div style='display:block;padding:4px;'><span style='background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend.png);padding-left:20px;'>Prietenie refuzat&#259;...</span></div>\n";
			}
		} else {
				echo "<div id=\"friends\" style=\"display:block;\"><a href=\"javascript:void(0);\" onclick=\"ajaxpage('ajaxfriend.php?add=".$user_data['user_id']."&amp;key=".$_SESSION['user_key']."','friends')\" title=\"Adaug&#259; la lista de prieteni\" class=\"optnav\"><span style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend_add.png);padding-left:20px;\">Adaug&#259; la prieteni</span></a></div>";
		}


		require_once SCRIPTS."sistem_favorite/functii.php";
		if (LaFavorite($user_data['user_id'],"U")) {
			$favurl = "ajaxfav.php?a=rm&amp;id=".$user_data['user_id']."&amp;t=U";		
			$favimg = "http://img.weskate.ro/fav_remove.png";
			$favtxt = "&#350;terge de la favorite";
		} else {
			$favurl = "ajaxfav.php?a=add&amp;id=".$user_data['user_id']."&amp;t=U";		
			$favimg = "http://img.weskate.ro/fav_add.png";
			$favtxt = "Adaug&#259; la favorite";
		}
		echo "<div id='favorite' style='display:block;'>";
		echo "<a href=\"javascript:void(0);\" onclick=\"ajaxpage('".$favurl."','favorite');\" class=\"optnav\"><span style=\"background-repeat:no-repeat;background-image:url(".$favimg.");padding-left:20px;\"> ".$favtxt."</span></a>";
		echo "</div>";

		closeProfilePanel();
	}



	echo "</td><td>";
	if ($ProfilVizibil) { //daca $ProfilVizibil == true
		$result = dbquery("SELECT panel_title,panel_id,panel_content,panel_template,panel_order FROM ".DB_USER_PANELS." WHERE panel_user=".$user_data['user_id']." ORDER BY panel_order ASC");
		$lastPanelOrder = 0 ;
		while ($data=dbarray($result)) {
			$edit = (iMEMBER && $userdata['user_id']==$user_data['user_id'] ? true : false);
			$editlink = (iMEMBER && $userdata['user_id']==$user_data['user_id'] ? "<a href='javascript:editpanel(".$data['panel_id'].");' class='flright'><img src='http://img.weskate.ro/edit_mare.png' alt='edit' style='border:0px;'/></a><a class='flright' href='javascript:deletepanel(".$data['panel_id'].",\"".$_SESSION['user_key']."\");'><img src='http://img.weskate.ro/del_mare.png' alt='edit' style='border:0px;'/></a>" : "");

			if (strpos($setari['profile_panels'],";")) {
				$panels = explode(";",$setari['profile_panels']);
				if (in_array($data['panel_template'],$panels) && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")) {
					if (file_exists(BASEDIR."panels/profile/".$data['panel_template'].".js")) {
						add_to_head("<script type='text/javascript' src='http://weskate.ro/panels/profile/".$data['panel_template'].".js'></script>");
					}
					$step = "render";
					openProfilePanel($editlink.$data['panel_title']);
					if ($edit) echo "<div id='panel_c".$data['panel_id']."'>";
					require BASEDIR."panels/profile/".$data['panel_template'].".php";
					if ($edit) echo "</div>";
					closeProfilePanel();
				}
			} else if ($data['panel_template'] == $setari['profile_panels'] && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")) {
				if (file_exists(BASEDIR."panels/profile/".$data['panel_template'].".js")) {
					add_to_head("<script type='text/javascript' src='http://weskate.ro/panels/profile/".$data['panel_template'].".js'></script>");
				}
				echo "<div id='panelbig".$data['panel_id']."'>";
				if (iMEMBER && $user_data['user_id'] == $userdata['user_id']) {
					echo "<div class='spacer' id='newpaneldiv".$data['panel_order']."'><a href='javascript:newPanel(".$data['panel_order'].");' class='header-link-m acasa smallround' style='display:inline-block;padding:3px 3px 3px 20px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3px 50%;'>Adaugă un panou nou aici</a></div>";
					$lastPanelOrder = $data['panel_order'];
				}
				$step = "render";
				openProfilePanel($editlink."<div id='paneltitle".$data['panel_id']."'>".$data['panel_title']."</div>");
				if ($edit) echo "<div id='panel_c".$data['panel_id']."'>";
				require BASEDIR."panels/profile/".$data['panel_template'].".php";
				if ($edit) echo "</div>";
				closeProfilePanel();
				echo "</div>";
			}
		}
		if (iMEMBER && $user_data['user_id'] == $userdata['user_id']) {
			echo "<div id='newpaneldiv".($lastPanelOrder+1)."'><a href='javascript:newPanel(".($lastPanelOrder+1).");' class='header-link-m acasa smallround' style='display:inline-block;padding:3px 3px 3px 20px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3px 50%;'>Adaugă un panou nou aici</a></div>";
		}
		if (!dbrows($result)) {
			echo "<div style='padding:20px;font-size:16px;font-weight:bold;margin:0px auto 0px auto;'>Profilul lui ".$user_data['user_name']." este gol.</div>";
		}
	} else { //pentru $ProfilVizibil == false :

		echo "<div style='width:360px;margin:3px auto 3px auto;'>";
		if ($user_data['user_visibility'] == 2) {
			echo "<span style='font-weight:bold;'>Profilul lui ".$user_data['user_name']." nu este vizibil pentru vizitatori.</span>";
			echo "<p>Acest profil este vizibil doar utilizatorilor inregistrati. Daca ai cont, conecteaza-te si vei putea vedea profilul imediat, daca nu, inregistrarea este usoara si rapida.</p>";
		} elseif ($user_data['user_visibility'] == 3) {
			echo "<span style='font-weight:bold;'>Profilul lui ".$user_data['user_name']." este vizibil doar pentru prieteni.</span>";
			echo "<p>Doar prietenii lui ".$user_data['user_name']." pot vedea acest profil. Daca doresti sa-l vezi, ca membru, poti trimite o cerere de prietenie.</p>";
		} elseif ($user_data['user_visibility'] == 4) {
			echo "<span style='font-weight:bold;'>Profilul lui ".$user_data['user_name']." este privat.</span>";
			echo "<p>Nimeni nu poate vedea acest profil in afara de proprietarul acestuia.</p>";
		}
		echo "</div>";


	} // end daca $ProfilVizibil == false;
	echo "</td></tr></table>";

closetable();

require_once SCRIPTS."footer.php";
?>
