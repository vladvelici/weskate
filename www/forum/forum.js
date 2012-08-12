function postreply(thread,key) {
	var text = document.getElementById('replymessage').value;
	text = text.replace("&","%26");
	var quote = document.getElementById('quote').value;
	ajaxpage('/forum/post.php?do=reply&key='+key+'&quote='+quote+'&thread='+thread+'&text='+text,'viewthread',true);
	document.getElementById('replymessage').value='';
	noquote();
	document.getElementById('replydiv').className='ascuns smallround';
	return false;
}
function reply(quote,quoteName) {
	if (quote === undefined) { quote=false; }
	if (quoteName === undefined) { quoteName=false; }
	if (quote && quoteName) {
		document.getElementById('quote').value=quote;
		document.getElementById('quotediv').className='vizibil';
		document.getElementById('quotename').innerHTML=quoteName;
	}
	document.getElementById('replydiv').className='vizibil smallround';
	document.getElementById('replymessage').focus();
}
function noquote() {
	document.getElementById('quote').value=0;
	document.getElementById('quotediv').className='ascuns';
	document.getElementById('quotename').innerHTML='';
}

function noreply() {
	var cont;
	if (document.getElementById('replymessage').value=='') {
		cont=true;
	} else {
		cont=confirm("Dacă renunți la răspuns vei pierde tot ce ai scris până acum.\n\n Ești sigur că vrei să continui?");
	}
	if (cont) {
		document.getElementById('replymessage').value='';
		noquote();
		document.getElementById('replydiv').className='ascuns smallround';
	}
}

function forumpage(page,thread) {
	ajaxpage('/forum/page.php?page='+page+'&thread='+thread,'viewthread');
}
function forumspage(page,forum) {
	ajaxpage('/forum/forumspage.php?page='+page+'&forum='+forum,'viewforum');
}
function deletePost(id,key) {
	if (confirm("Ești sigur că vrei să ștergi postarea?")) {
		ajaxpage('/forum/post.php?do=delpost&id='+id+'&key='+key,'post'+id,true);
	}
}
function deleteThread(id,key) {
	if (confirm("Ești sigur că vrei să ștergi discuția?")) {
		ajaxpage('/forum/post.php?do=delthread&id='+id+'&key='+key,'viewthread',true);
	}
}
function editPost(id) {
	document.getElementById('post_msg_'+id).className='ascuns';
	document.getElementById('post_msg_edit_'+id).className='vizibil';
}
function cancelEdit(id) {
	document.getElementById('post_msg_'+id).className='vizibil';
	document.getElementById('post_msg_edit_'+id).className='ascuns';
}
function saveEdit(id,key) {
	var text = document.getElementById('post_newval_'+id).value;
	text = text.replace("&","%26");
	ajaxpage('/forum/post.php?do=edit&post='+id+'&key='+key+'&text='+text,'post_msg_'+id,true);
	cancelEdit(id);
}
function moveThread(id) {
	document.getElementById('movethread'+id+"_a").style.display='none';
	document.getElementById('movethread'+id+"_b").style.display='inline-block';	
}
function moveThreadTo(forumid,threadid,key,id) {
	if (forumid=='0' || forumid==0) {
		document.getElementById('movethread1'+id+"_a").style.display='inline-block';
		document.getElementById('movethread1'+id+"_b").style.display='none';
	} else {
		if (confirm("Ești sigur că vrei să muți discuția?")) {
			ajaxpage("/forum/post.php?do=move&thread="+threadid+"&to="+forumid+"&key="+key,"viewthread",true);
		} else {
			document.getElementById('movethread'+id+"_a").style.display='inline-block';
			document.getElementById('movethread'+id+"_b").style.display='none';
		}
	}
}
function newThread() {
	document.getElementById('newthread').className='vizibil round';
	document.getElementById('subject').focus();
}
function cancelThread() {
	var subj = document.getElementById('subject');
	var msg = document.getElementById('message');
	if ((!(subj.value) && !(msg.value)) || confirm("Ești sigur că renunți la noua discuție? Modificările vor fi pierdute.")) {
		document.getElementById('newthread').className='ascuns round';
		subj.value='';
		msg.value='';
	}
}

