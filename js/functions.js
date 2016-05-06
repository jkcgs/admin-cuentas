String.prototype.reverse = function() {
    if(this.toString() == "") return this;

    return this.split('').reverse().join('');
};

function inFechaHoy() {
	var hoy = (new Date());
	var mes = hoy.getMonth(); if(mes < 10) mes = "0" + mes;
	var dia = hoy.getDate(); if(dia < 10) dia = "0" + dia;

	return hoy.getFullYear() + "-" + mes + "-" + dia;
}

function formatearDinero(s) {
    if(typeof s == "number") s = s.toString();
    else if(s === "") s = "0";

	return "$" + s.reverse().match(/[0-9]{1,3}/g).join('.').reverse();
}

function btnLoading(sel, loading) {
    var e = $(sel);
    e[loading ? 'addClass' : 'removeClass']("btn-loading");
    e.prop("disabled", !!loading);
}

$('.navbar-nav a').on('click', function(){
    var nt = $('.navbar-toggle');
    if(nt.is(':visible')) nt.click();
});