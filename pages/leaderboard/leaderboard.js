let scores = document.getElementById('scores-table-body');
let form = document.getElementById('leaderboard-form');
let xhttp = new XMLHttpRequest();

form.oninput = function() {
    console.log(1);
    get_leaderboard();
}

xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        let res = JSON.parse(this.responseText);
        if (res.result) {
            scores.innerHTML = "";
            for (let r of res.result) {
                scores.innerHTML += "<tr>"
                + "<td>" + r.game_id + "</td>"
                + "<td>" + r.first_name + " " + r.last_name + "</td>"
                + "<td>" + r.score + "</td>"
                + "<td>" + r.duration + "</td>"
                + "</tr>";
            }
        } else {
            document.getElementById("error-msg").innerText = res.error;
        }
    }
}



function get_leaderboard() {
    let formData = new FormData(form);
    let s = './leaderboard.get.php?';
    s += (formData.get("first-name") != "") ? "first_name=" + formData.get("first-name") + "&" : "";
    s += (formData.get("last-name") != "") ? "last_name=" + formData.get("last-name") + "&" : "";
    s += "include_ai=" + (formData.get("include-ai") != null) + "&";
    s += "sort=" + formData.get("sort-by") + "&";
    s += "order=" + formData.get("order-by");

    xhttp.open('GET', s, true);
    xhttp.send(formData);
}

get_leaderboard();