// Validação do campo nome
function validarNome() { 
    if (nome.value.trim() === "") { 
        nome.style.background = "yellow"; 
        alert("Nome não pode estar vazio!"); 
        return false;
    } 
    else {
        nome.style.background = "white"; 
        return true;
    } 
}

// Validação do campo matrícula
function validarMatricula() { 
    var erro = false;
    if (matricula.value.trim() === "") { 
        erro = true;
    } 
    else {
        if (isNaN(matricula.value) === true) { 
            erro = true;
        } else {
            var nQtd = matricula.value; 
            if (!/^\d{5}$/.test(nQtd)) { 
                erro = true;
            } 
        } 
    }

    if (erro === true) { 
        matricula.style.background = "yellow"; 
        alert("A matrícula deve ser númerica e conter 5 caracteres!"); 
    } 
    else { 
        matricula.style.background = "white"; 
    } 
    return (!erro); 
}

// Validação do formulário
function validarForm() {
    erro = false
    erroMatricula = false
    if (nome.value.trim() === "") { 
        nome.style.background = "yellow"; 
        erro = true;
    }
    if (matricula.value.trim() === "") { 
        erro = true;
    } 
    else {
        if (isNaN(matricula.value) === true) { 
            erroMatricula = true;
        } else {
            var nQtd = matricula.value; 
            if (!/^\d{5}$/.test(nQtd)) { 
                erroMatricula = true;
            } 
        } 
    }

    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const mensagemErro = document.getElementById('mensagemErro');
    let isChecked = false;

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            isChecked = true;
        }
    });

    if (!isChecked) {
        mensagemErro.textContent = "Por favor selecione pelo menos 1 curso.";
        erro = true;
    }
    else {
        mensagemErro.textContent = "";
        erro = false;
    }

    var MotivoFalta = document.querySelector('input[name="motivoFalta"]:checked');
    if (MotivoFalta == null) {
        alert("Selecione o motivo da falta!")
        return false;
    }
    
    if (erro == true || erroMatricula == true) {
        alert("Não foi possível enviar o formulário! Reveja se os campos foram preenchidos corretamente.")
        return false;
    }
    else {
        return true
    }
}
