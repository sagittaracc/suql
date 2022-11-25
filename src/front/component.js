var component = {
    setState: function (obj) {
        for (prop in obj) {
            this[prop] = obj[prop]
            var callbackList = this.scope[prop].callbackList
            for (var id in callbackList) {
                var callback = callbackList[id]
                var el = document.getElementById(id)
                var value = obj[prop]
                eval('(' + callback + ')(el, value)')
            }
        }
    }
}