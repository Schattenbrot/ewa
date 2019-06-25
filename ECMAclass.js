var items = [];
var price = 0;

function addItem(pizzaID, pizzaName, preis) {
  "use strict";
  var item = {};
  item.id = pizzaID;
  item.name = pizzaName;
  item.Price = preis;
  price += item.Price;
  items.push(item);

  var sel = document.getElementById('myList');
  var opt = document.createElement('option');
  opt.appendChild(document.createTextNode(pizzaName));
  opt.value = pizzaID;

  sel.appendChild(opt);
  document.getElementById("preis").innerText = price.toFixed(2) + "€";
}
function deleteAll() {
  "use strict";
  let sel = document.getElementById('myList');
  items = [];
  price = 0;
  while (sel.length > 0) {
    sel.remove(0);
  }
  document.getElementById("preis").innerText = price.toFixed(2) + "€";
}
function selectAll() {
  "use strict";
  var sel = document.getElementById('myList');
  for(var i = 0; i <= sel.childElementCount; i++) {
    sel.childNodes[i].selected = true;
  }
}
function deleteSelected() {
  "use strict";
  var sel = document.getElementById('myList');
  for(var i = sel.childElementCount - 1; i >= 0; i--) {
    if (sel.options[i].selected) {
      price -= items[i].Price;
      items.splice(i, 1);
      sel.options[i].remove();
    }
  }
  document.getElementById("preis").innerText = price.toFixed(2) + "€";
}

function validate() {
  var filled = true;

  if (document.getElementById("adressText").value == "" ||
      items.length == 0) {
    filled = false;
  }

  if (filled) {
    document.getElementById("submit").disabled = false;
  } else {
    document.getElementById("submit").disabled = true;
  }
}

function check() {
  document.getElementById("adressText").onkeyup = validate();
  document.getElementById("adressText").onkeydown = validate();
}

onload = function() {
  window.setInterval (check, 200);
}