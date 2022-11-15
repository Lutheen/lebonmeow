let input = document.querySelector(".listened");
let button = document.querySelector(".forgot-form-button");
button.disabled = true;
input.addEventListener("change", stateHandle);
function stateHandle() {
  if (document.querySelector(".listened").value === "") {
    button.disabled = true; 
  } else {
    button.disabled = false;
  }
}