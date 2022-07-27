function getElementsByQuery(query) {
    var queryParts = query.split('>')
    var id = queryParts[0]
    var className = queryParts[1]
    return document.getElementById(id).getElementsByClassName(className)
}

function assign(a, b)
{
    a.value = typeof b === "function" ? b(a.value) : b
    var elements = getElementsByQuery(a.path)
    
    if (typeof a.value === "object") {
        var content = "";
        for(var i = 0, n = a.value.length; i < n; i++) {
            var template = a.template;
            for (variable in a.value[i]) {
                template = template.replace("[[" + variable + "]]", a.value[i][variable])
            }
            content += template
        }

        for (var i = 0, n = elements.length; i < n; i++) {
            elements[i].innerHTML = content
        }
    }
    else {
        for (var i = 0, n = elements.length; i < n; i++) {
            elements[i].textContent = a.value
        }
    }
}