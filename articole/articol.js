var selectedArticle = 0;
function changeArticle(sel) {
	if (sel != selectedArticle) {
		if (selectedArticle) {
			document.getElementById('article_'+selectedArticle).className='ascuns';
			document.getElementById('thumb_'+selectedArticle).className='ar_thumb';
		}
		document.getElementById('article_'+sel).className='vizibil';
		document.getElementById('thumb_'+sel).className='ar_thumb selected';
		selectedArticle = sel;
	}
}
