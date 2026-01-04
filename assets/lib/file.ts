const ko = Math.pow(2, 10);

function ceil(n: number, decimals: number) {
  return Math.ceil(n * Math.pow(10, decimals)) / Math.pow(10, decimals);
}

/**
 * Convertit une taille de fichier en valeur humaine
 */
export function humanSize(size: number): string {
  let k = size / ko;
  let unit = "k";
  if (k > ko) {
    k = k / ko;
    unit = "M";
  }
  k = ceil(k, k > 10 ? 0 : 1);
  return `${k}${unit}`;
}
