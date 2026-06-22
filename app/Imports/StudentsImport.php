<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\AcademicSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $errorRows = [];

    public function collection(Collection $rows)
    {
        $school = SchoolProfile::current();
        $session = AcademicSession::getCurrent();
        $prefix = ($school->short_name ?? 'SCH') . '/' . $session->start_year . '/';

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +1 for 0-index, +1 for heading row

            // Define validation rules for the row
            $validator = Validator::make($row->toArray(), [
                'first_name' => 'required|string|max:100',
                'last_name'  => 'required|string|max:100',
                'gender'     => 'required|in:Male,Female',
                'email'      => 'nullable|email|unique:users,email',
            ]);

            if ($validator->fails()) {
                $this->errorRows[] = [
                    'row' => $rowNumber,
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'errors' => implode(', ', $validator->errors()->all())
                ];
                continue;
            }

            try {
                DB::transaction(function () use ($row, $prefix) {
                    $user = User::create([
                        'first_name'  => $row['first_name'],
                        'last_name'   => $row['last_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'email'       => $row['email'] ?? null,
                        'password'    => Hash::make(strtolower($row['last_name'])), // Default password
                        'role'        => 'student',
                    ]);

                    $admissionNumber = $this->generateUniqueAdmissionNumber($prefix);

                    $user->student()->create([
                        'admission_number' => $admissionNumber,
                        'gender'           => $row['gender'],
                        'admission_date'   => now(),
                        'status'           => 'Active',
                    ]);
                });

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errorRows[] = [
                    'row' => $rowNumber,
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'errors' => 'Database Error: ' . $e->getMessage()
                ];
            }
        }
    }

    private function generateUniqueAdmissionNumber(string $prefix): string
    {
        // Lock to prevent race conditions during bulk inserts
        $latest = Student::where('admission_number', 'like', $prefix . '%')
                         ->lockForUpdate()
                         ->orderBy('id', 'desc')
                         ->first();

        $sequence = $latest ? ((int) explode('/', $latest->admission_number)[2]) + 1 : 1;

        do {
            $adminNo = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Student::where('admission_number', $adminNo)->exists());

        return $adminNo;
    }
}
