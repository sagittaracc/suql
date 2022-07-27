function assign(a, b)
{
    a.value = typeof b === "function" ? b(a.value) : b
    
    var elementId = a.path
    if (typeof a.value === "object") {
        var content = "";
        for(var i = 0, n = a.value.length; i < n; i++) {
            var template = a.template;
            for (variable in a.value[i]) {
                template = template.replace("[[" + variable + "]]", a.value[i][variable])
            }
            content += template
        }
        document.getElementById(elementId).innerHTML = content;
    }
    else {
        document.getElementById(elementId).textContent = a.value
    }
}