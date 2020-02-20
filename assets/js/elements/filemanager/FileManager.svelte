<script>
  import { pathsToTree } from '@el/filemanager/helpers'

  export let apiEndpoint

  let files = []
  let folders = []
  let error = null

  async function loadFiles () {
    let response = await fetch(`${apiEndpoint}/files`, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      }
    })
    const data = await response.json()
    if (response.ok) {
      files = data
    } else {
      alert(data.error)
    }
  }

  async function loadFolders () {
    let response = await fetch(`${apiEndpoint}/folders`, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      }
    })
    const data = await response.json()
    if (response.ok) {
      folders = pathsToTree(data)
    } else {
      alert(data.error)
    }
  }

  loadFiles()
  loadFolders()
</script>


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
        {#each folders as folder}
          <Folder folder={folder}></Folder>
        {/each}
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
      {#each files as file}
      <tr>
        <td><img src={file.url} alt={file.alt}></td>
        <td>{file.name}</td>
        <td>{human(file.size, {locale: 'fr'})}</td>
        <td>
          <a href="#" class="delete">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" clip-rule="evenodd" d="M1 0h14c.6 0 1 .4 1 1v14c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1V1c0-.6.4-1 1-1zm9.1 11.5l1.4-1.4L9.4 8l2.1-2.1-1.4-1.4L8 6.6 5.9 4.5 4.5 5.9 6.6 8l-2.1 2.1 1.4 1.4L8 9.4l2.1 2.1z" fill="currentColor"/></svg>
          </a>
        </td>
      </tr>
      {/each}
      </tbody>
    </table>
  </main>
</div>
