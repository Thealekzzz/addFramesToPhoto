function $(str, all=false) {
    return all ? document.querySelectorAll(str) : document.querySelector(str)
}


