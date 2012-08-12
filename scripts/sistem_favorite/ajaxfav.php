<?php
require_once "../../mainfile.php";
require_once "functii.php";

if (iMEMBER) {
	if (isset($_GET['out']) && isnum($_GET['out']) && (($_GET['out'] >=1 && $_GET['out'] <=4) || $_GET['out']==9)) {
		//1 = image, 2=text, 3=image+text, 4=imagine mare, 9 la prin-tara
		$output = $_GET['out'];
	} else {
		$output = 1;
	}

	if (isset($_GET['a'])) {
		if ($_GET['a'] == "add") {
			if (isset($_GET['id']) && isnum($_GET['id']) && isset($_GET['t'])) {
		
				$id = $_GET['id'];
				$type = trim(htmlsafe($_GET['t']));
				$action = AddToFav($id,$type);
				$divid = "favorite".$id.$_GET['t'];
				if ($action) {
					if ($output == 1) { 
						echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=1','".$divid."');\" title=\"Sterge de la favorite\"><img src=\"http://img.weskate.ro/fav_remove.png\" alt=\"Sterge de la favorite\" style=\"border: 0pt none ; vertical-align: middle;\" /></a>";
					} elseif ($output == 2) {
						echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=2','".$divid."');\" title=\"Sterge de la favorite\">Sterge de la favorite</a>";
					} elseif ($output == 3) {
						echo "<a title=\"Sterge de la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=3','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/fav_remove.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"side lightonhoverF\">Sterge de la favorite</a>";
					} elseif ($output == 9) {
						echo "<a title=\"Nu ma mai dau aici\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=9','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/circle_delete.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"side lightonhoverF\">Nu ma mai dau aici</a>";
					} else {
						echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=4','".$divid."');\" title=\"Sterge de la favorite\"><img src=\"http://img.weskate.ro/fav_del_mare.png\" alt=\"Sterge de la favorite\" style=\"border: 0pt none ; vertical-align: middle;\" /></a>";
					}
				} else { 
					echo "Eroare.";
				}
				
			} else {
				echo "Eroare.";
			}
	
		} elseif ($_GET['a'] = "rm") {
			if (isset($_GET['id']) && isnum($_GET['id']) && isset($_GET['t'])) {
				
				$id = $_GET['id'];
				$type = trim(htmlsafe($_GET['t']));
				$action = DelFromFav($id,$type);
				$divid = "favorite".$id.$_GET['t'];
				if ($action) { 
					if ($output == 1) { 
						echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=1','".$divid."');\" title=\"Adauga la favorite\"><img src=\"http://img.weskate.ro/fav_add.png\"  style=\"border: 0pt none ; vertical-align: middle;\"  alt=\"Adauga la favorite\" /></a>";
					} elseif ($output == 2) {
						echo "<a title=\"Adauga la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=2','".$divid."');\">Adauga la favorite</a>";
					} elseif ($output == 3) {
						echo "<a title=\"Adauga la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=3','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/fav_add.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"side lightonhoverF\">Adauga la favorite</a>";
					} elseif ($output == 9) {
						echo "<a title=\"Ma dau aici\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=9','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/new.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"side lightonhoverF\">Ma dau aici</a>";

					} else {
						echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=4','".$divid."');\" title=\"Adauga la favorite\"><img src=\"http://img.weskate.ro/fav_add_mare.png\"  style=\"border: 0pt none ; vertical-align: middle;\"  alt=\"Adauga la favorite\" /></a>";
					}
				} else { 
					echo "Eroare.";
				}
				

			} else {
				echo "Eroare.";
			}
 

		} else {
			echo "Eroare.";
		}

	}
} else {
	echo "Doar pentru membri.";
}
mysql_close();

?>
