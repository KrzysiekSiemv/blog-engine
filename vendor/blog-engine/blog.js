(async() => {
    const bootstrap = await import("../twbs/bootstrap/dist/js/bootstrap.min.js");
    
});

if(document.getElementById("testConn"))
    document.getElementById("testConn").addEventListener("click", () => testConnection());

function testConnection(){
    let server = document.getElementsByName("DB_HOST")[0].value;
    let user = document.getElementsByName("DB_USER")[0].value;
    let pass = document.getElementsByName("DB_PASS")[0].value;
    let name = document.getElementsByName("DB_NAME")[0].value;
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function (){
        if(this.readyState == 4 && this.status == 200){
            document.getElementById("connStatus").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "_checkdbconn.php?s=" + server + "&u=" + user + "&p=" + pass + "&d=" + name, true);
    xmlhttp.send();
}

function insertAtCursor(field, text, mark) {
    let sel = window.getSelection().getRangeAt(0);
    if(sel != null){
        let startPos = sel.startOffset;
        let endPos = sel.endOffset;

        if(mark === true){
            let start_tag = text.substring(0, (text.length % 2 === 0 ? Math.ceil(text.length / 2) : Math.ceil(text.length / 2) - 1));
            let end_tag = text.substring((text.length % 2 === 0 ? Math.ceil(text.length / 2) : Math.ceil(text.length / 2) - 1), text.length);

            field.innerHTML = field.innerHTML.substring(0, startPos) + start_tag + field.innerHTML.substring(startPos, endPos) + end_tag + field.innerHTML.substring(endPos, field.innerHTML.length);
        } else {
            field.innerHTML = field.innerHTML.substring(0, startPos) + text + field.innerHTML.substring(startPos, endPos) + "<br>" + field.innerHTML.substring(endPos, field.innerHTML.length);
        }
    } else
        field.innerHTML += text;
}