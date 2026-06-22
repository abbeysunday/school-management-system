<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\AttendanceRecord;
use App\Models\CaConfiguration;
use App\Models\CaScore;
use App\Models\ClassArm;
use App\Models\ClassLevel;
use App\Models\ExamScore;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\GradingScale;
use App\Models\ParentStudent;
use App\Models\Result;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeLedger;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassArmTeacher;
use App\Models\TeacherArmSubject;
use App\Models\Term;
use App\Models\TermSummary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Seeds a complete demo school — Excellence Academy Lagos.
     *
     * Use this for:
     *   - Showcasing the system to prospective school clients
     *   - Internal testing during development
     *   - QA / feature testing
     *
     * Run AFTER production seeders:
     *   php artisan db:seed --class=DatabaseSeeder
     *   php artisan db:seed --class=DemoSeeder
     *
     * What gets created:
     *   - School profile filled with demo data
     *   - Session: 2024/2025 — all 3 terms (Third Term as current)
     *   - 6 class levels × 2 arms each = 12 class arms
     *   - 10 subjects assigned to each arm
     *   - 1 admin, 1 principal, 1 bursar
     *   - 8 teachers with subject assignments
     *   - 60 students spread across all arms (5 per arm)
     *   - 30 parent accounts (some parents have 2 children)
     *   - CA scores, exam scores, computed results for First Term
     *   - Term summaries with positions and grades
     *   - 30 days of attendance records for First Term
     *   - Fee structures and payment records (mix of paid/partial/unpaid)
     *
     * Login credentials seeded:
     *   Admin    → admin@demo.school   / password
     *   Teacher  → teacher1@demo.school / password (and teacher2–8)
     *   Parent   → parent1@demo.school / password (and parent2–30)
     *   Student  → student logins use admission numbers as passwords
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting DemoSeeder for Excellence Academy Lagos...');

        DB::transaction(function () {
            $this->seedSchoolProfile();
            $session = $this->seedSession();
            [$term1, $term2, $term3] = $this->seedTerms($session);
            $arms    = $this->seedClassArms();
            $subjects = $this->pickDemoSubjects();
            $armSubjects = $this->seedArmSubjects($arms, $subjects, $session);
            $staff   = $this->seedStaff();
            $this->seedTeacherAssignments($staff['teachers'], $arms, $armSubjects, $session);
            $students = $this->seedStudents($arms, $session, $term1);
            $this->seedParents($students);
            $this->seedFeeStructures($session, $term1, $term2, $term3);
            $this->seedStudentLedgersAndPayments($students, $term1);
            $this->seedCaAndExamScores($students, $subjects, $arms, $term1, $session);
            $this->seedResults($students, $subjects, $arms, $term1);
            $this->seedTermSummaries($students, $arms, $term1);
            $this->seedAttendance($students, $arms, $term1);
        });

        $this->command->info('✅ DemoSeeder complete! Login: admin@demo.school / password');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedSchoolProfile(): void
    {
        SchoolProfile::updateOrCreate(['id' => 1], [
            'name'               => 'Excellence Academy',
            'short_name'         => 'EA',
            'address'            => '14 Bode Thomas Street, Surulere, Lagos',
            'motto'              => 'Knowledge, Character, Excellence',
            'phone'              => '08012345678',
            'email'              => 'admin@excellenceacademy.edu.ng',
            'website'            => 'www.excellenceacademy.edu.ng',
            'principal_name'     => 'Mr. Adewale Okonkwo',
            'waec_centre_number' => 'LA10245',
            'neco_centre_number' => 'NEC/LA/0034',
            'state'              => 'Lagos',
            'lga'                => 'Surulere',
            'city'               => 'Lagos',
            'ca_weight'          => 30,
            'exam_weight'        => 70,
            'currency_symbol'    => '₦',
            'timezone'           => 'Africa/Lagos',
            'sms_on_absence'     => true,
            'sms_on_payment'     => true,
            'sms_on_result_publish' => true,
        ]);
        $this->command->line('  ✓ School profile: Excellence Academy, Lagos');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedSession(): AcademicSession
    {
        $session = AcademicSession::updateOrCreate(
            ['name' => '2024/2025'],
            ['start_year' => 2024, 'end_year' => 2025, 'is_current' => true]
        );
        $this->command->line('  ✓ Session: 2024/2025 (current)');
        return $session;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedTerms(AcademicSession $session): array
    {
        $term1 = Term::updateOrCreate(
            ['session_id' => $session->id, 'name' => 'First Term'],
            [
                'start_date'              => '2024-09-09',
                'end_date'                => '2024-12-13',
                'mid_term_break_start'    => '2024-10-28',
                'mid_term_break_end'      => '2024-11-01',
                'next_resumption_date'    => '2025-01-06',
                'total_school_days'       => 67,
                'is_current'              => false,
                'results_published'       => true,
            ]
        );

        $term2 = Term::updateOrCreate(
            ['session_id' => $session->id, 'name' => 'Second Term'],
            [
                'start_date'              => '2025-01-06',
                'end_date'                => '2025-03-28',
                'mid_term_break_start'    => '2025-02-17',
                'mid_term_break_end'      => '2025-02-21',
                'next_resumption_date'    => '2025-04-07',
                'total_school_days'       => 59,
                'is_current'              => false,
                'results_published'       => false,
            ]
        );

        $term3 = Term::updateOrCreate(
            ['session_id' => $session->id, 'name' => 'Third Term'],
            [
                'start_date'              => '2025-04-07',
                'end_date'                => '2025-07-11',
                'mid_term_break_start'    => '2025-05-26',
                'mid_term_break_end'      => '2025-05-30',
                'next_resumption_date'    => '2025-09-08',
                'total_school_days'       => 62,
                'is_current'              => true,
                'results_published'       => false,
            ]
        );

        $this->command->line('  ✓ Terms: First (published), Second, Third Term (current)');
        return [$term1, $term2, $term3];
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedClassArms(): array
    {
        $levels = ClassLevel::all()->keyBy('name');
        $armDefs = [
            'JSS1' => ['A', 'B'],
            'JSS2' => ['A', 'B'],
            'JSS3' => ['A', 'B'],
            'SS1'  => ['Science', 'Commercial'],
            'SS2'  => ['Science', 'Commercial'],
            'SS3'  => ['Science', 'Commercial'],
        ];

        $arms = [];
        foreach ($armDefs as $levelName => $armLetters) {
            $level = $levels->get($levelName);
            foreach ($armLetters as $arm) {
                $arms[] = ClassArm::updateOrCreate(
                    ['class_level_id' => $level->id, 'arm' => $arm],
                    ['capacity' => 40]
                );
            }
        }

        $this->command->line('  ✓ Class arms: JSS1A, JSS1B, JSS2A, JSS2B, JSS3A, JSS3B, SS1 Science, SS1 Commercial, SS2 Science, SS2 Commercial, SS3 Science, SS3 Commercial');
        return $arms;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function pickDemoSubjects(): array
    {
        // 10 subjects used for demo (a mix of core + electives)
        return Subject::whereIn('code', [
            'ENG', 'MTH', 'BIO', 'CHE', 'PHY',
            'ECO', 'ACC', 'LIT', 'GOV', 'CST',
        ])->get()->all();
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedArmSubjects(array $arms, array $subjects, AcademicSession $session): array
    {
        $armSubjects = [];
        foreach ($arms as $arm) {
            foreach ($subjects as $subject) {
                $armSubjects[] = ArmSubject::updateOrCreate(
                    ['class_arm_id' => $arm->id, 'subject_id' => $subject->id, 'session_id' => $session->id],
                    []
                );
            }
        }
        $this->command->line('  ✓ Arm-subjects: 10 subjects assigned to all 12 arms');
        return $armSubjects;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedStaff(): array
    {
        // Admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@demo.school'],
            [
                'first_name' => 'System',
                'last_name'  => 'Admin',
                'password'   => Hash::make('    '),
                'role'       => 'admin',
                'is_active'  => true,
            ]
        );

        // Principal
        $principalUser = User::updateOrCreate(
            ['email' => 'principal@demo.school'],
            [
                'first_name' => 'Adewale',
                'last_name'  => 'Okonkwo',
                'password'   => Hash::make('password'),
                'role'       => 'principal',
                'is_active'  => true,
            ]
        );

        // Bursar
        User::updateOrCreate(
            ['email' => 'bursar@demo.school'],
            [
                'first_name' => 'Ngozi',
                'last_name'  => 'Eze',
                'password'   => Hash::make('password'),
                'role'       => 'bursar',
                'is_active'  => true,
            ]
        );

        // 8 Teachers
        $teacherData = [
            ['first_name' => 'Chukwuemeka', 'last_name' => 'Nwosu',    'email' => 'teacher1@demo.school', 'specialization' => 'Mathematics'],
            ['first_name' => 'Fatima',      'last_name' => 'Suleiman', 'email' => 'teacher2@demo.school', 'specialization' => 'English Language'],
            ['first_name' => 'Babatunde',   'last_name' => 'Adeyemi',  'email' => 'teacher3@demo.school', 'specialization' => 'Biology & Chemistry'],
            ['first_name' => 'Amaka',       'last_name' => 'Obi',      'email' => 'teacher4@demo.school', 'specialization' => 'Physics'],
            ['first_name' => 'Idris',       'last_name' => 'Musa',     'email' => 'teacher5@demo.school', 'specialization' => 'Economics'],
            ['first_name' => 'Chidinma',    'last_name' => 'Agu',      'email' => 'teacher6@demo.school', 'specialization' => 'Accounting'],
            ['first_name' => 'Seun',        'last_name' => 'Adebayo',  'email' => 'teacher7@demo.school', 'specialization' => 'Literature & Government'],
            ['first_name' => 'Yusuf',       'last_name' => 'Ibrahim',  'email' => 'teacher8@demo.school', 'specialization' => 'Computer Studies'],
        ];

        $teachers = [];
        foreach ($teacherData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'password'   => Hash::make('password'),
                    'role'       => 'teacher',
                    'is_active'  => true,
                ]
            );
            $teachers[] = Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'staff_id'         => 'STF/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'gender'           => $index % 2 === 0 ? 'Male' : 'Female',
                    'qualification'    => 'B.Sc. Education',
                    'specialization'   => $data['specialization'],
                    'employment_date'  => '2020-09-01',
                    'employment_type'  => 'Full-time',
                ]
            );
        }

        $this->command->line('  ✓ Staff: 1 admin, 1 principal, 1 bursar, 8 teachers');
        return ['teachers' => $teachers, 'admin' => $adminUser];
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedTeacherAssignments(array $teachers, array $arms, array $armSubjects, AcademicSession $session): void
    {
        // Assign teachers as Form Teachers to the first 8 arms
        foreach (array_slice($arms, 0, 8) as $index => $arm) {
            ClassArmTeacher::updateOrCreate(
                ['class_arm_id' => $arm->id, 'session_id' => $session->id, 'role' => 'Form Teacher'],
                ['teacher_id' => $teachers[$index % count($teachers)]->id]
            );
        }

        // Assign each teacher to a subset of arm-subjects
        // Teacher 0 → Maths, Teacher 1 → English, etc.
        $subjectMap = ['MTH', 'ENG', 'BIO', 'PHY', 'ECO', 'ACC', 'LIT', 'CST'];
        $subjects = Subject::whereIn('code', $subjectMap)->get()->keyBy('code');

        foreach ($armSubjects as $armSubject) {
            $subject = $subjects->first(fn($s) => $s->id === $armSubject->subject_id);
            if (!$subject) continue;
            $teacherIndex = array_search($subject->code, $subjectMap);
            if ($teacherIndex === false) continue;
            TeacherArmSubject::updateOrCreate(
                ['teacher_id' => $teachers[$teacherIndex % count($teachers)]->id, 'arm_subject_id' => $armSubject->id],
                []
            );
        }

        $this->command->line('  ✓ Teacher assignments: Form Teachers set, subjects distributed');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedStudents(array $arms, AcademicSession $session, Term $term1): array
    {
        $nigerianFirstNames = [
            'Adaeze', 'Chukwuemeka', 'Fatima', 'Babatunde', 'Ngozi', 'Emeka',
            'Aisha', 'Oluwaseun', 'Chidinma', 'Idris', 'Amaka', 'Seun',
            'Yusuf', 'Oluwatobi', 'Precious', 'Chidi', 'Blessing', 'Abdullahi',
            'Ifeoma', 'Olumide', 'Hadiza', 'Tochukwu', 'Funmilayo', 'Musa',
        ];
        $nigerianLastNames = [
            'Okonkwo', 'Adeyemi', 'Suleiman', 'Nwosu', 'Ibrahim', 'Bello',
            'Eze', 'Afolabi', 'Musa', 'Chukwu', 'Obi', 'Adewale',
            'Garba', 'Agu', 'Lawal', 'Okeke', 'Yusuf', 'Olawale',
        ];
        $states = ['Lagos', 'Ogun', 'Oyo', 'Rivers', 'Anambra', 'Kano', 'Enugu', 'Delta'];
        $religions = ['Christianity', 'Islam', 'Christianity', 'Christianity', 'Islam'];
        $genders = ['Male', 'Female', 'Male', 'Female', 'Male'];

        $students = [];
        $admissionCounter = 1;

        foreach ($arms as $arm) {
            for ($i = 1; $i <= 5; $i++) {
                $firstName = $nigerianFirstNames[array_rand($nigerianFirstNames)];
                $lastName  = $nigerianLastNames[array_rand($nigerianLastNames)];
                $gender    = $genders[$i % count($genders)];
                $admNo     = 'ADM/2024/' . str_pad($admissionCounter++, 3, '0', STR_PAD_LEFT);

                $user = User::updateOrCreate(
                    ['email' => strtolower($admNo . '@demo.school')],
                    [
                        'first_name' => $firstName,
                        'last_name'  => $lastName,
                        'phone'      => '0801' . rand(1000000, 9999999),
                        'password'   => Hash::make('password'),
                        'role'       => 'student',
                        'is_active'  => true,
                    ]
                );

                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'admission_number' => $admNo,
                        'date_of_birth'    => now()->subYears(rand(11, 18))->subDays(rand(0, 365))->toDateString(),
                        'gender'           => $gender,
                        'religion'         => $religions[array_rand($religions)],
                        'state_of_origin'  => $states[array_rand($states)],
                        'admission_date'   => '2024-09-09',
                        'status'           => 'Active',
                    ]
                );

                // Enroll in First Term
                StudentEnrollment::updateOrCreate(
                    ['student_id' => $student->id, 'term_id' => $term1->id],
                    [
                        'class_arm_id'    => $arm->id,
                        'session_id'      => $session->id,
                        'enrollment_date' => '2024-09-09',
                        'is_active'       => true,
                    ]
                );

                $students[] = ['student' => $student, 'arm' => $arm];
            }
        }

        $this->command->line('  ✓ Students: ' . count($students) . ' students across 12 arms (5 per arm)');
        return $students;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedParents(array $students): void
    {
        $parentCounter = 1;
        foreach ($students as $index => $data) {
            // Every 2 students share a parent (simulating siblings)
            if ($index % 2 === 0) {
                $parentUser = User::updateOrCreate(
                    ['email' => 'parent' . $parentCounter . '@demo.school'],
                    [
                        'first_name' => 'Parent',
                        'last_name'  => 'Number' . $parentCounter,
                        'phone'      => '0802' . rand(1000000, 9999999),
                        'password'   => Hash::make('password'),
                        'role'       => 'parent',
                        'is_active'  => true,
                    ]
                );
                $parentCounter++;
            }

            ParentStudent::updateOrCreate(
                ['parent_user_id' => $parentUser->id, 'student_id' => $data['student']->id],
                ['relationship' => $index % 4 === 0 ? 'Father' : 'Mother', 'is_primary_contact' => true]
            );
        }
        $this->command->line("  ✓ Parents: {$parentCounter} parent accounts, some linked to 2 children");
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedFeeStructures(AcademicSession $session, Term $term1, Term $term2, Term $term3): void
    {
        $categories = FeeCategory::whereIn('name', [
            'Tuition Fee', 'PTA Levy', 'Book Levy',
        ])->get()->keyBy('name');

        $levels = ClassLevel::all();

        // Fees per class level (JSS classes cheaper than SS classes)
        $tuitionAmounts = [
            'JSS1' => 45000, 'JSS2' => 45000, 'JSS3' => 48000,
            'SS1'  => 55000, 'SS2'  => 55000, 'SS3'  => 60000,
        ];

        foreach ($levels as $level) {
            foreach ([$term1, $term2, $term3] as $term) {
                // Tuition
                if ($tuition = $categories->get('Tuition Fee')) {
                    FeeStructure::updateOrCreate(
                        ['fee_category_id' => $tuition->id, 'class_level_id' => $level->id, 'term_id' => $term->id],
                        ['session_id' => $session->id, 'amount' => $tuitionAmounts[$level->name] ?? 50000, 'due_date' => $term->start_date]
                    );
                }
                // PTA
                if ($pta = $categories->get('PTA Levy')) {
                    FeeStructure::updateOrCreate(
                        ['fee_category_id' => $pta->id, 'class_level_id' => $level->id, 'term_id' => $term->id],
                        ['session_id' => $session->id, 'amount' => 3000, 'due_date' => $term->start_date]
                    );
                }
                // Book
                if ($book = $categories->get('Book Levy')) {
                    FeeStructure::updateOrCreate(
                        ['fee_category_id' => $book->id, 'class_level_id' => $level->id, 'term_id' => $term->id],
                        ['session_id' => $session->id, 'amount' => 8000, 'due_date' => $term->start_date]
                    );
                }
            }
        }

        $this->command->line('  ✓ Fee structures: Tuition, PTA, Book Levy for all 6 levels × 3 terms');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedStudentLedgersAndPayments(array $students, Term $term1): void
    {
        $feeStructures = FeeStructure::where('term_id', $term1->id)->get();
        $statuses = ['Paid', 'Paid', 'Partial', 'Unpaid', 'Paid']; // 60% fully paid, 20% partial, 20% unpaid

        foreach ($students as $index => $data) {
            $student = $data['student'];
            $arm     = $data['arm'];
            $level   = $arm->classLevel;

            // Get fee structures for this student's class level
            $levelStructures = $feeStructures->where('class_level_id', $level->id);
            $paymentStatus   = $statuses[$index % count($statuses)];

            foreach ($levelStructures as $structure) {
                $netAmount   = $structure->amount;
                $amountPaid  = match($paymentStatus) {
                    'Paid'    => $netAmount,
                    'Partial' => round($netAmount * 0.5),
                    'Unpaid'  => 0,
                };
                $ledgerStatus = match($paymentStatus) {
                    'Paid'    => 'Paid',
                    'Partial' => 'Partial',
                    'Unpaid'  => 'Unpaid',
                };

                StudentFeeLedger::updateOrCreate(
                    ['student_id' => $student->id, 'fee_structure_id' => $structure->id, 'term_id' => $term1->id],
                    [
                        'original_amount' => $netAmount,
                        'discount_amount' => 0,
                        'net_amount'      => $netAmount,
                        'amount_paid'     => $amountPaid,
                        'status'          => $ledgerStatus,
                    ]
                );
            }
        }

        $this->command->line('  ✓ Fee ledgers: generated for all students (mix of Paid/Partial/Unpaid)');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedCaAndExamScores(array $students, array $subjects, array $arms, Term $term1, AcademicSession $session): void
    {
        $caConfigs = CaConfiguration::where('is_active', true)->get();

        foreach ($students as $data) {
            $student = $data['student'];
            $arm     = $data['arm'];

            foreach ($subjects as $subject) {
                // Check this subject is assigned to this arm
                $armSubject = ArmSubject::where('class_arm_id', $arm->id)
                    ->where('subject_id', $subject->id)
                    ->where('session_id', $session->id)
                    ->first();
                if (!$armSubject) continue;

                // CA Scores (randomised but realistic)
                foreach ($caConfigs as $config) {
                    $max = $config->max_score;
                    // Slightly bias towards upper half (realistic school distribution)
                    $score = round(max(0, min($max, $max * (0.4 + lcg_value() * 0.55))), 1);

                    CaScore::updateOrCreate(
                        [
                            'student_id'   => $student->id,
                            'subject_id'   => $subject->id,
                            'term_id'      => $term1->id,
                            'ca_config_id' => $config->id,
                        ],
                        [
                            'class_arm_id' => $arm->id,
                            'score'        => $score,
                        ]
                    );
                }

                // Exam Score (out of 70)
                $examMax = 70;
                $examScore = round(max(20, min($examMax, $examMax * (0.4 + lcg_value() * 0.55))), 1);

                ExamScore::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'term_id'    => $term1->id,
                    ],
                    [
                        'class_arm_id' => $arm->id,
                        'score'        => $examScore,
                        'submitted_at' => now(),
                    ]
                );
            }
        }

        $this->command->line('  ✓ CA & Exam scores: generated for all 60 students × 10 subjects × First Term');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedResults(array $students, array $subjects, array $arms, Term $term1): void
    {
        $gradingScale = GradingScale::orderBy('min_score', 'desc')->get();

        foreach ($students as $data) {
            $student = $data['student'];
            $arm     = $data['arm'];

            foreach ($subjects as $subject) {
                // Sum CA
                $caTotal = CaScore::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->where('term_id', $term1->id)
                    ->sum('score');

                // Get exam score
                $examScore = ExamScore::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->where('term_id', $term1->id)
                    ->value('score') ?? 0;

                $totalScore = round($caTotal + $examScore, 1);

                // Lookup grade
                $grade = $gradingScale->first(fn($g) => $totalScore >= $g->min_score && $totalScore <= $g->max_score);

                Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'term_id'    => $term1->id,
                    ],
                    [
                        'class_arm_id'  => $arm->id,
                        'ca_total'      => round($caTotal, 1),
                        'exam_score'    => $examScore,
                        'total_score'   => $totalScore,
                        'grade'         => $grade?->grade ?? 'F9',
                        'grade_remark'  => $grade?->remark ?? 'Fail',
                        'is_published'  => true,
                    ]
                );
            }
        }

        // Calculate class averages and subject positions per arm
        foreach ($arms as $arm) {
            foreach ($subjects as $subject) {
                $armResults = Result::where('class_arm_id', $arm->id)
                    ->where('subject_id', $subject->id)
                    ->where('term_id', $term1->id)
                    ->orderByDesc('total_score')
                    ->get();

                if ($armResults->isEmpty()) continue;

                $avg     = round($armResults->avg('total_score'), 1);
                $highest = $armResults->max('total_score');
                $lowest  = $armResults->min('total_score');

                $position = 1;
                foreach ($armResults as $result) {
                    $result->update([
                        'class_average'    => $avg,
                        'highest_score'    => $highest,
                        'lowest_score'     => $lowest,
                        'subject_position' => $position++,
                    ]);
                }
            }
        }

        $this->command->line('  ✓ Results: computed grades, class averages, and subject positions for all students');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedTermSummaries(array $students, array $arms, Term $term1): void
    {
        // Compute per-arm summaries and positions
        $armGroups = [];
        foreach ($students as $data) {
            $armGroups[$data['arm']->id][] = $data['student'];
        }

        foreach ($armGroups as $armId => $armStudents) {
            $summaries = [];

            foreach ($armStudents as $student) {
                $results = Result::where('student_id', $student->id)
                    ->where('term_id', $term1->id)
                    ->get();

                $totalObtained   = $results->sum('total_score');
                $totalObtainable = $results->count() * 100;
                $percentage      = $totalObtainable > 0
                    ? round(($totalObtained / $totalObtainable) * 100, 1)
                    : 0;
                $noPassed        = $results->where('grade', '!=', 'F9')->count();
                $noFailed        = $results->where('grade', 'F9')->count();

                $summaries[] = [
                    'student_id'        => $student->id,
                    'total_obtained'    => round($totalObtained, 1),
                    'total_obtainable'  => $totalObtainable,
                    'percentage'        => $percentage,
                    'no_of_subjects'    => $results->count(),
                    'no_passed'         => $noPassed,
                    'no_failed'         => $noFailed,
                    'days_present'      => rand(55, 67),
                    'total_school_days' => 67,
                    'is_published'      => true,
                    'published_at'      => now(),
                ];
            }

            // Sort by percentage to assign arm_position
            usort($summaries, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

            $arm = ClassArm::find($armId);
            foreach ($summaries as $position => $summary) {
                $summary['days_absent'] = 67 - $summary['days_present'];
                TermSummary::updateOrCreate(
                    ['student_id' => $summary['student_id'], 'term_id' => $term1->id],
                    array_merge($summary, [
                        'class_arm_id'       => $armId,
                        'arm_position'       => $position + 1,
                        'class_position'     => $position + 1, // simplified for demo
                        'form_teacher_remark' => $this->randomTeacherRemark($summary['percentage']),
                        'principal_remark'    => $this->randomPrincipalRemark($summary['percentage']),
                    ])
                );
            }
        }

        $this->command->line('  ✓ Term summaries: positions, percentages, and remarks for all 60 students');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedAttendance(array $students, array $arms, Term $term1): void
    {
        // Seed 30 school days of attendance for First Term
        $schoolDays = [];
        $date = \Carbon\Carbon::parse($term1->start_date);
        $count = 0;
        while ($count < 30) {
            if (!in_array($date->dayOfWeek, [0, 6])) { // skip weekends
                $schoolDays[] = $date->toDateString();
                $count++;
            }
            $date->addDay();
        }

        $statuses = ['Present', 'Present', 'Present', 'Present', 'Absent', 'Present', 'Late', 'Present', 'Present', 'Present'];

        foreach ($students as $data) {
            $student = $data['student'];
            $arm     = $data['arm'];

            foreach ($schoolDays as $day) {
                $status = $statuses[array_rand($statuses)];
                AttendanceRecord::updateOrCreate(
                    ['student_id' => $student->id, 'attendance_date' => $day],
                    [
                        'class_arm_id' => $arm->id,
                        'term_id'      => $term1->id,
                        'status'       => $status,
                    ]
                );
            }
        }

        $this->command->line('  ✓ Attendance: 30 days of records for all 60 students (realistic Present/Absent/Late mix)');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function randomTeacherRemark(float $percentage): string
    {
        if ($percentage >= 75) return 'An exceptional student who consistently demonstrates a love for learning. Keep it up!';
        if ($percentage >= 60) return 'A dedicated student with good academic standing. Continue to work hard.';
        if ($percentage >= 50) return 'Shows potential but needs to put in more effort, especially in weaker subjects.';
        return 'Performance is below expectation. Please seek extra lessons and revise regularly.';
    }

    private function randomPrincipalRemark(float $percentage): string
    {
        if ($percentage >= 80) return 'Outstanding performance. We are proud of your achievements this term.';
        if ($percentage >= 65) return 'Good performance. Continue to strive for excellence.';
        if ($percentage >= 50) return 'Fair performance. There is room for improvement next term.';
        return 'Below average performance. Please work harder and engage more actively in class.';
    }
}
