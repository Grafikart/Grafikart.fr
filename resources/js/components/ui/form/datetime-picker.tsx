"use client"

import { CalendarIcon, ClockIcon } from "lucide-react"
import * as React from "react"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
import { Separator } from "@/components/ui/separator.tsx"

function parseDatetime(value?: string | null): Temporal.PlainDateTime {
  if (!value) {
    return Temporal.Now.plainDateTimeISO()
  }
  try {
    const instant = Temporal.Instant.from(value)
    return instant
      .toZonedDateTimeISO(Temporal.Now.timeZoneId())
      .toPlainDateTime()
  } catch {
    try {
      return Temporal.PlainDateTime.from(value)
    } catch {
      return Temporal.Now.plainDateTimeISO()
    }
  }
}

function formatAtom(dt: Temporal.PlainDateTime): string {
  return dt
    .toZonedDateTime(Temporal.Now.timeZoneId())
    .toString({ timeZoneName: "never" })
}

type Props = {
  defaultValue?: string | null
  name?: string
}

export function DatetimePicker(props: Props) {
  const [open, setOpen] = React.useState(false)
  const [dateTime, setDateTime] = React.useState<Temporal.PlainDateTime>(() =>
    parseDatetime(props.defaultValue),
  )

  return (
    <div className="flex gap-2 items-center border border-input bg-input dark:bg-input/30 rounded-lg in-[.bg-card]:bg-background/50! h-9">
      <input
        name={props.name}
        value={formatAtom(dateTime)}
        type="hidden"
        className="w-full"
      />
      <Popover open={open} onOpenChange={setOpen}>
        <PopoverTrigger
          render={
            <Button
              variant="ghost"
              id={props.name}
              className="w-max justify-between font-normal"
            />
          }
        >
          <CalendarIcon className="text-muted-foreground size-4" />
          {dateTime.toLocaleString(undefined, {
            year: "numeric",
            month: "long",
            day: "numeric",
          })}
        </PopoverTrigger>
        <PopoverContent className="w-auto overflow-hidden p-0" align="start">
          <Calendar
            mode="single"
            selected={new Date(dateTime.year, dateTime.month - 1, dateTime.day)}
            captionLayout="dropdown"
            onSelect={(selected) => {
              if (selected) {
                setDateTime((dt) =>
                  dt.with({
                    year: selected.getFullYear(),
                    month: selected.getMonth() + 1,
                    day: selected.getDate(),
                  }),
                )
              }
              setOpen(false)
            }}
          />
        </PopoverContent>
      </Popover>
      <Separator orientation="vertical" className="h-3" />
      <ClockIcon className="text-muted-foreground size-4" />
      <input
        type="time"
        id="time-picker"
        step="5"
        onChange={(e) => {
          const time = Temporal.PlainTime.from(e.currentTarget.value)
          setDateTime((dt) =>
            dt.with({ hour: time.hour, minute: time.minute, second: 0 }),
          )
        }}
        defaultValue={dateTime.toPlainTime().toString()}
        className="bg-background appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none field-sizing-content w-max"
      />
    </div>
  )
}
