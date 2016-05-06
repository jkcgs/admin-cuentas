var deudores = new ObjBase({
    el: "#deudores-cont",
    template: "#tpl-deudores",
    data: {
        deudores: []
    },
    obj_name: "deudores",
    modal: "#agregar-deudor",
    strings: {
        t_agregar: "Agregar deudor",
        t_agregar_btn: "Agregar",
        t_editar: "Editar deudor #"
    },
    acciones: {
        agregar: "agregar_deudor",
        editar: "editar_deudor",
        borrar: "borrar&t=deudores",
        get: "get&t=deudores"
    },
    form: document.agregar_deudor,
    form_elements: ['nombre', 'descripcion', 'no_pagadas'],

    deudasPara: function(id, pagadas) {
        pagadas = pagadas && pagadas == true ? "1" : "0";
        return deudas.get('deudas').filter(function(e){
            return 'deudor' in e && e.deudor == id && e.pagada == pagadas;
        });
    },
    
    totalNoPagadas: function(id) {
        var all = deudores.deudasPara(id, false), suma = 0;

        all.forEach(function(e){ suma += parseFloat(e.monto); });
        return suma;
    },

    actNoPagadas: function() {
        var all = deudores.get("deudores");
        for(var i = 0; i < all.length; i++) {
            deudores.dset(all[i].id, "no_pagadas", deudores.totalNoPagadas(all[i].id));
        }
    }
});

deudores.on('marcar_pagadas', function(e, id) {
    var all = deudores.deudasPara(id, false);
    var ids = [];
    for(var i = 0; i < all.length; i++) {
        ids.push(all[i].id);
    }

    if(ids.length <= 0) {
        alert("El deudor no tiene cuentas por pagar");
        return;
    }

    btnLoading('[data-mpid="'+id+'"]', true);
    deudas.jget('acciones/?marcar_pagadas&deudas&ids='+ids.join(","), function(e, data){
        if(e != null) {
            alert(e.message);
        } else {
            for(var i = 0; i < ids.length; i++) {
                deudas.dset(ids[i], 'pagada', "1");
            }
        }

        btnLoading('[data-mpid="'+id+'"]', false);
    });
});

$(function(){
    deudas.observe("deudas.*", function(){
        deudores.actNoPagadas();
    });
    deudores.actNoPagadas();
});