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
        <ul>
          <a href="PagCoord.html"><li>Lista de Requisições</li></a>
        </ul>
      </div>


      <div class="item item-5">
        <h3>Area do Professor</h3>
        <ul>
          <a href="status.html">
            <li>Status</li>
          </a>
          <l1><a href="justificativa.html">
              <li>Justificativa de Faltas</l1></a>
          <l1><a href="reposicao.html">
              <li>Plano de Reposição</l1></a>
        </ul>
      </div>

      <div class="item item-6"></div>

    </div>

  </footer>

    `;

  }
}

  customElements.define('footer-component', footer);