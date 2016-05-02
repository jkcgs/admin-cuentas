var deudores = new ObjBase({
    el: "#deudores-cont",
    template: "#tpl-deudores",
    data: {
        deudores: [],
        totalDeuda: function(id) {
            var all = deudas.get('deudas').filter(function(e){
                return 'deudor' in e && e.deudor == id && e.pagado != "1";
            });

            var suma = 0;
            all.forEach(function(e){ suma += parseFloat(e.monto); });
            return suma;
        }
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
    form_elements: ['nombre', 'descripcion']
});

$(function(){
    deudas.observe("deudas.*", function(){
        deudores.update();
    });
});