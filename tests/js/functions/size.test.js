import { human } from '/functions/size'

test('Convertit les kilos octets', () => {
  expect(human(1024)).toBe('1k')
  expect(human(2048)).toBe('2k')
  expect(human(1536)).toBe('1.5k')
  expect(human(343361)).toBe('336k')
  expect(human(4911)).toBe('4.8k')
  expect(human(335421910)).toBe('320M')
})
