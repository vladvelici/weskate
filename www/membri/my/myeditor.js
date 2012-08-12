var notST = 0;
var isSaved = false;
var activeTab = 0;
var openedSources=1; //ID-ul ultimei surse deschise in tab-ul "surse..."
var cmmi = 1; //cel mai mare indice
function savedCheck() {
	if (isSaved == false) {
		isSaved = true;
		updateTimer();
		showNotSavedWarning();
	}
}

function updateTimer() {
	if (notST > 4) {
		document.getElementById('notSavedTime').innerHTML = notST;
	}
	if (notST == 5) {
		document.getElementById('saveTimeC').style.display='inline';
	}
	notST = notST + 1;
	var t=setTimeout("updateTimer()",60000);
}

function showNotSavedWarning() {
	document.getElementById('savebar').style.backgroundColor = '#FFFCA8';
	notST = 0;
	document.getElementById('savebarText').innerHTML = 'Ai modificari nesalvate... ';
}

function deleteThumb(item,type,key) {
	if (confirm("Esti sigur ca vrei sa stergi pictograma?")) {
		ajaxpage('jedit.php?p=4&t='+type+'&id='+item+"&key="+key,'thumbimg');
		alert(item+";"+type+";"+key);
	} else {
		return false;
	}
}

function toggleTab(tabno) {
	if (activeTab != 0) {
		document.getElementById('toggleTabButton'+activeTab).className = 'toggleTabButton';
		document.getElementById('menuitem'+activeTab).className = 'ascuns';
	}
	if (activeTab == tabno) {
		activeTab = 0;
	} else {
		activeTab = tabno;
		document.getElementById('toggleTabButton'+tabno).className = 'toggleTabButtonA';
		document.getElementById('menuitem'+tabno).className = 'vizibil';
	}
}

function newSource(howMuch) {
	var sources = document.getElementById('sources');
	var newSource;
	for (i=1;i<=howMuch;i++) {
		openedSources++;
		cmmi++;
		newSource = document.createElement('div');
		newSource.setAttribute('id','sourceCon'+openedSources);
		newSource.innerHTML = "<span style='font-weight:bold;font-size:14px;' id='indice"+openedSources+"'>" + cmmi + "</span>" +
		"Nume sau titlu : <input type='text' name='sursatxt[]' id='sursaTxt"+openedSources+"' />&nbsp;&nbsp;Link : <input type='text' name='sursaurl[]' id='sursaUrl"+openedSources+"' /> " + 
		"<a title='Sterge sursa' href='javascript:removeSource(" + openedSources + ");' class='side'><img src='http://img.weskate.ro/uncheck.gif' alt='Sterge' title='Sterge sursa'/></a>";
		sources.appendChild(newSource);
	}
}

function fillSource(rowId, txt, url) {
	document.getElementById('sursaTxt'+rowId).value = txt;
	document.getElementById('sursaUrl'+rowId).value = url;
}

function removeSource(sourceId) {
	if (cmmi>1) {
		if (sourceId == openedSources) {
			openedSources = openedSources - 1;
		} else {
			var startIndice = document.getElementById('indice'+sourceId).innerHTML;
			for (i=sourceId+1;i<=openedSources;i++) {
				if (document.getElementById('indice'+i)) {
					document.getElementById('indice'+i).innerHTML = startIndice;
					startIndice++;
				}
			}
		}
		cmmi = cmmi - 1;
		document.getElementById('sources').removeChild(document.getElementById('sourceCon'+sourceId));
	}
}
var state=false;
function toggleMyContainer() {
	if (state) {
		document.getElementById('myContainer').style.display='none';
		document.getElementById('myContainerB').style.borderWidth='0px 0px 2px 0px';
		document.getElementById('myContainerBa').innerHTML='&darr;';
		state=false;
	} else {
		document.getElementById('myContainer').style.display='block';
		document.getElementById('myContainerB').style.borderWidth='0px';
		document.getElementById('myContainerBa').innerHTML='&uarr;';
		state=true;
	}
}
function deleteItem() {
	return confirm("Stergerea materialului inseamna pierderea lui permanenta, fara nici o posibilitate de recuperare. \n\n Sigur vrei sa-l stergi?");
}
function sureNew() {
	if (isSaved) {
		return confirm("Inceperea unui nou material inseamna pierderea modificariloir celui deschis. Continuati?");
	} else {
		return true;
	}
}
