var component = {
    setState: function (variable, value) {
        this.scope[variable].value = value
        var callbackList = this.scope[variable].callbackList
        for (var id in callbackList) {
            var callback = callbackList[id]
            var el = document.getElementById(id)
            eval('(' + callback + ')(el, value)')
        }
    }
}