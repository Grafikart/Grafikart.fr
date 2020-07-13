import { FileManager as FileManagerComponent } from './FileManager.jsx'
import { h, render } from 'preact'

export default class FileManager extends HTMLElement {
  constructor () {
    super()
    this.root = this.attachShadow({ mode: 'closed' })
    this.root.innerHTML = this.style()
    this.addEventListener('dragenter', this.onDragEnter.bind(this))
    this.addEventListener('dragleave', this.ondragleave.bind(this))
    this.addEventListener('dragover', this.onDragOver)
    this.addEventListener('drop', this.onDrop.bind(this))
    render(
      h(FileManagerComponent, {
        apiEndpoint: '/admin/attachment',
        dragOver: false,
        onSelectFile: this.onSelectFile.bind(this)
      }),
      this.root
    )
  }

  /**
   * @param {{id: number, createdAt: number, name: string, size: number, url: string}} file
   */
  onSelectFile (file) {
    this.dispatchEvent(
      new CustomEvent('file', {
        detail: file
      })
    )
  }

  style () {
    return `<style>
    :host {
      display: block;
      --space: 8px;
      --accent: #457cff;
      --space-4: calc(4 * var(--space));
      --space-3: calc(3 * var(--space));
      --space-2: calc(2 * var(--space));
      --background:
    }
    button {
      cursor: pointer;
      background: transparent;
      border: none;
    }
    ::-webkit-scrollbar {
      width: 5px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
      padding: 1px;
    }

    ::-webkit-scrollbar-thumb {
      background: #c6d0d6;
      border-radius: 4px;
    }

    * {
      box-sizing: border-box;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    a:hover {
      text-decoration: underline;
    }
    .input-attachment {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      opacity: 0;
      background: var(--accent);
      transition: opacity .3s;
      z-index: 5;
      pointer-events: none;
    }
    .has-dragover .input-attachment {
      opacity: .8;
      pointer-events: auto;
    }
    .filemanager {
      position: relative;
      height: 100%;
      box-shadow: 0 1px 4px rgba(212, 212, 212, 0.2);
      border-radius: 8px;
      border: 1px solid #ebeced;
      background-color: #fefefe;
      font-size: 16px;
      display: grid;
      grid-template-columns: 278px 1fr;
    }
    hr {
      border: none;
      margin-top: var(--space-3);
      margin-bottom: var(--space-3);
      height: 1px;
      background-color: #f0f0f6;
    }
    main {
      padding-top: var(--space-4);
      padding-bottom: var(--space-4);
    }
    aside {
      padding: var(--space-4);
      border-right:1px solid #f0f0f6;
    }
    aside, main {
        overflow: auto;
    }
    input[type=text],
    input[type=search] {
      display: block;
      font-size: 16px;
      padding: 0 var(--space-2);
      width: 100%;
      height: 48px;
      box-shadow: 0 1px 1px rgba(212, 212, 212, 0.2);
      border-radius: 6px;
      border: 1px solid #d7dee1;
      background-color: #fefefe;
    }
    .hierarchy .arrow {
      width: 8px;
      height: 12px;
      margin-right: var(--space-2);
    }
    .bloc__title {
      display: block;
      color: #6f6f85;
      margin-bottom: var(--space-2);
      font-size: 14px;
      font-weight: 300;
    }
    .hierarchy {
        min-height: 150px;
        position: relative;
    }
    .hierarchy svg {
      display: block;
      width: 16px;
      height: 16px;
      margin-right: var(--space);
      color: #c6d0d6;
      transition: color .3s;
    }
    .hierarchy__item {
      display: flex;
      align-items: center;
      font-weight: 500;
      height: 36px;
      margin-left: calc(var(--space) * -1);
      margin-right: calc(var(--space) * -1);
      padding: var(--space);
      border-radius: 6px;
      transition: .3s;
      cursor: pointer;
    }
    .hierarchy__item span {
      display: block;
      padding: 0 5px 0 4px;
      margin-left: 12px;
      height: 16px;
      border-radius: 4px;
      background-color: #c6d0d6;
      font-size: 12px;
      transition: .2s;
      transition-property: background, box-shadow;
      font-weight: 600;
    }
    .hierarchy__item.is-deeper {
      margin-left: var(--space-3);
    }
    .hierarchy__item.is-deeper .arrow {
        opacity: 0;
        width: 0;
    }
    .hierarchy__item:hover {
      background-color:#457cff1A;
      color: var(--accent);
      text-decoration: none;
    }
    .hierarchy__item:hover span {
      color: #FFF;
      box-shadow: 0 1px 3px rgba(106, 106, 106, 0.5);
      background-image: linear-gradient(180deg, #457cff 0%, #366ae6 100%);
    }
    .hierarchy__item:hover svg {
      color: var(--accent);
    }
    .hierarchy__item.is-selected {
      color: #212944;
    }
    .hierarchy__item.is-selected .arrow {
      transform: rotate(90deg);
    }
    .hierarchy__item.is-selected svg {
      color: #212944;
    }
    table {
      width: 100%;
    }
    th:first-child, td:first-child {
      padding-left: var(--space-4)!important;
    }
    th:last-child, td:last-child {
      padding-right: var(--space-4)!important;
      text-align: right;
    }
    th {
      text-align: left;
      padding-bottom: var(--space);
      color: #6f6f85;
      font-size: 14px;
      font-weight: 300;
      border: none;
    }
    th.th-image {
      width: 250px;
    }
    th:last-child {
      text-align: right;
    }
    td {
      border: none;
      padding: calc(1.5 * var(--space)) 0;
      padding-right: var(--space-3);
    }
    td:last-child {
      padding-right: 0;
    }
    td img {
      cursor: pointer;
      object-fit: cover;
      width: 250px;
      height: 100px;
      box-shadow: 0 1px 4px rgba(16, 43, 107, 0.6);
      border-radius: 6px;
    }
    tr:hover td {
      background-color: #f8fafb;
    }
    .delete svg {
      width: 16px;
      height: 16px;
      color: #c6d0d6;
      transition: color .3s;
    }
    .delete:hover svg {
      color: #FB4635;
    }
    .loader {
       position:absolute;
       top: 0;
       left: 0;
       right: 0;
       bottom: 0;
       display: flex;
       align-items: center;
       justify-content: center;
       background: #FFFC;
       color: #6f6f85;
    }
    .loader spinning-dots {
      width: 20px;
      height: 20px;
    }
    </style>`
  }

  onDragEnter (e) {
    e.stopPropagation()
    e.preventDefault()
    render(
      h(FileManagerComponent, {
        apiEndpoint: '/admin/attachment',
        dragOver: true,
        onSelectFile: this.onSelectFile.bind(this)
      }),
      this.root
    )
  }

  ondragleave (e) {
    e.stopPropagation()
    e.preventDefault()
    render(
      h(FileManagerComponent, {
        apiEndpoint: '/admin/attachment',
        dragOver: false,
        onSelectFile: this.onSelectFile.bind(this)
      }),
      this.root
    )
  }

  onDragOver (e) {
    e.stopPropagation()
    e.preventDefault()
  }

  onDrop () {
    render(
      h(FileManagerComponent, {
        apiEndpoint: '/admin/attachment',
        dragOver: false,
        onSelectFile: this.onSelectFile.bind(this)
      }),
      this.root
    )
  }
}
