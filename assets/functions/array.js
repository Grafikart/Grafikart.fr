/**
 * Injecte un élément entre chaque élément du tableau
 *
 * @param {unknown[]} arr
 * @param {unknown} element
 * @return {unknown[]}
 */
export function insertBetweenItems(arr, element) {
  if (arr.length <= 1) {
    return arr;
  }

  return arr.reduce((acc, curr, index) => {
    if (index === arr.length - 1) {
      return [...acc, curr];
    } else {
      return [...acc, curr, element];
    }
  }, []);
}
