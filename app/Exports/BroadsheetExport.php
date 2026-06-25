<?php

namespace App\Exports;

use App\Models\ClassArm;
use App\Models\Result;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TermSummary;
use App\Services\ResultCalculationService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BroadsheetExport implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private int $armId,
        private int $termId,
        private ResultCalculationService $calcService = new ResultCalculationService()
    ) {}

    public function title(): string
    {
        $arm = ClassArm::find($this->armId);
        return $arm?->full_name ?? 'Broadsheet';
    }

    public function array(): array
    {
        $classArm = ClassArm::with('classLevel')->findOrFail($this->armId);
        $term = Term::findOrFail($this->termId);

        $subjects = Subject::whereIn('id', function ($q) {
            $q->select('subject_id')
              ->from('arm_subjects')
              ->where('class_arm_id', $this->armId);
        })->orderBy('name')->get();

        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_arm_id', $this->armId)
            ->where('term_id', $this->termId)
            ->where('is_active', true)
            ->get()
            ->sortBy('student.user.full_name');

        $results = Result::where('class_arm_id', $this->armId)
            ->where('term_id', $this->termId)
            ->get()
            ->keyBy(fn($r) => $r->student_id . '|' . $r->subject_id);

        $termSummaries = TermSummary::where('class_arm_id', $this->armId)
            ->where('term_id', $this->termId)
            ->get()
            ->keyBy('student_id');

        // Build header
        $header = ['S/N', 'Student Name', 'Admission No'];
        foreach ($subjects as $subject) {
            $header[] = $subject->name;
        }
        $header[] = 'Total';
        $header[] = 'Average';
        $header[] = 'Percentage';
        $header[] = 'Arm Pos';
        $header[] = 'Class Pos';
        $header[] = 'Passed';
        $header[] = 'Failed';

        $data = [$header];

        foreach ($enrollments as $i => $enrollment) {
            $student = $enrollment->student;
            $row = [
                $i + 1,
                $student->user->full_name,
                $student->admission_number,
            ];

            $total = 0;
            $count = 0;
            foreach ($subjects as $subject) {
                $result = $results->get($student->id . '|' . $subject->id);
                if ($result) {
                    $row[] = $result->total_score;
                    $total += (float) $result->total_score;
                    $count++;
                } else {
                    $row[] = '-';
                }
            }

            $summary = $termSummaries->get($student->id);
            $row[] = $total;
            $row[] = $count > 0 ? round($total / $count, 2) : 0;
            $row[] = $summary?->percentage ?? '-';
            $row[] = $summary?->arm_position ?? '-';
            $row[] = $summary?->class_position ?? '-';
            $row[] = $summary?->no_passed ?? 0;
            $row[] = $summary?->no_failed ?? 0;

            $data[] = $row;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1A5F2A']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal('center');

        $sheet->freezePane('A2');

        return [];
    }
}
