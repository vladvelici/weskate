function colorMenu(newColor) {
	for (i=1;i<=4;i++) {
		try {
		document.getElementById('menuitem'+i).style.color='#'+newColor;
		} catch(err) {}
	}
}
function colorBorder(newColor) {
	document.getElementById('avatar-paneltop').style.borderColor='#'+newColor;
}
function showSaveTool() {
	document.getElementById('saveTool').style.display='block';
	document.getElementById('saveTool').innerHTML='<div style=\"background-color:#F7FFB0;padding:5px;font-size:13px;display:block;\"><div class=\"flright\"><a href=\"javascript:void(0)\" onclick=\"saveColors();\">Salveaz&#259;</a> -- <a href=\"javascript:void(0)\" onclick=\"setDefaults();\">Anuleaz&#259; modific&#259;rile</a></div>&#354;i-ai modificat profilul. Dore&#351;ti s&#259; salvezi modific&#259;rile?</div>';
}
function hideSaveTool() {
	document.getElementById('saveTool').style.display='none';
}

function saveColors() {
	var cn=document.getElementById('color_name').value;
	var bpt=document.getElementById('background_panel_title').value;
	var cpt=document.getElementById('color_panel_title').value;
	var wpb=document.getElementById('width_panel_border').value;
	var cpb=document.getElementById('color_panel_border').value;
	var bpb=document.getElementById('background_panel_body').value;
	var cml=document.getElementById('color_menu_link').value;
	
	ajaxpage('ajaxcolors.php?type=p&cn='+cn+'&bpt='+bpt+'&cpt='+cpt+'&wpb='+wpb+'&cpb='+cpb+'&bpb='+bpb+'&cml='+cml,'saveTool');
}

function editpanel(panel_id) {
	ajaxpage('/editpanel.php?do=showedit&panel_id='+panel_id,'panel_c'+panel_id,true);
}
function noPanelEdit(panel_id) {
	ajaxpage('/editpanel.php?do=render&panel_id='+panel_id,'panel_c'+panel_id,true)
}
function submitEdit(panel_id) {
	try {
	var form = document.getElementById('panel_form_'+panel_id);
	var params="";
	var childs = form.childNodes;
	for (i=0;i<childs.length;i++) {
		if (childs[i].tagName == "INPUT" || childs[i].tagName == "SELECT" || childs[i].tagName == "TEXTAREA" ||
		    childs[i].tagName == "input" || childs[i].tagName == "select" || childs[i].tagName == "textarea") {
			txt = childs[i].value.replace("&","%26");
			params += "&"+childs[i].getAttribute('name')+"="+txt;
			if (childs[i].getAttribute('name')=="paneltitle") {
				document.getElementById('paneltitle'+panel_id).innerHTML=childs[i].value;
			}
		}
	}
	ajaxpage('/editpanel.php?do=save&panel_id='+panel_id+params,'panel_c'+panel_id,true);
	} catch (err) { }
	return false;
}
function newPanel(order) {
	var divid = 'newpaneldiv'+order;
	ajaxpage('/editpanel.php?do=templatelist&order='+order,divid,true);
}
function cancelNewPanel(order) {
	document.getElementById('newpaneldiv'+order).innerHTML = "<a href='javascript:newPanel("+order+");' class='header-link-m acasa smallround' style='display:inline-block;padding:3px 3px 3px 20px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3px 50%;'>Adaugă un panou nou aici</a>";
}
function createNewPanel(order,key) {
	var template = document.getElementById('template'+order).value;
	ajaxpage('/editpanel.php?do=create&key='+key+'&order='+order+'&template='+template,'newpaneldiv'+order,true);
}
function deletepanel(id,key) {
	ajaxpage('/editpanel.php?do=delete&key='+key+'&panel_id='+id,'panelbig'+id,true);
}
