const formatter = new Intl.RelativeTimeFormat("fr", {
  numeric: "auto",
  style: "long",
})

/**
 * Display a relative time using Intl.RelativeTimeFormat
 * Usage: <time-ago time="1770404862">Il y a environ un jour</time-ago>
 */
export class TimeAgo extends HTMLElement {
  private timestamp = 0
  private intervalId: number | null = null

  connectedCallback() {
    const timeAttr = this.getAttribute("time")
    if (!timeAttr) {
      console.error('TimeAgo: missing required "time" attribute')
      return
    }

    this.timestamp = parseInt(timeAttr, 10)
    if (Number.isNaN(this.timestamp)) {
      console.error("TimeAgo: invalid timestamp value")
      return
    }

    // Initial update
    this.updateTime()

    // Update every minute for fresh relative times
    this.intervalId = window.setInterval(() => this.updateTime(), 60000)
  }

  disconnectedCallback() {
    if (this.intervalId !== null) {
      clearInterval(this.intervalId)
      this.intervalId = null
    }
  }

  private updateTime() {
    const now = Date.now()
    const timestampMs = this.timestamp * 1000
    const diffInSeconds = Math.floor((timestampMs - now) / 1000)

    const { value, unit } = this.getRelativeTimeValue(diffInSeconds)
    this.textContent = formatter.format(value, unit)
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
