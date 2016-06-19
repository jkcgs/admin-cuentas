var deudas = new ObjBase({
    el: "#deudas",
    template: "#tpl-deudas",
    data: {
        deudas: [], deudores: [],
        no_pagadas: function() {
            return this.get('deudas').filter(function(e){
                return 'pagada' in e && e.pagado != "1";
            });
        },
        get_deudor: function(id) {
            var t = this.get('deudores');
            for(var i = 0; i < t.length; i++){
                if(t[i].id == id) return t[i];
            }

            return null;
        },

        sumar_deudas: function() {
            var d = this.get('deudas'), res = 0;
            for(var i = 0; i < d.length; i++) {
                res += parseFloat(d[i].monto);
            }

            return res;
        }
    },

    obj_name: "deudas",
    modal: "#agregar-deuda",
    strings: {
        t_agregar: "Agregar deuda",
        t_agregar_btn: "Agregar",
        t_editar: "Editar deuda #"
    },
    acciones: {
        agregar: "agregar_deuda",
        editar: "editar_deuda",
        borrar: "borrar&t=deudas",
        get: "get&t=deudas"
    },
    form_elements: [
        'deudor', 'descripcion', 'monto', 'fecha', 'pagada'
    ],
    form_defaults: {
        'monto': 0,
        'fecha': inFechaHoy()
    }
});

deudas.on('pagar', function(e, id){
    $('[data-deuda-id="'+id+'"').addClass("btn-loading").prop("disabled", true);
    deudas.jget('acciones/?marcar_pagadas&deudas&ids='+id, function(e, data){
        if(e != null) {
            alert(e.message);
        } else {
            deudas.dset(id, 'pagada', "1");
        }
        $('[data-deuda-id="'+id+'"').removeClass("btn-loading").prop("disabled", false);
    });
});
deudas.on('despagar', function(e, id){
    $('[data-deuda-id="'+id+'"').addClass("btn-loading").prop("disabled", true);
    deudas.jget('acciones/?marcar_pagadas&deudas&pagadas=0&ids='+id, function(e, data){
        if(e != null) {
            alert(e.message);
        } else {
            deudas.dset(id, 'pagada', "0");
        }
        $('[data-deuda-id="'+id+'"').removeClass("btn-loading").prop("disabled", true);
    });
});

deudas.form = document.agregar_deuda;
deudores.observe('deudores', function(m,v,c){
    deudas.set('deudores', deudores.get('deudores'));
});
