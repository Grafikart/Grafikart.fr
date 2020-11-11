import { vatPrice } from "/functions/vat.js";

test("Trouve les taux de TVA correctement", () => {
  expect(vatPrice(5, "FR")).toBe(0.83);
  expect(vatPrice(120, "FR")).toBe(20);
  expect(vatPrice(5, "AA")).toBe(0);
  expect(vatPrice(120, "AA")).toBe(0);
});
