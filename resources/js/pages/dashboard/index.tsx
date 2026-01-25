import { BarChart3Icon } from 'lucide-react';

import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Card, CardContent } from '@/components/ui/card';
import { SimpleChart } from '@/components/ui/simple-chart.tsx';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { DailyData, MonthlyData } from '@/types';

type Props = {
    months: MonthlyData[];
    days: DailyData[];
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-6">
                <PageTitle>Dashboard</PageTitle>
                <RevenueChart months={props.months} days={props.days} />
            </div>
        );
    },
    {
        breadcrumb: [{ label: 'Dashboard' }],
    },
);

function RevenueChart({ days, months }: Props) {
    return (
        <Tabs>
            <div className="flex items-center justify-between">
                <h1 className="mb-4 flex items-center gap-2 text-xl font-semibold">
                    <BarChart3Icon className="text-primary" />
                    Revenus
                </h1>
                <TabsList>
                    <TabsTrigger value="month">30 derniers jours</TabsTrigger>
                    <TabsTrigger value="year">24 derniers mois</TabsTrigger>
                </TabsList>
            </div>
            <Card>
                <CardContent className="py-0">
                    <TabsContent value="month">
                        <SimpleChart
                            data={days}
                            formatter={(v) => {
                                return new Date(v.date).toLocaleDateString(
                                    'fr-FR',
                                    {
                                        month: 'short',
                                        day: 'numeric',
                                    },
                                );
                            }}
                        />
                    </TabsContent>
                    <TabsContent value="year">
                        <SimpleChart
                            data={months}
                            formatter={(v) => {
                                return new Date(
                                    v.year,
                                    v.month - 1,
                                    10,
                                ).toLocaleDateString('fr-FR', {
                                    month: 'long',
                                });
                            }}
                        />
                    </TabsContent>
                </CardContent>
            </Card>
        </Tabs>
    );
}
