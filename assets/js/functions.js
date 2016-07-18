String.prototype.reverse = function() {
    if(this.toString() === "") return this;

    return this.split('').reverse().join('');
};