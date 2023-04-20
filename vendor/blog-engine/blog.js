import * as bootstrap from "../twbs/bootstrap/dist/js/bootstrap.min.js";

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
