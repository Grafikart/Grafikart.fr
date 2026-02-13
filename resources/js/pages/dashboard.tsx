import {
    AlertTriangleIcon,
    BarChart3Icon,
    BellIcon,
    ClockIcon,
    RotateCcwIcon,
    TrashIcon,
} from 'lucide-react';
import type { ReactNode } from 'react';
import { toast } from 'sonner';

import {
    destroy,
    destroyFailed,
    flushFailed,
    retryFailed,
} from '@/actions/App/Http/Cms/JobController.ts';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { ButtonLink } from '@/components/ui/button-link.tsx';
import { Button } from '@/components/ui/button.tsx';
import { Card, CardContent } from '@/components/ui/card.tsx';
import { SimpleChart } from '@/components/ui/simple-chart.tsx';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table.tsx';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs.tsx';
import { apiFetch } from '@/hooks/use-api-fetch.ts';
import { formatDate } from '@/lib/date.ts';
import type { DailyData, JobItemData, MonthlyData } from '@/types';

type Props = {
    months: MonthlyData[];
    days: DailyData[];
    jobs: JobItemData[];
    failedJobs: JobItemData[];
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-6">
                <PageTitle>Dashboard</PageTitle>
                {props.jobs.length > 0 && (
                    <JobsTable
                        type="future"
                        title="Jobs planifiés"
                        icon={<ClockIcon className="text-primary" />}
                        jobs={props.jobs}
                    />
                )}
                {props.failedJobs.length > 0 && (
                    <JobsTable
                        type="failed"
                        title="Jobs échoués"
                        icon={
                            <AlertTriangleIcon className="text-destructive" />
                        }
                        jobs={props.failedJobs}
                    />
                )}
                <RevenueChart months={props.months} days={props.days} />
                <Button
                    variant="secondary"
                    onClick={() => {
                        apiFetch('/cms/dashboard/notifications', {
                            method: 'POST',
                        }).then(() => toast.success('Notification envoyée'));
                    }}
                >
                    <BellIcon />
                    Envoyer une notification de test
                </Button>
            </div>
        );
    },
    {
        breadcrumb: [{ label: 'Dashboard' }],
    },
);

function JobsTable({
    type,
    title,
    icon,
    jobs,
}: {
    type: 'future' | 'failed';
    title: string;
    icon: ReactNode;
    jobs: JobItemData[];
}) {
    return (
        <div>
            <h2 className="mb-4 flex items-center gap-2 text-xl font-semibold">
                {icon}
                {title}
            </h2>

            <Table className="border">
                <TableHeader>
                    <TableRow>
                        <TableHead>Type</TableHead>
                        <TableHead>Message</TableHead>
                        {type === 'failed' && <TableHead>Erreur</TableHead>}
                        <TableHead className="w-50">Date</TableHead>
                        <TableHead className="w-10"></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {jobs.map((job) => (
                        <TableRow key={job.id}>
                            <TableCell className="w-30 font-mono text-xs">
                                {job.type}
                            </TableCell>
                            <TableCell
                                dangerouslySetInnerHTML={{
                                    __html: job.message,
                                }}
                            />
                            {type === 'failed' && (
                                <TableCell>
                                    <div className="max-w-50 text-destructive line-clamp-3 overflow-hidden whitespace-pre-wrap">
                                        {job.exception}
                                    </div>
                                </TableCell>
                            )}
                            <TableCell className="text-muted-foreground">
                                {formatDate(job.date)}
                            </TableCell>
                            <TableCell className="flex justify-end gap-1">
                                {type === 'failed' && (
                                    <ButtonLink
                                        variant="secondary"
                                        size="icon"
                                        href={retryFailed(job.id)}
                                    >
                                        <RotateCcwIcon />
                                    </ButtonLink>
                                )}
                                <ButtonLink
                                    variant="secondary"
                                    size="icon"
                                    href={
                                        type === 'failed'
                                            ? destroyFailed(job.id)
                                            : destroy(job.id)
                                    }
                                    confirm="Voulez-vous vraiment supprimer ce job ?"
                                >
                                    <TrashIcon />
                                </ButtonLink>
                            </TableCell>
                        </TableRow>
                    ))}
                    {type === 'failed' && (
                        <TableRow>
                            <TableCell colSpan={5}>
                                <ButtonLink
                                    href={flushFailed()}
                                    variant="ghost"
                                    className="text-muted-foreground w-full"
                                >
                                    <TrashIcon />
                                    Vider les jobs échoués
                                </ButtonLink>
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        </div>
    );
}

function RevenueChart({ days, months }: Pick<Props, 'days' | 'months'>) {
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
