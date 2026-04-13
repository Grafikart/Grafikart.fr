<?php

namespace App\Http\Front;

use App\Domains\Coupon\Coupon;
use App\Domains\History\Progress;
use App\Domains\School\Data\SchoolImportData;
use App\Domains\School\Data\SchoolImportRow;
use App\Domains\School\InvalidCSVException;
use App\Domains\School\School;
use App\Domains\School\SchoolImportService;
use App\Domains\School\SchoolRepository;
use App\Http\Front\Data\StudentProgressData;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class SchoolController
{
    public function show(\Illuminate\Http\Request $request, SchoolRepository $repository): View
    {
        $school = $this->school();
        $page = $request->query->getInt('page', 1);

        return view('schools.show', [
            'school' => $school,
            'coupons' => $page === 1 ? $repository->pendingCoupons($school->id) : collect([]),
            'students' => $repository->activeStudents($school->id),
        ]);
    }

    public function import(SchoolImportData $data, SchoolImportService $service): JsonResponse|array
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

    public function student(User $student): View
    {
        $school = $this->school();
        $coupon = Coupon::where('user_id', $student->id)->where('school_id', $school->id)->first();
        if (! $coupon) {
            throw new AccessDeniedException("Cet utilisateur n'est pas un élève de votre école");
        }

        $progress = Progress::query()
            ->where('progressable_type', 'formation')
            ->with('progressable', 'progressable.technologies')
            ->where('user_id', $student->id)
            ->get();

        return view('schools.student', [
            'name' => $student->name,
            'email' => $student->email,
            'school' => $school->name,
            'items' => StudentProgressData::collect($progress),
        ]);

    }
}
