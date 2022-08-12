function getElementsByQuery(query, add = false) {
    var queryParts = query.split('>')
    var id = queryParts[0]
    if (id === "") {
        return []
    }
    var className = queryParts[1]
    var elements = document.getElementById(id).getElementsByClassName(className)
    return elements
}

function assign(a, b) {
    a.value = typeof b === "function" ? b(a.value) : b

    for (var path in a.paths) {
        var elements = getElementsByQuery(path)

        if (elements.length === 0) {
            return
        }

        var config = a.paths[path]

        for (var i = 0, n = elements.length; i < n; i++) {
            switch (config.format) {
                case "raw":
                    elements[i].textContent = a.value
                    break
                case "value":
                    elements[i].value = a.value
                    break
                case "html":
                    var content = "";
                    for (var i = 0, n = a.value.length; i < n; i++) {
                        var template = config.template;
                        for (variable in a.value[i]) {
                            template = template.replace("[[" + variable + "]]", a.value[i][variable])
                        }
                        content += template
                    }

                    for (var i = 0, n = elements.length; i < n; i++) {
                        elements[i].innerHTML = content
                    }
                    break
            }
        }
    }

    // TODO: обновить все связи по template functions которые используют данную переменную
}

function append(a, b) {
    for (var path in a.paths) {
        var elements = getElementsByQuery(path)

        if (elements.length === 0) {
            return
        }

        var config = a.paths[path]

        for (var i = 0, n = elements.length; i < n; i++) {
            switch (config.format) {
                case "html":
                    var content = "";
                    var template = config.template;
                    for (variable in b) {
                        template = template.replace("[[" + variable + "]]", b[variable])
                    }
                    content += template

                    for (var i = 0, n = elements.length; i < n; i++) {
                        elements[i].innerHTML += content
                    }
                    break
            }
        }
    }
}

function get(a)
{
    return a.value
}