const formatter = new Intl.RelativeTimeFormat("fr", {
  numeric: "auto",
  style: "long",
})

/**
 * Display a relative time using Intl.RelativeTimeFormat
 * Usage: <time-ago time="1770404862">Il y a environ un jour</time-ago>
 */
export class TimeAgo extends HTMLElement {
  private _timestamp = 0
  private _intervalId: number | null = null
  private _display: "future" | "always" | "past" = "always"
  private _prefix: string | null = null

  connectedCallback() {
    const timeAttr = this.getAttribute("time")
    if (!timeAttr) {
      console.error('TimeAgo: missing required "time" attribute')
      return
    }

    this._timestamp = parseInt(timeAttr, 10)
    if (Number.isNaN(this._timestamp)) {
      console.error("TimeAgo: invalid timestamp value")
      return
    }

    this._display =
      (this.getAttribute("display") as typeof this._display) ?? "always"
    this._prefix = this.getAttribute("prefix")

    // Update every minute for fresh relative times
    this._intervalId = window.setInterval(() => this.updateTime(), 60000)

    // Initial update
    this.updateTime()
  }

  disconnectedCallback() {
    if (this._intervalId !== null) {
      clearInterval(this._intervalId)
      this._intervalId = null
    }
  }

  private updateTime() {
    const now = Date.now()
    const timestampMs = this._timestamp * 1000
    const diffInSeconds = Math.floor((timestampMs - now) / 1000)

    // Toggle visibility of the element depending on the display condition
    if (this._display === "future" && diffInSeconds < 0) {
      this.setAttribute("hidden", "")
      this.remove()
    } else if (this._display === "past" && diffInSeconds > 0) {
      this.setAttribute("hidden", "")
      this.remove()
    } else {
      this.removeAttribute("hidden")
    }

    const { value, unit } = this.getRelativeTimeValue(diffInSeconds)
    let text = formatter.format(value, unit)
    if (this._prefix) {
      text = `${this._prefix} ${text}`
    }
    this.textContent = text
  }

  private getRelativeTimeValue(diffInSeconds: number): {
    value: number
    unit: Intl.RelativeTimeFormatUnit
  } {
    const absDiff = Math.abs(diffInSeconds)

    // Seconds (less than 1 minute)
    if (absDiff < 60) {
      return { value: diffInSeconds, unit: "second" }
    }

    // Minutes (less than 1 hour)
    const diffInMinutes = Math.floor(diffInSeconds / 60)
    if (absDiff < 3600) {
      return { value: diffInMinutes, unit: "minute" }
    }

    // Hours (less than 1 day)
    const diffInHours = Math.floor(diffInSeconds / 3600)
    if (absDiff < 86400) {
      return { value: diffInHours, unit: "hour" }
    }

    // Days (less than 1 week)
    const diffInDays = Math.floor(diffInSeconds / 86400)
    if (absDiff < 604800) {
      return { value: diffInDays, unit: "day" }
    }

    // Weeks (less than 1 month - 4 weeks)
    const diffInWeeks = Math.floor(diffInSeconds / 604800)
    if (absDiff < 2592000) {
      return { value: diffInWeeks, unit: "week" }
    }

    // Months (less than 1 year)
    const diffInMonths = Math.floor(diffInSeconds / 2592000)
    if (absDiff < 31536000) {
      return { value: diffInMonths, unit: "month" }
    }

    // Years
    const diffInYears = Math.floor(diffInSeconds / 31536000)
    return { value: diffInYears, unit: "year" }
  }
}
