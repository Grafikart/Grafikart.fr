<script>
  /**
   * Fichier (retour d'api)
   * @typedef {{id: number, createdAt: number, name: string, size: number, url: string}} ApiFile
   */

  import { pathsToTree } from '@el/filemanager/helpers'
  import {human} from '@fn/size'
  import {jsonFetch} from '@fn/api'
  import {objToSearchParams} from '@fn/url'
  import Folder from './Folder.svelte'

  export let apiEndpoint
  export let dragOver
  export let onSelectFile

  /** @var {ApiFile[]} file **/
  let files = []
  let folders = []
  let error = null
  let filesLoading = true
  let foldersLoading
  let currentFolder = null
  let fileDropper
  let search = ''


  /**
   * Récupère la liste des fichier et l'injecte dans files
   * @param {?string} path
   **/
  async function loadFiles (params) {
    filesLoading = true
    let url = new URL(`${apiEndpoint}/files`, location.href)
    url.search = objToSearchParams(params)
    console.log('URL', url)
    let response = await fetch(url, {
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
    filesLoading = false
  }

  /**
   * Récupère la liste des dossiers et l'injecte dans folders
   **/
  async function loadFolders () {
    foldersLoading = true
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
    foldersLoading = false
  }

  /**
   * Supprime un fichier et met à jour la liste files
   **/
  async function deleteFile (file) {
    jsonFetch(`${apiEndpoint}/${file.id}`, {
      method: 'DELETE'
    }).catch(e => alert(e.detail))
    files = files.filter(f => file !== f)
  }

  /**
   * @param {Folder} folder
   */
  function onFolderClick(folder) {
    currentFolder = folder
    if (folder.children.length === 0) {
      loadFiles({path: folder.path})
    }
  }

  /**
   * @param {CustomEvent} e
   */
  function onNewFile(e) {
    files = [e.detail, ...files]
    fileDropper.value = ''
  }

  function onSearch (e) {
    e.preventDefault()
    loadFiles({q: search})
    currentFolder = null
  }

  loadFiles()
  loadFolders()
</script>

<div class="filemanager" class:has-dragover={dragOver}>
  <input type="text" bind:this={fileDropper} is="input-attachment" on:attachment={onNewFile}>
  <aside>
    <form class="form-group" on:submit={onSearch}>
      <label for="file-search" class="bloc__title">Rechercher</label>
      <input type="search" placeholder="e.g. image.png" id="file-search" name="q" bind:value={search} on:search={onSearch}>
    </form>
    <hr>
    <div class="bloc">
      <div class="bloc__title">Dossiers</div>
      <div class="hierarchy">
        {#if foldersLoading}
          <div class="loader">
            <spinning-dots></spinning-dots>
          </div>
        {:else}
          {#each folders as folder}
            <Folder
              folder={folder}
              currentFolder={currentFolder}
              onSelect={onFolderClick}
            />
          {/each}
        {/if}
      </div>
    </div>
  </aside>
  <main style="position: relative;">
    {#if filesLoading}
    <div class="loader">
      <spinning-dots></spinning-dots>
    </div>
    {:else}
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
      {#each files as file, index (file.id)}
      <tr>
        <td on:click={() => onSelectFile(file)}><img src={file.url} alt={file.alt}></td>
        <td on:click={() => onSelectFile(file)}>{file.name}</td>
        <td>{human(file.size, {locale: 'fr'})}</td>
        <td>
          <button class="delete" on:click={deleteFile(file)}>
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" clip-rule="evenodd" d="M1 0h14c.6 0 1 .4 1 1v14c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1V1c0-.6.4-1 1-1zm9.1 11.5l1.4-1.4L9.4 8l2.1-2.1-1.4-1.4L8 6.6 5.9 4.5 4.5 5.9 6.6 8l-2.1 2.1 1.4 1.4L8 9.4l2.1 2.1z" fill="currentColor"/></svg>
          </button>
        </td>
      </tr>
      {/each}
      </tbody>
    </table>
    {/if}
  </main>
</div>
