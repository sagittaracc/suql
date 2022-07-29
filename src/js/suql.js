function getElementsByQuery(query, add = false) {
    var queryParts = query.split('>')
    var id = queryParts[0]
    if (id === "") {
        return []
    }
    var className = queryParts[1]
    var elements = document.getElementById(id).getElementsByClassName(className)
    if (elements.length === 0 && add) {
        var element = document.createElement('div')
        element.classList = className
        elements = [document.getElementById(id).appendChild(element)]
    }
    return elements
}

function assign(a, b)
{
    a.value = typeof b === "function" ? b(a.value) : b
    var elements = getElementsByQuery(a.path)

    if (elements.length === 0) {
        return
    }
    
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

    // TODO: обновить все связи по template functions которые используют данную переменную
}

function append(a, b)
{
    var elements = getElementsByQuery(a.path, true);

    if (typeof a.value === "object") {
        a.value.push(b)
        var template = a.template
        for (variable in b) {
            template = template.replace("[[" + variable + "]]", b[variable])
        }

        for (var i = 0, n = elements.length; i < n; i++) {
            elements[i].innerHTML += template;
        }
    }

    // TODO: обновить все связи по template functions которые используют данную переменную
}

function get(a)
{
    return a.value
}