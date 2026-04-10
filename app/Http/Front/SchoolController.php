<?php

namespace App\Http\Front;

use App\Domains\School\Data\SchoolImportData;
use App\Domains\School\Data\SchoolImportRow;
use App\Domains\School\InvalidCSVException;
use App\Domains\School\School;
use App\Domains\School\SchoolImportService;
use App\Domains\School\SchoolRepository;
use Illuminate\Support\Facades\Auth;

class SchoolController
{
    public function show(\Illuminate\Http\Request $request, SchoolRepository $repository)
    {
        $school = $this->school();
        $page = $request->query->getInt('page', 1);

        return view('schools.show', [
            'school' => $school,
            'coupons' => $page === 1 ? $repository->pendingCoupons($school->id) : collect([]),
            'students' => $repository->activeStudents($school->id),
        ]);
    }

    public function import(SchoolImportData $data, SchoolImportService $service)
    {
        $school = $this->school();
        try {
            $rows = SchoolImportRow::fromCSV($data->csv);
            $months = array_sum(array_map(fn (SchoolImportRow $row) => $row->months, $rows));
            if ($data->confirmed) {
                $service->import($school, $rows, $data->subject, $data->message);

                return [
                    'success' => true,
                ];
            } else {
                return [
                    'count' => count($rows),
                    'months' => $months,
                    'credits' => $school->credits,
                    'left' => $school->credits - $months,
                ];
            }
        } catch (InvalidCSVException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    private function school(): School
    {
        return School::where('user_id', Auth::user()->id)->firstOrFail();
    }
}
