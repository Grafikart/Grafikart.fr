<?php

namespace App\Domains\Attachment;

use App\Http\Cms\Data\Attachment\FolderData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttachmentRepository
{
    /**
     * @return Collection<FolderData>
     */
    public function findYearsMonths(): Collection
    {
        $driver = DB::getDriverName();

        $pathExpression = match ($driver) {
            'sqlite' => "strftime('%Y/%m', created_at)",
            'pgsql' => "TO_CHAR(created_at, 'YYYY/MM')",
            default => "DATE_FORMAT(created_at, '%Y/%m')",
        };

        return Attachment::query()
            ->select(DB::raw("{$pathExpression} as path"), DB::raw('COUNT(*) as count'))
            ->groupBy('path')
            ->orderByDesc('path')
            ->toBase()
            ->get()
            ->map(
                /** @param object{path: string, count: int} $row */
                fn (object $row) => new FolderData(
                    path: $row->path,
                    count: $row->count,
                )
            );
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function findLatest(int $limit = 25): Collection
    {
        return Attachment::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function findForPath(string $path): Collection
    {
        [$year, $month] = explode('/', $path);

        return Attachment::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function search(string $query, int $limit = 50): Collection
    {
        return Attachment::query()
            ->where('name', 'like', "%{$query}%")
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function orphaned(): Collection
    {
        // TODO: Implement when relationships are defined
        return collect();
    }
}
