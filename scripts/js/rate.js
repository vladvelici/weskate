function rate(type,id,vote,key) {
	ajaxpage('/scripts/rate.php?type='+type+'&id='+id+'&vote='+vote+'&key='+key,'ratings-'+type+'-'+id);
}
