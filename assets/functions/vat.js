export function vatRate (countryCode) {
  if (countryCode === 'FR') {
    return 0.2
  }
  return 0
}

export function vatPrice (price, countryCode) {
  return Math.floor((price - price / (1 + vatRate(countryCode))) * 100) / 100
}
