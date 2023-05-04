// Recursively builds JSX output adding `<mark>` tags around matches
import {insertBetweenItems} from '/functions/array'

const highlight = (value, indices = [], i = 1) => {
  const pair = indices[indices.length - i];
  return !pair ? value : (
    <>
      {highlight(value.substring(0, pair[0]), indices, i+1)}
      <mark>{value.substring(pair[0], pair[1]+1)}</mark>
      {value.substring(pair[1]+1)}
    </>
  );
};

export const highlightFuse = (result, attribute) => {
  let value = result.item[attribute]

  // Pour un tableau, on highlight chaque élément
  if (Array.isArray(value)) {
    value = [...value]
    const matches = result.matches?.filter(m => m.key === attribute);
    for (const match of matches) {
      value[match.refIndex] = <Fragment key={match.value}>{highlight(match.value, match.indices)}</Fragment>
    }
    return insertBetweenItems(value, ', ')
  }

  // On highlight un élément
  const match = result.matches?.find(m => m.key === attribute);
  return highlight(match?.value ?? value, match?.indices);
};
