/**
 * A simple cache that stores a value invalidated everytime the key changes
 */
export class SimpleCachedValue<T> {
  private keyValue: string | null = null
  private cachedValue: T | null = null

  constructor(
    private key: () => string,
    private value: () => T,
  ) {}

  getValue(): T {
    const newKey = this.key()
    if (newKey === this.keyValue && this.cachedValue !== null) {
      return this.cachedValue
    }

    this.keyValue = newKey
    this.cachedValue = this.value()
    return this.cachedValue
  }
}
