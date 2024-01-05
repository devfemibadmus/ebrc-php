const form = document.querySelector("#cashout");
const amountInput = document.querySelector("#amount");

form.addEventListener("submit", (event) => {
  event.preventDefault();

  let amount = amountInput.value;
  amount = parseFloat(amount).toFixed(2);
  amountInput.value = amount;
  const errorMessage = document.getElementById("error");
  if(errorMessage.innerHTML == ""){
    form.submit();
  }
});
function setCurrentBalance(balance) {
  document.getElementById("current-balance").innerHTML = balance;
}


function checkInputValue() {
  const amount = document.getElementById("amount").value;
  const errorMessage = document.getElementById("error");
  const currentBalance = parseFloat(document.getElementById("current-balance").innerHTML);
  if (amount > currentBalance) {
    errorMessage.innerHTML = "The input value is greater than the current balance.";
  }
  else if (amount < 100) {
    errorMessage.innerHTML = "Amount must be at least 100 Naira";
  } else {
    errorMessage.innerHTML = "";
  }
}
