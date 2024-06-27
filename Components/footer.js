class footer extends HTMLElement {
    constructor() {
      super();
    }
  

  connectedCallback() {
    this.innerHTML = `
        <footer>
    
    <div class="container">

      <div class="item item-1"> <img src="images/logo_fatec_br.png"> </div>

      

      <div class="item item-3"><a href="index.html">
          <h3>Inicio</h3>
        </a>
      </div>


      <div class="item item-4">
        <h3>Área do Coordenador</h3>
        
          <a href="PagCoord.html">Lista de Requisições</a>
        
      </div>


      <div class="item item-5">
        <h3>Area do Professor</h3>
        
          <a href="status.html">Status</a><br>
          <a href="justificativa.html">Justificativa de Faltas</a><br>
          <a href="reposicao.html">Plano de Reposição</a>
        
      </div>

      <div class="item item-6"></div>

    </div>

  </footer>

    `;

  }
}

  customElements.define('footer-component', footer);