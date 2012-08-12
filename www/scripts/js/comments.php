<?php
if (!defined("inWeSkateCheck")) { die("Acces respins"); }

function showcomments($type,$item) {
	add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/comments.js'></script>");
	add_to_head("<link rel='stylesheet' href='http://weskate.ro/look/comments.css' type='text/css' media='screen' />");
	$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_item_id=$item AND comment_type='$type'");
	if (iMEMBER) {
		global $userdata;
		echo "<div id='postcomment'>";
		echo "<div class='capmain_color' style='font-size:16px;font-weight:bold;padding:5px;'>Postează un comentariu</div>";
		echo "<form method='post' name='comment_form' action='".PAGE_REQUEST."' onsubmit='return comment_post(\"$type\",$item,".($comments ? "true" : "false").");'>";
		echo "<textarea id='comment_msg' cols='48' rows='5'></textarea><br />";
		echo "<input type='submit' value='Publică' /></form>";
		echo "</div>";
	}
	echo "<div id='showcomments'>";
	if (!$comments) {
		echo "<strong>Nici un comentariu postat încă.</strong>";
	} else {
		echo "<div class='capmain_color' style='font-size:16px;font-weight:bold;padding:5px;'>Comentarii</div>";
		echo "<div id='raw_comments'>";
		$result = dbquery("SELECT c.*,u.user_name,u.user_profileurl FROM ".DB_COMMENTS." c 
				LEFT JOIN ".DB_USERS." u ON c.comment_name = u.user_id
				WHERE comment_type='$type' AND comment_item_id=$item
				ORDER BY comment_datestamp DESC, comment_id DESC
				LIMIT 0,10");
		while ($data=dbarray($result)) {
			echo "<div id='comment_".$data['comment_id']."' class='comment'>";
			echo "<div class='comment_head'>";
			$canEdit = (dbcount("(comment_id)",DB_COMMENTS,"comment_name=".$userdata['user_id']." AND comment_id=".$data['comment_id']) || iADMIN ? true : false);
			if ($canEdit) {
				echo "<span class='flright'>";
				echo "<a href='javascript:editComment(".$data['comment_id'].");'>editează</a> / ";
				echo "<a href='javascript:deleteComment(".$data['comment_id'].");'>șterge</a>";
				echo "</span>";
			}
			echo "<a href='http://profil.weskate.ro/".$data['user_profileurl']."'>".$data['user_name']."</a> - ".showdate("datehover longdate",$data['comment_datestamp']);
			echo "</div>";
			if ($canEdit) {
				echo "<div id='comment_msg_".$data['comment_id']."' class='vizibil'>";
				echo $data['comment_message'];
				echo "</div>";
				echo "<div id='comment_editdiv_".$data['comment_id']."' class='ascuns'>";
				echo "<textarea id='comment_edit_".$data['comment_id']."' rows='3' cols='48'>".$data['comment_message']."</textarea><br />";
				echo "<a href='javascript:comment_cancelEdit(".$data['comment_id'].")'>renunță</a> - <a href='javascript:comment_saveEdit(".$data['comment_id'].");'>salvează</a>";
				echo "</div>";
			}
			echo "</div>";
		}
		echo "</div>";
	}
	echo "</div>";
}

?>
