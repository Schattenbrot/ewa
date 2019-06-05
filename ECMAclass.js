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
  for(var i = 0; i < sel.childElementCount; i++) {
    if (sel.childNodes[i+1].selected == true) {
      price -= items[i].Price;;
      items.splice(i, 1);
      sel.remove(i);
    }
  }
  document.getElementById("preis").innerText = price.toFixed(2) + "€";
}