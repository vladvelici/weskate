function deleteFriend(f,key) {
	if (confirm("Sigur ștergi ?")) {
		ajaxpage('/ajaxfriend.php?delete='+f+'&key='+key+'&profil','friend'+f);
	}
}
function acceptFriend(f,key) {
	if (confirm("Sigur accepți ?")) {
		ajaxpage('/ajaxfriend.php?accept='+f+'&key='+key+'&profil','request'+f);
	}
}
function denyFriend(f,key) {
	if (confirm("Sigur refuzi ?")) {
		ajaxpage('/ajaxfriend.php?deny='+f+'&key='+key+'&profil','request'+f);
	}
}
