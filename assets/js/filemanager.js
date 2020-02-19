function folderIcon () {
  return `<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 16"><path d="M15.8 7.4c-.2-.2-.5-.4-.8-.4H4c-.4 0-.8.2-.9.6l-3 7c-.2.6 0 1.4.9 1.4h11c.4 0 .8-.2.9-.6l3-7c.2-.3.1-.7-.1-1z" fill="currentColor"/><path d="M1.2 6.8C1.7 5.7 2.8 5 4 5h9V3c0-.6-.4-1-1-1H6.4L4.7.3C4.5.1 4.3 0 4 0H1C.4 0 0 .4 0 1v8.7l1.2-2.9z" fill="currentColor"/></svg>`
}

function arrowIcon () {
  return `<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 10" class="arrow"><path d="M1.196.073A.47.47 0 00.737.048.414.414 0 00.5.417v9.166c0 .155.092.297.238.369a.459.459 0 00.458-.025l7.111-4.584A.41.41 0 008.5 5a.41.41 0 00-.193-.343L1.196.073z" fill="currentColor"/></svg>`
}

function deleteIcon () {
  return `<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" clip-rule="evenodd" d="M1 0h14c.6 0 1 .4 1 1v14c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1V1c0-.6.4-1 1-1zm9.1 11.5l1.4-1.4L9.4 8l2.1-2.1-1.4-1.4L8 6.6 5.9 4.5 4.5 5.9 6.6 8l-2.1 2.1 1.4 1.4L8 9.4l2.1 2.1z" fill="currentColor"/></svg>`
}

class FileManager extends HTMLElement {

  constructor () {
    super()
    const root = this.attachShadow({mode: 'closed'})
    root.innerHTML = `
    ${this.buildStyle()}
    <div class="filemanager">
    <aside>
      <div class="form-group">
        <label for="file-search" class="bloc__title">Rechercher</label>
        <input type="text" placeholder="e.g. image.png" id="file-search" name="q">
      </div>
      <hr>
      <div class="bloc">
        <div class="bloc__title">Dossiers</div>
        <div class="hierarchy">
          <a class="hierarchy__item is-selected has-children" href="#">${arrowIcon()}${folderIcon()}2019</a>
          <a class="hierarchy__item is-deeper" href="#">${arrowIcon()}${folderIcon()}01</a>
          <a class="hierarchy__item is-deeper" href="#">${arrowIcon()}${folderIcon()}02</a>
          <a class="hierarchy__item is-deeper" href="#">${arrowIcon()}${folderIcon()}03</a>
          <a class="hierarchy__item is-deeper" href="#">${arrowIcon()}${folderIcon()}04</a>
          <a class="hierarchy__item" href="#">${arrowIcon()}${folderIcon()}Uploads</a>
        </div>
      </div>
    </aside>
    <main>
      <table cellspacing="0">
        <thead>
        <tr>
          <th class="th-image">Image</th>
          <th>Nom</th>
          <th>Taille</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                  <tr>
            <td><img src="https://picsum.photos/200/300"></td>
            <td>Image.jpg</td>
            <td>612Mo</td>
            <td>
              <a href="#" class="delete">
                ${deleteIcon()}
              </a>
            </td>
          </tr>
                </tbody>
      </table>
    </main>
  </div>
    `
  }

  buildStyle () {
    return `
    <style>
      :host {
        --space: 8px;
        --accent: #457cff;
        --space-4: calc(4 * var(--space));
        --space-3: calc(3 * var(--space));
        --space-2: calc(2 * var(--space));
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
      .filemanager {
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
      input[type=text] {
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
      }
      .hierarchy__item.is-deeper {
        margin-left: var(--space-3);
      }
      .hierarchy__item:hover {
         background-color:#457cff1A;
         color: var(--accent);
         text-decoration: none;
      }
      .hierarchy__item:hover svg {
        color: var(--accent);
      }
      .hierarchy__item.is-selected {
        color: #212944;
      }
      .hierarchy__item.is-selected .arrow {
        transform: rotate(-90deg);
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
      </style>
    `
  }

}

customElements.define('file-manager', FileManager)
