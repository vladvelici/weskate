var LTPnow=1;
var LTPtime;
var LTPmax;
var timer=0;

function LTPsetMax(newMaxVal) {
	LTPmax = newMaxVal;
}

function changeSlide(slideId) {
	if (slideId==0) {
		if (LTPnow < LTPmax) {
			slideId = LTPnow+1;
		} else {
			slideId = 1;
		}
	}
	if (slideId != LTPnow) {
		document.getElementById('LTslideItem'+slideId).style.display='inline-block';
		document.getElementById('LTslideItem'+LTPnow).style.display='none';
		document.getElementById('LTslideLink'+slideId).style.borderWidth="2px 2px 2px 0px";
		document.getElementById('LTslideLink'+slideId).style.borderColor="#333";
		document.getElementById('LTslideLink'+slideId).style.borderStyle="solid";
		document.getElementById('LTslideLink'+LTPnow).style.border="0pt none";
		LTPnow=slideId;
		return false;
	} else {
		return true;
	}
}

function LTPtimeSlide(seconds) {
	var tim = seconds*1000;
	LTPtime = setTimeout("changeSlide(0);LTPtimeSlide("+seconds+");",tim);
	timer=1;
}

function LTPtimePause() {
	clearTimeout(LTPtime);
	timer = 0;
}

