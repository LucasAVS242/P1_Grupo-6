class tabela extends HTMLElement {
    constructor() {
      super();
    }
  

  connectedCallback() {
    this.innerHTML = `
     <select name="tabela" id="tabela">
                    <option value=""></option>
                    <option value="X">X</option>
                    <option value="HAE">HAE</option>
                    <option value="R">R</option>
                </select>



    `;

  }
}

  customElements.define('tabela-component', tabela);
