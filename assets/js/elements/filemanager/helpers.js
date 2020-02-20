const DS = '/'

/**
 * Représentation d'un dossier
 * @typedef {{folder: string, count: number, children: Folder[]}} Folder
 */

/**
 * Retour d'API
 * @typedef {{path: string, count: number}} Path
 */

/**
 * Normalize la structure des dossiers en renvoyant un arbre
 *
 * @param {Path[]} paths
 * @return {Folder[]}
 */
export function pathsToTree (paths) {

  function pathToObject (path, count, relativePath) {
    if (folderMap[relativePath]) {
      folderMap[relativePath].count += count
      return folderMap[relativePath]
    }
    let folder = {
      folder: path,
      count: count,
      children: []
    }
    folderMap[relativePath] = folder
    return folder
  }

  const folderMap = {}

  return uniq(paths.map(p => {
    if (p.path.includes('/')) {
      const parts = p.path.split('/')
      let folder = pathToObject(parts[0], p.count, parts[0])
      let parent = folder
      for (let i = 1; i < parts.length; i++) {
        let child = pathToObject(parts[i], p.count, parts.slice(0, i + 1).join(DS))
        if (!parent.children.includes(child)) parent.children.push(child)
        parent = child
      }
      return folder
    }
    return pathToObject(p.path, p.count, p.path)
  }))

}

function uniq (arr) {
  return arr.filter((value, index) => arr.indexOf(value) === index)
}
