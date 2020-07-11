import { classNames } from '/functions/dom.js'
import { useRef, useState } from 'preact/hooks'
import { Loader } from '/components/Loader.jsx'
import { useAsyncEffect } from '/functions/hooks.js'
import { jsonFetch } from '/functions/api.js'
import { human } from '/functions/size.js'
import { pathsToTree } from './helpers'
import { objToSearchParams } from '/functions/url.js'

/**
 * Fichier (retour d'api)
 * @typedef {{id: number, createdAt: number, name: string, size: number, url: string}} ApiFile
 */

export function FileManager ({ dragOver, apiEndpoint, onSelectFile }) {
  const searchInput = useRef(null)
  const [folders, setFolders] = useState(null)
  const [files, setFiles] = useState(null)
  const [currentFolder, setCurrentFolder] = useState(null)
  const handleNewfile = e => {
    console.log('----', e)
    setFiles(files => [e.detail, ...files])
  }
  const handleSearch = e => {
    e.preventDefault()
    loadFiles({ q: searchInput.current.value })
  }
  const handleSelectFolder = folder => {
    if (currentFolder === folder) {
      return
    }
    setCurrentFolder(folder)
    if (folder.children.length === 0) {
      loadFiles({ path: folder.path })
    }
  }
  const handleDelete = async file => {
    await jsonFetch(`${apiEndpoint}/${file.id}`, { method: 'DELETE' })
    setFiles(files => files.filter(f => file !== f))
  }

  const loadFiles = async params => {
    setFiles(null)
    const url = new URL(`${apiEndpoint}/files`, location.href)
    url.search = objToSearchParams(params)
    const files = await jsonFetch(url)
    setFiles(files)
  }

  useAsyncEffect(async () => {
    const folders = await jsonFetch(`${apiEndpoint}/folders`)
    setFolders(pathsToTree(folders))
  }, [])

  useAsyncEffect(async () => {
    const files = await jsonFetch(`${apiEndpoint}/files`)
    setFiles(files)
  }, [])

  return (
    <div className={classNames('filemanager', dragOver && 'has-dragover')}>
      <input type='text' is='input-attachment' onattachment={handleNewfile} />
      <aside>
        <form onSubmit={handleSearch} className='form-group'>
          <label htmlFor='file-search' className='bloc__title'>
            Rechercher
          </label>
          <input type='search' placeholder='e.g. image.png' id='file-search' name='q' ref={searchInput} />
        </form>
        <hr />
        <div className='bloc'>
          <div className='bloc__title'>Dossiers</div>
          <div className='hierarchy'>
            {folders === null ? (
              <div className='loader'>
                <Loader />
              </div>
            ) : (
              folders.map(folder => (
                <Folder key={folder} folder={folder} currentFolder={currentFolder} onSelect={handleSelectFolder} />
              ))
            )}
          </div>
        </div>
      </aside>
      <main className='relative'>
        {files === null ? (
          <div className='loader'>
            <Loader />
          </div>
        ) : (
          <table cellSpacing='0'>
            <thead>
              <tr>
                <th className='th-image'>Image</th>
                <th>Nom</th>
                <th>Taille</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {files.map(file => (
                <File key={file} file={file} onDelete={handleDelete} onSelect={onSelectFile} />
              ))}
            </tbody>
          </table>
        )}
      </main>
    </div>
  )
}

/**
 * Représente un fichier
 *
 * @param {{file: ApiFile, onSelect: function}} props
 */
function File ({ file, onSelect, onDelete }) {
  return (
    <tr>
      <td onClick={() => onSelect(file)}>
        <img src={file.url} alt={file.alt} loading='lazy' />
      </td>
      <td onClick={() => onSelect(file)}>{file.name}</td>
      <td>{human(file.size)}</td>
      <td>
        <button class='delete' onClick={() => onDelete(file)}>
          <svg fill='none' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'>
            <path
              fillRule='evenodd'
              clipRule='evenodd'
              d='M1 0h14c.6 0 1 .4 1 1v14c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1V1c0-.6.4-1 1-1zm9.1 11.5l1.4-1.4L9.4 8l2.1-2.1-1.4-1.4L8 6.6 5.9 4.5 4.5 5.9 6.6 8l-2.1 2.1 1.4 1.4L8 9.4l2.1 2.1z'
              fill='currentColor'
            />
          </svg>
        </button>
      </td>
    </tr>
  )
}

/**
 * Représente un dossier
 */
function Folder ({ folder, onSelect, currentFolder }) {
  const isSelected = currentFolder && currentFolder.path.startsWith(folder.path)
  const hasChildren = folder.children && folder.children.length > 0
  return (
    <>
      <div
        className={classNames(
          'hierarchy__item',
          hasChildren ? 'has-children' : 'is-deeper',
          isSelected && 'is-selected'
        )}
        onClick={() => onSelect(folder)}
      >
        <svg fill='none' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 9 10' class='arrow'>
          <path
            d='M1.196.073A.47.47 0 00.737.048.414.414 0 00.5.417v9.166c0 .155.092.297.238.369a.459.459 0 00.458-.025l7.111-4.584A.41.41 0 008.5 5a.41.41 0 00-.193-.343L1.196.073z'
            fill='currentColor'
          />
        </svg>
        <svg fill='none' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 17 16'>
          <path
            d='M15.8 7.4c-.2-.2-.5-.4-.8-.4H4c-.4 0-.8.2-.9.6l-3 7c-.2.6 0 1.4.9 1.4h11c.4 0 .8-.2.9-.6l3-7c.2-.3.1-.7-.1-1z'
            fill='currentColor'
          />
          <path
            d='M1.2 6.8C1.7 5.7 2.8 5 4 5h9V3c0-.6-.4-1-1-1H6.4L4.7.3C4.5.1 4.3 0 4 0H1C.4 0 0 .4 0 1v8.7l1.2-2.9z'
            fill='currentColor'
          />
        </svg>
        {folder.folder}
        <span>{folder.count}</span>
      </div>
      {isSelected &&
        folder.children.map(child => (
          <Folder key={folder} folder={child} onSelect={onSelect} currentFolder={currentFolder} />
        ))}
    </>
  )
}
