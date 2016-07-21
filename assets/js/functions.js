String.prototype.reverse = function() {
    if(this.toString() === "") return this;

    return this.split('').reverse().join('');
};

Promise.prototype.finally = function(onResolveOrReject) {
    return this.catch(function(reason){
        return reason;
    }).then(onResolveOrReject);
};

function strPadding(str, pad, num) {
    if(str.length >= num) {
        return str;
    }

    var p = new Array(num - str.length);
    p = p.fill(pad);
    return p.join("") + str;
}

function zeroFill(num, amount) {
    return strPadding(num+"", "0", amount);
}

function dateToForm(date, withDay) {
    if(typeof withDay == "undefined") withDay = true;

    var d = date.getFullYear() + "-" + zeroFill(date.getMonth()+1, 2);
    if(withDay) {
         d += "-" + zeroFill(date.getDate(), 2);
    }

    return d;
}