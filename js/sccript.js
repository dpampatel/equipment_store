const hamburger = document.querySelector(".hamburger");
const navigationList = document.querySelector(".navigationList");

hamburger.addEventListener("click", () => {
  navigationList.classList.toggle("active");
  hamburger.classList.toggle("active");
});

function updateQuantity(change) {
  var qtyInp = document.getElementById("quantityInput");
  var curQty = parseInt(qtyInp.value);

  if (curQty + change >= 1) {
    qtyInp.value = curQty + change;
  }

  document.querySelector(".qty span.minus").disabled = qtyInp.value === "1";
}
