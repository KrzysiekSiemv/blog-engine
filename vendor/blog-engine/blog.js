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

function createTag(show_name, slug, tags){
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", "/vendor/blog-engine/php/Posts/Tags/AddTags.php", true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.onreadystatechange = function (){
        if(this.readyState === 4 && this.status === 200){
            console.log(this.responseText);
            let data = JSON.parse(this.responseText);

            let div = document.createElement("div");
            div.classList.add("form-check");

            let checkbox = document.createElement('input');
            checkbox.setAttribute("type", "checkbox");
            checkbox.setAttribute("name", "tags[]");
            checkbox.setAttribute("value", data.id);
            checkbox.setAttribute("id", "tag" + data.id);
            checkbox.classList.add("form-check-input");

            let label = document.createElement("label");
            label.setAttribute("for", "tag" + data.id);
            label.classList.add("form-check-label");
            label.innerText = data.show_name;

            div.appendChild(checkbox);
            div.appendChild(label);

            tags.appendChild(div);
        }
    };
    xmlhttp.send("show_name=" + show_name + "&slug=" + slug);
}

function saveToDraft(title, content, name, tags, comments_status){

}