let scores = document.getElementById('scores-table-body');
let form = document.getElementById('leaderboard-form');
let xhttp = new XMLHttpRequest();
let data = [];

form.oninput = function() {
    loadData();
}

xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        data = JSON.parse(this.responseText);
        loadData();
    }
}
xhttp.open('GET', './leaderboard.get.php', true);
xhttp.send();

function loadData() {
    let formData = new FormData(form);
    let matches = (name, data) => formData.get(name) != "" && !data.toLowerCase().includes(formData.get(name).toLowerCase());

    data.result.sort((a,b) => (a[formData.get('sort-by')] > b[formData.get('sort-by')]) ? formData.get('order-by') : formData.get('order-by') * -1);

    if (data.result) {
        scores.innerHTML = "";
        for (let r of data.result) {
            if (matches('first-name', r.first_name)
            || matches('last-name', r.last_name)) {
                continue;
            }
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
