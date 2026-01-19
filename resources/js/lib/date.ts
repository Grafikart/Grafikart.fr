const formatter = new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
    timeZone: 'Europe/Paris',
});

const relativeFormatter = new Intl.RelativeTimeFormat('fr-FR');

export function formatDate(date?: string | null): string {
    if (!date) {
        return '-';
    }
    return formatter.format(new Date(date));
}

export function formatRelative(date?: string | null): string {
    if (!date) {
        return '-';
    }

    const now = Date.now();
    const timestamp = new Date(date).getTime();
    const diffInSeconds = Math.round((timestamp - now) / 1000);

    const units: { unit: Intl.RelativeTimeFormatUnit; seconds: number }[] = [
        { unit: 'year', seconds: 31536000 },
        { unit: 'month', seconds: 2592000 },
        { unit: 'week', seconds: 604800 },
        { unit: 'day', seconds: 86400 },
        { unit: 'hour', seconds: 3600 },
        { unit: 'minute', seconds: 60 },
        { unit: 'second', seconds: 1 },
    ];

    for (const { unit, seconds } of units) {
        if (Math.abs(diffInSeconds) >= seconds) {
            const value = Math.round(diffInSeconds / seconds);
            return relativeFormatter.format(value, unit);
        }
    }

    return relativeFormatter.format(0, 'second');
}
