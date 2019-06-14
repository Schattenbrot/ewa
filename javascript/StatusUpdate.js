function createStatus(jsonobj) {
  let sel = document.getElementById("div1");
  while (sel.firstChild) {
    sel.removeChild(sel.firstChild);
  }

  for(var i = 0; i < jsonobj.length; i++) {
    var currJson = jsonobj[i];
    //alert("CREATE STATUS: " + currJson['PizzaID'] + " " + currJson['Status']);
    let PizzaName = currJson["PizzaName"];
    let Status = currJson["Status"];

    let newElem = document.createElement("p");
    let newText = document.createTextNode(PizzaName + ": " + Status);
    newElem.appendChild(newText);
    sel.appendChild(newElem);

  }
}

var request = new XMLHttpRequest();

function requestData() {
  //alert("RD1: " + request.readyState);
  request.open("GET", "KundenStatus.php");
  //alert("RD2: " + request.readyState);
  request.onreadystatechange = processData;
  //alert("RD3: " + request.readyState);
  request.send(null);
  //alert("RD4: " + request.readyState);
}

function processData() {
  //alert(request.readyState);
  if (request.readyState == 4) {
    if (request.status == 200) {
      if (request.responseText != null) {
        process(request.responseText);
      } else {
        console.error("Dokument leer.");
      }
    } else {
      console.error("Fehler bei Datenübertragung.");
    }
  } else {
//    console.log("Übertragung läuft noch.");
  }
}

function process(jsonResponse) {
  "use strict";
  const jsonobj = JSON.parse(jsonResponse);
  createStatus(jsonobj);
}

onload = function() {
  window.setInterval (requestData, 2000);
}