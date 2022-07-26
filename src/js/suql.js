function assign(a, b)
{
    a.value = typeof b === "function" ? b(a.value) : b
    
    var elementId = a.path
    if (typeof a.value === "array") {
        // ...
    }
    else if (typeof a.value === "object") {
        // ...
    }
    else {
        document.getElementById(elementId).textContent = a.value
    }
}