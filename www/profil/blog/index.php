<?php
require_once "../../mainfile.php";
$redir_subdomain = 1;

if (!isset($_GET['user'])) redirect("http://weskate.ro/index.php");

$user = htmlsafe(trim($_GET['user']));
$result = dbquery("SELECT user_blog,user_id,user_name,user_profileurl,user_culoarepagina FROM ".DB_USERS." WHERE user_profileurl='".$user."' LIMIT 1");
if (dbrows($result)) { $user_data = dbarray($result); } else { redirect("http://www.weskate.ro/blog/index.php?err=user_not_found&u=$user"); }
$VizProfil = $user_data['user_id'];
$CuloarePagina = $user_data['user_culoarepagina'];

require_once SCRIPTS."header.php";
require_once SCRIPTS."sistem_prieteni/functii.php";
add_to_head("<link rel='stylesheet' href='http://weskate.ro/look/stilprofil.php?user_id=".$user_data['user_id']."' type='text/css' media='screen' />\n");

	echo "<table cellpadding='25' cellspacing='0' width='100%'><tr>

	<td align='left'>
	<span style='font-size:20px;text-transform:uppercase;'>Profil</span><span class='namecolor' id='username_color'>".$user_data['user_name']."</span>
	</td>

	<td align='right' width='50%'><div class='round MeniuRotunjit'>
	<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."' style='border-left:0px;' onclick='return CheckReturnToBlog();'>Profil</a><span>Blog</span><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/my' onclick='return CheckReturnToBlog();'>My</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/favorite' onclick='return CheckReturnToBlog();'>Favorite</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/prieteni' onclick='return CheckReturnToBlog();'>Prieteni</a>";
	echo "</div>
	</td>

	</tr></table>
	";
if (isnum($user_data['user_blog']) && $user_data['user_blog']==0) {
	echo "<div style='font-weight:bold;font-size:20px;text-align:center;'>".$user_data['user_name']." are blogul dezactivat.</div>";	
} else if ($user_data['user_blog']!=1) {
	echo "<div style='font-weight:bold;font-size:20px;text-align:center;'>".$user_data['user_name']." folosește un blog extern:<br /><br /><a href='".$user_data['user_blog']."'>".$user_data['user_blog']."</a></div>";
} else if (isnum($userdata['user_blog'])) {

	if (isset($_GET['id']) && isnum($_GET['id'])) {		//read a post

	$row = dbcount("(blog_id)",DB_BLOG,"blog_id='".$_GET['id']."' AND blog_draft='0'");
	if ($row) {

		$result = dbquery("SELECT bl.*,up.user_name AS post_name,up.user_profileurl AS post_url,ue.user_name AS edit_name, ue.user_profileurl AS edit_url FROM ".DB_BLOG." bl
			LEFT JOIN ".DB_USERS." up ON bl.blog_user=up.user_id
			LEFT JOIN ".DB_USERS." ue ON bl.blog_edit_user=ue.user_id
			WHERE blog_id='".$_GET['id']."' AND blog_draft='0'");

		$data = dbarray($result);
		//verificare url

		$URLcorect = "/".$data['post_url']."/blog/".urltext($data['blog_subject']).".".$data['blog_id'];
		if (PAGE_REQUEST != $URLcorect) { 
			redirect("http://profil.weskate.ro".$URLcorect);
		}

		//end verificare url
			//setting meta
			set_title($data['blog_subject']." - Blogul lui ".$data['post_name']);
			$keywords = trim(strip_tags($data['blog_subject']));
			$keywords = keywordize($data['blog_subject']);

			set_meta("keywords",$keywords);

			//end setting meta.
		//calculam $iRead
		$iRead = false;
		if (iMEMBER && $userdata['user_id'] == $VizProfil) {
			$iRead = true;
		} else {
			if ($data['blog_visibility'] == 0) {
				$iRead = true;
			} elseif ($data['blog_visibility'] == 1) {
				if (iMEMBER) { 
					$iRead = true;
				}
			} elseif ($data['blog_visibility'] == 2) {
				if (iMEMBER && friends($VizProfil,$userdata['user_id'])) {
					$iRead = true;
				}
			}
		}
		//$iRead calculat.

		if ($iRead) {
			 //blog_reads grows
			 $result2 = dbquery("UPDATE ".DB_BLOG." SET blog_reads=blog_reads+1 WHERE blog_id='".$_GET['id']."'");
			 $data['blog_reads']++;

			echo "<hr />";
			echo "<span style='font-size:15px;padding-left:20px;'><a href='http://profil.weskate.ro/".$data['post_url']."/blog'>Blogul lui ".$data['post_name']."</a> &rsaquo; <a href='http://profil.weskate.ro/".$data['post_url']."/blog/".urltext($data['blog_subject']).".".$data['blog_id']."'>".$data['blog_subject']."</a></span>";
			echo "<hr />";	
			echo "<table cellpadding='1' cellspacing='1' width='100%'><tr valign='top'><td>";		
			echo "<div class='blog-inside-dark round' style='border:1px solid #999;'>\n";

			echo "<table cellpadding='7' cellspacing='0' width='100%'>";
			echo "<tr>\n";
			echo "<td class='title-blog'><span>\n";
			echo $data['blog_subject']."</span>";
			echo "</td>";
			echo "<td width='1%' style='white-space:nowrap;padding-right:10px;text-align:center;'>";
			echo "<span style='font-size:14px;'><strong>".$data['blog_reads']."</strong><br />vizualiz&#259;ri</span>";
			echo "</td>";
			if (iMEMBER) {
				echo "<td width='1%' style='white-space:nowrap;padding-right:10px;text-align:center;'>";
				add_to_head("<script type=\"text/javascript\" src=\"http://weskate.ro/scripts/js/gradualfader.js\">\n
				/***********************************************\n
				* Gradual Element Fader- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)\n
				* Visit http://www.dynamicDrive.com for hundreds of DHTML scripts\n
				* This notice must stay intact for legal use\n
				***********************************************/\n
				</script>\n");
				
				//favorite : 
				require_once SCRIPTS."sistem_favorite/functii.php";

				echo " <div id='favorite".$data['blog_id']."B' style='white-space:nowrap;display:inline-block;width:32px;' class='gradualfader'>";
				if (LaFavorite($data['blog_id'],"B")) {
					echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('http://profil.weskate.ro/ajaxfa.php?a=rm&amp;id=".$data['blog_id']."&amp;t=B','favorite".$data['blog_id']."B');\" title=\"&#350;terge de la favorite\"><img src=\"http://img.weskate.ro/fav_del_mare.png\" alt=\"&#350;terge de la favorite\" style=\"border: 0pt none ; vertical-align: middle;\" /></a>";
				} else {
					echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('http://profil.weskate.ro/ajaxfa.php?a=add&amp;id=".$data['blog_id']."&amp;t=B','favorite".$data['blog_id']."B');\" title=\"Adaug&#259; la favorite\"><img src=\"http://img.weskate.ro/fav_add_mare.png\"  style=\"border: 0pt none ; vertical-align: middle;\" alt=\"Adaug&#259; la favorite\" /></a>";
				}
				echo "</div>";

				if ($VizProfil == $userdata['user_id']) {
					echo "<a href='http://weskate.ro/membri/blog.php?action=edit&amp;blog_id=".$data['blog_id']."&amp;key=".$_SESSION['user_key']."' title='Editeaz&#259; blog' style='margin-left:5px;'><img src='http://img.weskate.ro/edit_mare.png' style='border: 0pt none; vertical-align: middle;' alt='Editeaz&#259; blog'  class='gradualfader' /></a>";
				}
				echo "<script type=\"text/javascript\">\n
					gradualFader.init() //activate gradual fader\n
				      </script>\n";
				//end favorite
				echo "</td>";
			}


			echo "</tr><tr><td colspan='".(iMEMBER ? "3" : "2")."'>";
			echo nl2br($data['blog_blog']);
			echo "</td></tr><tr><td style='padding:4px;background-image:url(http://t.img.weskate.ro/panou-mid.png);background-repeat:repeat-x;border-top:1px solid #ccc;' colspan='".(iMEMBER ? "3" : "2")."'>";
			echo " <strong> &middot; </strong> Postat la <strong>".date("j F Y",$data['blog_datestamp'])."</strong>. ";
			if ($data['edit_name']) {
				echo "Ultima modificare efectuat&#259; de <a href='http://profil.weskate.ro/".$data['edit_url']."'><strong>".$data['edit_name']."</strong></a> ".showdate("shortdate",$data['blog_edit_datestmp']).".";
			}
			echo "</td></tr>";
			echo "</table>\n";

			echo "</div>\n";
		} else {
			redirect("http://profil.weskate.ro/".$user."/blog/err:access-deny");
		}
	} else {
		redirect("http://profil.weskate.ro/".$user."/blog/err:not-found");
	}
	} else { 
			//show user blogs
		if (isset($_GET['err']) && $_GET['err'] == "nf") {
			echo "<div class='notered'>Postarea c&#259;utat&#259; nu a fost g&#259;sit&#259; &icirc;n baza de date.</div>";
		} elseif (isset($_GET['err']) && $_GET['err'] == "nr") {
			echo "<div class='notered'>Postarea pe care ai vrut s&#259; o cite&#351;ti este vizibil&#259; ".(!iMEMBER ? "doar pentru membri, " : "")."doar pentru prietenii lui ".$user_data['user_name']." sau doar autorului.</div>";
		}


			//selectam wherevisy pentru a fi sigur ca nimeni nu poate citi mai mult decat are voie,  nici macar preview's.
		$wherevisy = " AND blog_visibility='0'"; //visible to anyone.

		if (iMEMBER) { //membru, blog_visibility=1
			$wherevisy = " AND blog_visibility<2"; //visisble to anyone, members
		}
		if (iMEMBER && friends($userdata['user_id'],$VizProfil)) {
			$wherevisy = " AND blog_visibility<3"; //visible to anyone, members, friends
		} 
		if (iMEMBER && $VizProfil == $userdata['user_id']) {
			$wherevisy = ""; //no visibility restrictions for the blog owner.
		}

		$rows = dbcount("(blog_id)", DB_BLOG, "blog_draft='0' AND blog_user='".$VizProfil."'".$wherevisy);

		$forcedRowstart = false;
		if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 1; $forcedRowstart = true;}
		$items_per_page = 7;

		if ($rows) {

			$real_rowstart = ($_GET['rowstart']-1)*$items_per_page;
			$result = dbquery("SELECT bl.*,up.user_name AS post_name,up.user_profileurl AS post_url,ue.user_name AS edit_name, ue.user_profileurl AS edit_url FROM ".DB_BLOG." bl
				LEFT JOIN ".DB_USERS." up ON bl.blog_user=up.user_id
				LEFT JOIN ".DB_USERS." ue ON bl.blog_edit_user=ue.user_id
				WHERE blog_draft = '0' AND blog_user='".$VizProfil."'".$wherevisy."
				ORDER BY blog_datestamp DESC LIMIT ".$real_rowstart.",$items_per_page
				");

			$i = 0;
			echo "<table cellpadding='0' cellspacing='0' width='100%'><tr valign='top'><td>";
			while ($data = dbarray($result)) {
				if ($i == 0) {
					set_title("Blogul lui ".$data['post_name']." pe We Skate");
					set_meta("keywords","blogul,lui,".$data['post_name']);
					set_meta("description","Blogul We Skate al lui ".$data['post_name']);


					if ($_GET['rowstart'] != 1) {
						add_to_title(" (pagina ".$_GET['rowstart'].")");
					}
				}			
				echo "<div class='blog-inside-".($i % 2 == 0 ? "dark" : "light")." round' style='border:1px solid #999;'>\n";
		
				echo "<table cellpadding='7' cellspacing='0' width='100%'>";
				echo "<tr valign='top'>\n";
				echo "<td class='title-blog'>\n";
				$blog_subj = $data['blog_subject'];
				echo "<a href='http://profil.weskate.ro/".$data['post_url']."/blog/".urltext($data['blog_subject']).".".$data['blog_id']."'>".$blog_subj."</a>";
				echo "</td></tr><tr><td>";
				echo nl2br($data['blog_blog']);
				echo "</td></tr><tr><td style='padding:4px;background-image:url(http://t.img.weskate.ro/panou-mid.png);background-repeat:repeat-x;border-top:1px solid #ccc;'>";
				echo " <strong> &middot; </strong> Postat la <strong>".showdate("shortdate",$data['blog_datestamp'])."</strong>. ";
				if ($data['edit_name']) {
				echo "Ultima modificare efectuat&#259; de <a href='http://profil.weskate.ro/".$data['edit_url']."'><strong>".$data['edit_name']."</strong></a> la <strong>".date("j F Y",$data['blog_edit_datestmp'])."</strong>.";
				}
				echo "</td></tr>";
				echo "</table>\n";
	
				echo "</div>\n";
				
				$i++;
	
			}		

			if ($rows > $items_per_page) echo "<div align='center' style=';margin-top:5px;'>\n".pagenav($_GET['rowstart'],$rows,$items_per_page,"http://profil.weskate.ro/".$user_data['user_profileurl']."/blog/pag")."\n</div>\n";

		} else {


				$DoNotDisplay = true;
				if (!(iMEMBER && $VizProfil == $userdata['user_id'])) {
				echo "<div class='noteyellow'>Nicio postare &icirc;n blog &icirc;nc&#259;.</div>";
				}

		}
}
if (!(isset($DoNotDisplay) && $DoNotDisplay == true)) {
/*************************************************   RIGHT SIDE   */
		echo "</td><td width='200' style='padding-left:4px;'>";

					if (iMEMBER && $VizProfil == $userdata['user_id']) {
						
						openside("Blogul meu",$user_data['user_culoarepagina']);
						echo "<a href='http://weskate.ro/membri/blog.php?key=".$_SESSION['user_key']."' class='lightonhoverF side' style='display:block;padding:4px;' title='Editeaz&#259; post&#259;rile deja existente din blogul t&#259;u sau scrie altele noi'><span style='padding-left:20px;background-image:url(http://img.weskate.ro/edit.gif);background-repeat:no-repeat;background-position:center left;'>Administrare blog</span></a>";
						echo "<a href='#' class='lightonhoverF side spacer' style='display:block;padding:4px;' title='Afl&#259; tot ce trebuie s&#259; &#351;ti pentru a-&#355;i face blogul a&#351;a cum dore&#351;ti'><span style='padding-left:20px;background-image:url(http://img.weskate.ro/question.png);background-repeat:no-repeat;background-position:center left;'>Nu m&#259; descurc!</span></a>";
						closeside();
					}

			
			//postari favorite
			$result = dbquery("SELECT fa.item_id,bl.blog_subject,bl.blog_user,us.user_name,us.user_profileurl FROM ".DB_PREFIX."favo fa
				LEFT JOIN ".DB_BLOG." bl ON fa.item_id=bl.blog_id
				LEFT JOIN ".DB_USERS." us ON bl.blog_user = us.user_id
				WHERE fav_type='B' AND fav_user='".$VizProfil."'
				ORDER BY RAND() DESC LIMIT 0,10
			");
			if (dbrows($result)) {	
				openside("Post&#259;ri favorite","galben");
				while ($data = dbarray($result)) {
					$blog_subj = $data['blog_subject'];
					echo "<a href='http://profil.weskate.ro/".$data['user_profileurl']."/blog/".urltext($data['blog_subject']).".".$data['item_id']."' style='font-size:13px;display:block;margin-bottom:2px;padding-left:20px;background:url(http://img.weskate.ro/go.png) center left no-repeat;' title='".$blog_subj."'>".trimlink($blog_subj,23)."</a>";

				}
				closeside();
			}
			//end postari favorite

			echo "</td></tr></table>";
				
/*************************************************  end RIGHT SIDE   */
} else {
	if (iMEMBER && $VizProfil == $userdata['user_id']) {
		echo "<table cellpadding='4' cellspacing='6' width='90%' align='center'><tr valign='top'><td width='50%' style='text-align:justify;padding-right:7px;'>";
		echo "<span class='capmain' style='display:block;font-size:17px;'>Incepe sa postezi!</span>";
		echo "<img src='http://img.weskate.ro/edit_mare.png' alt='colors' align='left' style='margin:5px;' />Nici nu ai idee c&acirc;t de usor este s&#259; postezi &icirc;n blog. Trebuie doar s&#259; ape&#351;i <a href='http://weskate.ro/membri/blog.php?key=".$_SESSION['user_key']."'><em>Administrare blog</em></a>, s&#259; scrii un subiect, s&#259; alegi categoria &#351;i s&#259; scrii articolul folosind BBcode (ca &icirc;n post&#259;rile din forum), astfel c&#259; nu &icirc;&#355;i trebuie cuno&#351;tin&#355;e HTML. Po&#355;i previzualiza articolul de c&acirc;te ori vrei &icirc;nainte de a-l posta &#351;i po&#355;i chiar s&#259;-l salvezi ca ciorn&#259;.<br />
<span style='display:block;padding:4px;text-align:right;font-size:13px;'>
<a href='http://weskate.ro/membri/blog.php?key=".$_SESSION['user_key']."'>&icirc;ncepe s&#259; postezi</a> <strong>&rsaquo;&rsaquo;</strong><br />
<a href='#'>Afl&#259; cum s&#259; scrii &icirc;n blog (pas cu pas)</a> <strong>&rsaquo;&rsaquo;</strong>
</span>";
		echo "</td><td width='50%' style='text-align:justify;padding-left:10px;background:url(http://t.img.weskate.ro/linie_pe_bara.png) no-repeat center left;'>";
		echo "</td></tr></table>";
	}
}




	} //end show user blogs


require_once SCRIPTS."footer.php";
?>
