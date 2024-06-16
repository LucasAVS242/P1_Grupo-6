var modalAprovar = document.getElementById("modalAprovar");
var modalRejeitar = document.getElementById("modalRejeitar");
var modalObservacao = document.getElementById("modalObservacao");

function openModal(modal) {
  if (modal == 'aprovar') {
    modalAprovar.style.display = "block";
  }
  else {
    if (modal == 'rejeitar') {
      modalRejeitar.style.display = "block";
    }
    else {
        modalObservacao.style.display = "block";
    }
  }
}

function closeModal() {
  modalAprovar.style.display = "none";
  modalRejeitar.style.display = "none";
  modalObservacao.style.display = "none";
}

function aprovarSim() {
  alert("Requisição aprovada com sucesso!");
  closeModal();
}

function rejeitarSim() {
  alert("Requisição rejeitada com sucesso!");
  closeModal();
}

function adicionarObservacao() {
  alert("Observação adicionada com sucesso!");
  closeModal();
}

// Fecha o modal se o usuário clickar fora dele
window.onclick = function (event) {
  if (event.target == modalAprovar || event.target == modalRejeitar || event.target == modalObservacao) {
    closeModal();
  }
}
