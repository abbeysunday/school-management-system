<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceRecordsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?int $classArmId = null,
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
        private ?string $status = null,
    ) {}

    public function query()
    {
        $query = AttendanceRecord::with(['student.user', 'classArm.classLevel', 'markedBy'])
            ->latest('attendance_date');

        if ($this->classArmId) {
            $query->where('class_arm_id', $this->classArmId);
        }
        if ($this->dateFrom) {
            $query->whereDate('attendance_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('attendance_date', '<=', $this->dateTo);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Date', 'Student Name', 'Admission No', 'Class Arm', 'Status', 'Remarks', 'Marked By', 'Marked At'];
    }

    public function map($record): array
    {
        return [
            $record->attendance_date->format('d M Y'),
            $record->student->user->full_name,
            $record->student->admission_number,
            $record->classArm->full_name ?? 'N/A',
            $record->status,
            $record->remarks ?? '',
            $record->markedBy?->full_name ?? 'System',
            $record->created_at->format('d M Y, h:i A'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a5f2a']]],
        ];
    }
}
