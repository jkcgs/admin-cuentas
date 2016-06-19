function getCurrentPage() {
	var dpage = "";
	var dtarget = $('[data-default-target=true]');
	if(dtarget.length > 0) {
		dpage = dtarget[0].id;
	}

	if(location.hash != "" && $(location.hash).length != 0 && $(location.hash).hasClass('container')) {
		dpage = location.hash.replace('#', '');
	}

	return dpage;
}

function updatePage() {
	var cpage = getCurrentPage();
	if(cpage == "") return;

	$('.page-container').hide();
	$('a[href^="#"]').parent().removeClass("active");

	$('#'+cpage).show();
	$('a[href="#'+cpage+'"]').parent().addClass('active');
	window.scroll(0,0);
}

$(function(){
    $('.loading-container').hide();
    updatePage();
    window.addEventListener('hashchange', function(){
        updatePage();
    });
});