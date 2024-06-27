class footer extends HTMLElement {
    constructor() {
      super();
    }
  

  connectedCallback() {
    this.innerHTML = `
        <footer>
    
    <div class="container">

      <div class="item item-1"><a href="index.html"><img src="images/logo_fatec_br.png"></a></div>

      

      <div class="item item-3">
      </div>


      <div class="item item-4">
        <h3>Área do Coordenador</h3>
        
          <a href="PagCoord.html"><p>Lista de Requisições</p></a>
        
      </div>


      <div class="item item-5">
        <h3>Área do Professor</h3>
        
          <a href="status.html"><p>Status</p></a>
          <a href="justificativa.html"><p>Justificativa de Faltas</p></a>
          <a href="reposicao.html"><p>Plano de Reposição</p></a>
        
      </div>

      <div class="item item-6"></div>

    </div>

  </footer>

    `;

  }
}

  customElements.define('footer-component', footer);