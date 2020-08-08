export default class Skeleton extends HTMLElement {

  constructor () {
    super()
    this.root = this.attachShadow({ mode: 'open' })
  }

  connectedCallback () {
    let extraCss = ''
    const width = this.getAttribute('width')
    const height = this.getAttribute('height')
    const text = this.getAttribute('text')
    if (text) {
      extraCss += `transform: scale(1, 0.60);`
    }
    if (width) {
      extraCss += `width: ${width}px;`
    }
    if (height) {
      extraCss += `height: ${height}px;`
    }
    if (width || height) {
      extraCss += `display: block;`
    }
    if (this.getAttribute('rounded')) {
      extraCss += 'border-radius: 50%;'
    }
    this.root.innerHTML = `<style>
      div {
        display: flex;
      }
      span {
        display: inline-block;
        position: relative;
        height: auto;
        margin-top: 0;
        border-radius: 4px;
        margin-bottom: 0;
        transform-origin: 0 60%;
        background-color: var(--border-light);
        overflow: hidden;
        ${extraCss}
      }
      span::before {
        content: "${this.getAttribute('text')}";
        opacity: 0;
      }
      span::after {
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          content: "";
          position: absolute;
          animation: waves 1.6s linear 0.5s infinite;
          transform: translateX(-100%);
          background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.04), transparent);
      }
      @keyframes waves {
        0% {
          transform: translateX(-100%);
        }
        60% {
          transform: translateX(100%);
        }
        100% {
          transform: translateX(100%);
        }
      }
    </style><div><span></span></div>`
  }

}
