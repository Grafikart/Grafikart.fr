import { pathsToTree } from '/elements/admin/filemanager/helpers'

test('Convertit des chemins simples en arbre', () => {
  let paths = [
    {
      path: '2020',
      count: 1
    },
    {
      path: '2010',
      count: 2
    }
  ]
  let expected = [
    {
      folder: '2020',
      count: 1,
      children: []
    },
    {
      folder: '2010',
      count: 2,
      children: []
    }
  ]
  expect(pathsToTree(paths)).toMatchObject(expected)
})

test('Convertit des chemins avec 2 profondeurs en arbre', () => {
  let paths = [
    {
      path: '2020',
      count: 1
    },
    {
      path: '2010/23',
      count: 2
    }
  ]
  let expected = [
    {
      folder: '2020',
      count: 1,
      children: []
    },
    {
      folder: '2010',
      count: 2,
      children: [
        {
          folder: '23',
          count: 2,
          children: []
        }
      ]
    }
  ]
  expect(pathsToTree(paths)).toMatchObject(expected)
})

test('Convertit des chemins avec 3 profondeurs en arbre', () => {
  let paths = [
    {
      path: '2020',
      count: 1
    },
    {
      path: '2010/23/23',
      count: 2
    }
  ]
  let expected = [
    {
      folder: '2020',
      count: 1,
      children: []
    },
    {
      folder: '2010',
      count: 2,
      children: [
        {
          folder: '23',
          count: 2,
          children: [
            {
              folder: '23',
              count: 2,
              children: []
            }
          ]
        }
      ]
    }
  ]
  expect(pathsToTree(paths)).toMatchObject(expected)
})

test('Convertit des chemins avec 1 profondeurs en ajoutant le nombre de fichiers', () => {
  let paths = [
    {
      path: '2020',
      count: 1
    },
    {
      path: '2020',
      count: 2
    }
  ]
  let expected = [
    {
      folder: '2020',
      count: 3,
      children: []
    }
  ]
  expect(pathsToTree(paths)).toMatchObject(expected)
})

test('Convertit des chemins avec 2 profondeurs en ajoutant le nombre de fichiers', () => {
  let paths = [
    {
      path: '2020',
      count: 1
    },
    {
      path: '2020/23',
      count: 2
    }
  ]
  let expected = [
    {
      folder: '2020',
      count: 3,
      children: [
        {
          folder: '23',
          count: 2,
          children: []
        }
      ]
    }
  ]
  expect(pathsToTree(paths)).toMatchObject(expected)
})
