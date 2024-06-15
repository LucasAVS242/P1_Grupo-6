class footer extends HTMLElement {
    constructor() {
      super();
    }
  

  connectedCallback() {
    this.innerHTML = `
    <footer>
    </footer>


    `;

  }
}

  customElements.define('footer-component', footer);