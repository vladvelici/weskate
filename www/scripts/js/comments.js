function comment_post(type,id,append,usrKey) {
	var text = document.getElementById('comment_msg').value;
	text = text.replace("&","%26");
	ajaxpage('/scripts/comm_ajax.php?act=post&key='+usrKey+'&type='+type+'&id='+id+'&text='+text,'showcomments',true,append);
	document.getElementById('postcomment').style.display='none';
	return false;
}

function comment_saveEdit(id,usrKey) {
	var text = document.getElementById('comment_edit_'+id).value;
	text = text.replace("&","%26");
	ajaxpage('/scripts/comm_ajax.php?act=edit&id='+id+'&key='+usrKey+'&text='+text,'comment_msg_'+id,true);
	comment_cancelEdit(id);
}

function comment_cancelEdit(id) {
	document.getElementById('comment_msg_'+id).className="vizibil";
	document.getElementById('comment_editdiv_'+id).className="ascuns";
}


function editComment(id) {
	document.getElementById('comment_msg_'+id).className="ascuns";
	document.getElementById('comment_editdiv_'+id).className="vizibil";
}

function deleteComment(id,usrKey) {
	if (confirm("Ești sigur că vrei să ștergi comentariul?")) {
		ajaxpage('/scripts/comm_ajax.php?act=del&id='+id+'&key='+usrKey,'comment_'+id);
	}
}

function comments_page(page,type,id) {
	ajaxpage('/scripts/comm_ajax.php?act=page&page='+page+'&type='+type+'&id='+id,'showcomments');
}
