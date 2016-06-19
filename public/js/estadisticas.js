var estadisticas = new Ractive({
    el: "#estadisticas",
    template: "#tpl-estadisticas",
    data: {
        meses: {}, max: 0, width: 300, height: 200, num: 0,
        posx: function(e,m){ return e/m*100; }
    }
});