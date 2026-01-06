"use client";

import * as React from "react";

import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { CalendarIcon, ClockIcon } from "lucide-react";
import { Separator } from "@/components/ui/separator.tsx";

type Props = {
  defaultValue?: string | null;
  name?: string;
};

export function DatetimePicker(props: Props) {
  const [open, setOpen] = React.useState(false);
  const [date, setDate] = React.useState<Date>(new Date(props.defaultValue ?? Date.now()));

  return (
    <div className="flex gap-2 items-center border border-input rounded-lg in-[.bg-card]:bg-background/50! h-9">
      <input name={props.name} value={date.toISOString()} type="hidden" />
      <Popover open={open} onOpenChange={setOpen}>
        <PopoverTrigger
          render={<Button variant="ghost" id={props.name} className="w-max justify-between font-normal" />}
        >
          <CalendarIcon className="text-muted-foreground size-4" />
          {date ? date.toLocaleDateString() : "Selectionner une date"}
        </PopoverTrigger>
        <PopoverContent className="w-auto overflow-hidden p-0" align="start">
          <Calendar
            mode="single"
            selected={date}
            captionLayout="dropdown"
            onSelect={(date) => {
              if (date) {
                setDate(date);
              }
              setOpen(false);
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
          setDate(
            new Date(
              date.getFullYear(),
              date.getMonth(),
              date.getDate(),
              e.currentTarget.valueAsDate?.getHours() ?? 0,
              e.currentTarget.valueAsDate?.getMinutes() ?? 0,
              0,
            ),
          );
        }}
        defaultValue={`${date.getHours().toString().padStart(2, "0")}:${date.getMinutes().toString().padStart(2, "0")}:00`}
        className="bg-background appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none field-sizing-content w-max"
      />
    </div>
  );
}
