import { Area, AreaChart, CartesianGrid, XAxis } from "recharts";

import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/components/ui/chart.tsx";

type Props<T extends Record<string, unknown>> = {
  data: T[];
  valueKey?: keyof T;
  formatter: (v: T) => string;
};

const EMPTY = {};

/**
 * Linear chart avec une signature plus simple à utiliser pour des graphiques génériques
 */
export function SimpleChart<T extends Record<string, unknown>>(props: Props<T>) {
  return (
    <ChartContainer config={EMPTY} className="aspect-auto h-62.5 w-full">
      <AreaChart data={props.data}>
        <defs>
          <linearGradient id="fillArea" x1="0" y1="0" x2="0" y2="1">
            <stop offset="5%" stopColor="var(--chart-1)" stopOpacity={0.8} />
            <stop offset="95%" stopColor="var(--chart-1)" stopOpacity={0.1} />
          </linearGradient>
        </defs>
        <CartesianGrid vertical={false} />
        <XAxis
          tickLine={false}
          axisLine={false}
          tickMargin={8}
          minTickGap={32}
          tickFormatter={(k) => props.formatter(props.data[k])}
        />
        <ChartTooltip
          cursor={false}
          content={
            <ChartTooltipContent labelFormatter={(_, series) => props.formatter(series[0].payload)} indicator="dot" />
          }
        />
        <Area
          dataKey={(props.valueKey ?? "value") as string}
          type="linear"
          fill="url(#fillArea)"
          stroke="var(--chart-1)"
        />
      </AreaChart>
    </ChartContainer>
  );
}
