<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enquiry;
use App\Models\CounsellingRecord;
use App\Models\FollowUp;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\StudentFee;
use App\Models\FeeInstallment;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StudentAttendance;
use App\Models\StudentProgress;
use App\Models\MediaFile;
use App\Models\Certificate;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\Setting;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $rolesData = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full administrative access to everything'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrative control of operations'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Branch & operational manager'],
            ['name' => 'Counselor', 'slug' => 'counselor', 'description' => 'Enquiries, admissions, and student counselling'],
            ['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Fee collection, payments, and financial reports'],
            ['name' => 'Trainer', 'slug' => 'trainer', 'description' => 'Classes, attendance, and student progress assessments'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'General office staff and support'],
        ];

        $roles = [];
        foreach ($rolesData as $rd) {
            $roles[$rd['slug']] = Role::create($rd);
        }

        // 2. Permissions
        $modules = [
            'dashboard' => ['view'],
            'students' => ['view', 'create', 'edit', 'delete', 'export'],
            'enquiries' => ['view', 'create', 'edit', 'delete', 'convert'],
            'courses' => ['view', 'create', 'edit', 'delete'],
            'batches' => ['view', 'create', 'edit', 'delete'],
            'fees' => ['view', 'collect', 'edit', 'refund', 'export'],
            'attendance' => ['view', 'mark', 'export'],
            'progress' => ['view', 'create', 'edit'],
            'certificates' => ['view', 'generate', 'verify'],
            'staff' => ['view', 'create', 'edit', 'delete'],
            'media' => ['view', 'upload', 'delete'],
            'reports' => ['view', 'export'],
            'settings' => ['view', 'edit'],
            'users' => ['view', 'create', 'edit', 'delete'],
        ];

        $allPermissions = [];
        foreach ($modules as $mod => $actions) {
            foreach ($actions as $act) {
                $slug = "{$mod}.{$act}";
                $p = Permission::create([
                    'module' => ucfirst($mod),
                    'name' => ucfirst($act) . ' ' . ucfirst($mod),
                    'slug' => $slug,
                    'description' => "Permission to {$act} {$mod}",
                ]);
                $allPermissions[$slug] = $p;
            }
        }

        // Assign permissions
        $roles['super-admin']->permissions()->sync(collect($allPermissions)->pluck('id'));
        $roles['admin']->permissions()->sync(collect($allPermissions)->pluck('id'));

        // Counselor
        $counselorPerms = collect($allPermissions)->filter(function ($p) {
            return in_array($p->module, ['Dashboard', 'Enquiries', 'Students', 'Courses', 'Batches']);
        })->pluck('id');
        $roles['counselor']->permissions()->sync($counselorPerms);

        // Accountant
        $accountantPerms = collect($allPermissions)->filter(function ($p) {
            return in_array($p->module, ['Dashboard', 'Fees', 'Students', 'Reports']);
        })->pluck('id');
        $roles['accountant']->permissions()->sync($accountantPerms);

        // Trainer
        $trainerPerms = collect($allPermissions)->filter(function ($p) {
            return in_array($p->module, ['Dashboard', 'Students', 'Courses', 'Batches', 'Attendance', 'Progress', 'Certificates']);
        })->pluck('id');
        $roles['trainer']->permissions()->sync($trainerPerms);

        // 3. Users
        $superAdmin = User::create([
            'name' => 'Dr. Madhav Sharma',
            'email' => 'admin@medhaversum.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['super-admin']->id,
            'phone' => '+91 98765 43210',
            'status' => 'Active',
            'last_login_at' => now(),
        ]);

        $counselorUser = User::create([
            'name' => 'Pooja Verma',
            'email' => 'counselor@medhaversum.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['counselor']->id,
            'phone' => '+91 98765 43211',
            'status' => 'Active',
        ]);

        $accountantUser = User::create([
            'name' => 'Rajesh Gupta',
            'email' => 'accounts@medhaversum.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['accountant']->id,
            'phone' => '+91 98765 43212',
            'status' => 'Active',
        ]);

        $trainerUser = User::create([
            'name' => 'Acharya Anand Dev',
            'email' => 'trainer@medhaversum.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['trainer']->id,
            'phone' => '+91 98765 43213',
            'status' => 'Active',
        ]);

        // 4. Staff Profiles
        $staffMembers = [
            [
                'staff_code' => 'STF-001',
                'user_id' => $superAdmin->id,
                'name' => 'Dr. Madhav Sharma',
                'email' => 'admin@medhaversum.com',
                'mobile' => '+91 98765 43210',
                'designation' => 'Director & Founder',
                'department' => 'Administration',
                'joining_date' => Carbon::now()->subYears(3),
                'salary' => 150000,
                'address' => 'Gurukulam Campus, Mind Science Lane, New Delhi',
                'status' => 'Active',
            ],
            [
                'staff_code' => 'STF-002',
                'user_id' => $trainerUser->id,
                'name' => 'Acharya Anand Dev',
                'email' => 'trainer@medhaversum.com',
                'mobile' => '+91 98765 43213',
                'designation' => 'Chief Neuro-Trainer',
                'department' => 'Training',
                'joining_date' => Carbon::now()->subYears(2),
                'salary' => 85000,
                'address' => 'Gurukulam Campus, New Delhi',
                'status' => 'Active',
            ],
            [
                'staff_code' => 'STF-003',
                'user_id' => $counselorUser->id,
                'name' => 'Pooja Verma',
                'email' => 'counselor@medhaversum.com',
                'mobile' => '+91 98765 43211',
                'designation' => 'Senior Educational Counselor',
                'department' => 'Counselling',
                'joining_date' => Carbon::now()->subMonths(18),
                'salary' => 55000,
                'address' => 'Vasant Kunj, New Delhi',
                'status' => 'Active',
            ],
            [
                'staff_code' => 'STF-004',
                'user_id' => $accountantUser->id,
                'name' => 'Rajesh Gupta',
                'email' => 'accounts@medhaversum.com',
                'mobile' => '+91 98765 43212',
                'designation' => 'Finance & Accounts Head',
                'department' => 'Accounts',
                'joining_date' => Carbon::now()->subMonths(14),
                'salary' => 60000,
                'address' => 'Janakpuri, New Delhi',
                'status' => 'Active',
            ],
        ];

        $staffModels = [];
        foreach ($staffMembers as $sm) {
            $staffModels[] = Staff::create($sm);
        }

        // 5. Initial Courses (as required by prompt)
        $course1 = Course::create([
            'course_code' => 'CRS-SMA',
            'name' => 'Subconscious Mind Activation',
            'slug' => 'subconscious-mind-activation',
            'description' => 'Unleash intuitive intelligence, emotional balance, deep mental focus, and accelerated cognitive capacities.',
            'duration_weeks' => 12,
            'total_fees' => 25000.00,
            'admission_fees' => 5000.00,
            'installment_count' => 3,
            'batch_timing' => '09:00 AM - 11:00 AM',
            'trainer_id' => $trainerUser->id,
            'status' => 'Active',
        ]);

        $course2 = Course::create([
            'course_code' => 'CRS-PHM',
            'name' => 'Photographic Memory',
            'slug' => 'photographic-memory',
            'description' => 'Advanced mnemonics, visualization mechanics, speed reading, and permanent memory retention techniques.',
            'duration_weeks' => 8,
            'total_fees' => 20000.00,
            'admission_fees' => 4000.00,
            'installment_count' => 2,
            'batch_timing' => '11:30 AM - 01:30 PM',
            'trainer_id' => $trainerUser->id,
            'status' => 'Active',
        ]);

        $course3 = Course::create([
            'course_code' => 'CRS-BWM',
            'name' => 'Brain Waves Mapping',
            'slug' => 'brain-waves-mapping',
            'description' => 'Scientific EEG assessment and bio-feedback coaching to synchronize Alpha, Theta, and Gamma wave states for peak performance.',
            'duration_weeks' => 10,
            'total_fees' => 18000.00,
            'admission_fees' => 3000.00,
            'installment_count' => 3,
            'batch_timing' => '03:00 PM - 05:00 PM',
            'trainer_id' => $trainerUser->id,
            'status' => 'Active',
        ]);

        // 6. Batches
        $batches = [
            Batch::create([
                'course_id' => $course1->id,
                'batch_code' => 'SMA-BATCH-01',
                'batch_name' => 'SMA Morning Batch (Alpha-1)',
                'start_date' => Carbon::now()->subMonths(1)->startOfMonth(),
                'end_date' => Carbon::now()->addMonths(2)->endOfMonth(),
                'schedule_time' => '09:00 AM - 11:00 AM',
                'days_of_week' => 'Mon, Wed, Fri',
                'max_capacity' => 25,
                'trainer_id' => $trainerUser->id,
                'status' => 'Ongoing',
            ]),
            Batch::create([
                'course_id' => $course2->id,
                'batch_code' => 'PHM-BATCH-01',
                'batch_name' => 'Memory Masters Weekend',
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addWeeks(6),
                'schedule_time' => '11:30 AM - 01:30 PM',
                'days_of_week' => 'Sat, Sun',
                'max_capacity' => 20,
                'trainer_id' => $trainerUser->id,
                'status' => 'Ongoing',
            ]),
            Batch::create([
                'course_id' => $course3->id,
                'batch_code' => 'BWM-BATCH-01',
                'batch_name' => 'Brain Sync Evening Track',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addMonths(3),
                'schedule_time' => '03:00 PM - 05:00 PM',
                'days_of_week' => 'Tue, Thu, Sat',
                'max_capacity' => 15,
                'trainer_id' => $trainerUser->id,
                'status' => 'Upcoming',
            ]),
        ];

        // 7. Enquiries
        $enquiriesData = [
            [
                'enquiry_code' => 'ENQ-2026-001',
                'student_name' => 'Aarav Sharma',
                'parent_name' => 'Sunil Sharma',
                'mobile' => '+91 98111 22233',
                'email' => 'sunil.sharma@gmail.com',
                'course_id' => $course1->id,
                'enquiry_date' => Carbon::now()->subDays(25),
                'source' => 'Website',
                'counselor_id' => $counselorUser->id,
                'follow_up_date' => null,
                'remarks' => 'Very keen on Subconscious Activation for Grade 8 child.',
                'status' => 'Converted',
            ],
            [
                'enquiry_code' => 'ENQ-2026-002',
                'student_name' => 'Ananya Patel',
                'parent_name' => 'Mahesh Patel',
                'mobile' => '+91 98222 33344',
                'email' => 'mahesh.patel@yahoo.com',
                'course_id' => $course2->id,
                'enquiry_date' => Carbon::now()->subDays(18),
                'source' => 'Referral',
                'counselor_id' => $counselorUser->id,
                'follow_up_date' => null,
                'remarks' => 'Parent requested demonstration on photographic memory techniques.',
                'status' => 'Converted',
            ],
            [
                'enquiry_code' => 'ENQ-2026-003',
                'student_name' => 'Rohan Mehta',
                'parent_name' => 'Kavita Mehta',
                'mobile' => '+91 98333 44455',
                'email' => 'kavita.mehta@gmail.com',
                'course_id' => $course3->id,
                'enquiry_date' => Carbon::now()->subDays(10),
                'source' => 'Social Media',
                'counselor_id' => $counselorUser->id,
                'follow_up_date' => Carbon::now()->addDays(2),
                'remarks' => 'Scheduled demo session for brain wave mapping.',
                'status' => 'Follow-up',
            ],
            [
                'enquiry_code' => 'ENQ-2026-004',
                'student_name' => 'Isha Deshmukh',
                'parent_name' => 'Sanjay Deshmukh',
                'mobile' => '+91 98444 55566',
                'email' => 'sanjay.d@outlook.com',
                'course_id' => $course1->id,
                'enquiry_date' => Carbon::now()->subDays(5),
                'source' => 'Walk-in',
                'counselor_id' => $counselorUser->id,
                'follow_up_date' => Carbon::now()->addDays(1),
                'remarks' => 'Visited campus, requested fee installment schedule.',
                'status' => 'Interested',
            ],
            [
                'enquiry_code' => 'ENQ-2026-005',
                'student_name' => 'Vivaan Joshi',
                'parent_name' => 'Deepak Joshi',
                'mobile' => '+91 98555 66677',
                'email' => 'deepak.joshi@gmail.com',
                'course_id' => $course2->id,
                'enquiry_date' => Carbon::now()->subDays(2),
                'source' => 'Phone',
                'counselor_id' => $counselorUser->id,
                'follow_up_date' => Carbon::now()->addDays(3),
                'remarks' => 'Enquired about upcoming batches.',
                'status' => 'New',
            ],
        ];

        $enquiryModels = [];
        foreach ($enquiriesData as $eq) {
            $enquiryModels[] = Enquiry::create($eq);
        }

        // Counselling & Followups
        CounsellingRecord::create([
            'enquiry_id' => $enquiryModels[0]->id,
            'counselor_id' => $counselorUser->id,
            'session_date' => Carbon::now()->subDays(24),
            'discussion_notes' => 'Detailed 45-minute counselling with parent and student. Assessed concentration span and stress patterns. Recommended Subconscious Mind Activation.',
            'student_interest_level' => 'High',
            'recommended_course_id' => $course1->id,
            'next_step' => 'Proceed with admission and enrollment.',
        ]);

        FollowUp::create([
            'enquiry_id' => $enquiryModels[2]->id,
            'user_id' => $counselorUser->id,
            'follow_up_date' => Carbon::now()->subDays(2),
            'follow_up_type' => 'Call',
            'response_notes' => 'Discussed EEG mapping advantages. Parent agreed to visit next Tuesday.',
            'next_follow_up_date' => Carbon::now()->addDays(2),
            'status' => 'Completed',
        ]);

        // 8. Students & Admissions
        $studentsData = [
            [
                'student_id' => 'MG-2026-0001',
                'enquiry_id' => $enquiryModels[0]->id,
                'full_name' => 'Aarav Sharma',
                'dob' => '2012-05-14',
                'gender' => 'Male',
                'email' => 'aarav.sharma@gmail.com',
                'mobile' => '+91 98111 22233',
                'address' => 'Flat 402, Royal Palms, Rohini Sector 9',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'pincode' => '110085',
                'parent_name' => 'Sunil Sharma',
                'parent_contact' => '+91 98111 22233',
                'parent_relation' => 'Father',
                'parent_email' => 'sunil.sharma@gmail.com',
                'admission_date' => Carbon::now()->subDays(23),
                'status' => 'Active',
                'counselor_id' => $counselorUser->id,
                'notes' => 'Dedicated learner, exhibits great visualization response.',
                'course' => $course1,
                'batch' => $batches[0],
                'discount' => 1000.00,
                'payments' => [
                    ['amount' => 5000.00, 'date' => Carbon::now()->subDays(23), 'method' => 'UPI', 'ref' => 'UPI/981122/8821', 'remarks' => 'Admission Fee'],
                    ['amount' => 9500.00, 'date' => Carbon::now()->subDays(10), 'method' => 'Bank Transfer', 'ref' => 'NEFT-55443321', 'remarks' => '1st Installment'],
                ],
            ],
            [
                'student_id' => 'MG-2026-0002',
                'enquiry_id' => $enquiryModels[1]->id,
                'full_name' => 'Ananya Patel',
                'dob' => '2011-09-20',
                'gender' => 'Female',
                'email' => 'ananya.p@gmail.com',
                'mobile' => '+91 98222 33344',
                'address' => 'B-12, Green Glen Layout, Bellandur',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pincode' => '560103',
                'parent_name' => 'Mahesh Patel',
                'parent_contact' => '+91 98222 33344',
                'parent_relation' => 'Father',
                'parent_email' => 'mahesh.patel@yahoo.com',
                'admission_date' => Carbon::now()->subDays(15),
                'status' => 'Active',
                'counselor_id' => $counselorUser->id,
                'notes' => 'Enrolled in Photographic Memory program.',
                'course' => $course2,
                'batch' => $batches[1],
                'discount' => 0.00,
                'payments' => [
                    ['amount' => 20000.00, 'date' => Carbon::now()->subDays(15), 'method' => 'UPI', 'ref' => 'UPI/776655/4432', 'remarks' => 'Full Course Fee Paid in Advance'],
                ],
            ],
            [
                'student_id' => 'MG-2026-0003',
                'enquiry_id' => null,
                'full_name' => 'Devansh Singhal',
                'dob' => '2013-02-11',
                'gender' => 'Male',
                'email' => 'devansh.s@gmail.com',
                'mobile' => '+91 98777 88899',
                'address' => '34-C, Civil Lines',
                'city' => 'Jaipur',
                'state' => 'Rajasthan',
                'pincode' => '302006',
                'parent_name' => 'Dr. Rakesh Singhal',
                'parent_contact' => '+91 98777 88899',
                'parent_relation' => 'Father',
                'parent_email' => 'dr.rakesh@gmail.com',
                'admission_date' => Carbon::now()->subDays(8),
                'status' => 'Active',
                'counselor_id' => $counselorUser->id,
                'notes' => 'Direct Walk-in admission.',
                'course' => $course1,
                'batch' => $batches[0],
                'discount' => 2000.00,
                'payments' => [
                    ['amount' => 5000.00, 'date' => Carbon::now()->subDays(8), 'method' => 'Cash', 'ref' => 'CASH-REC-01', 'remarks' => 'Admission Fee in Cash'],
                ],
            ],
        ];

        $paymentCounter = 1;
        $receiptCounter = 1001;

        foreach ($studentsData as $stData) {
            $student = Student::create([
                'student_id' => $stData['student_id'],
                'enquiry_id' => $stData['enquiry_id'],
                'full_name' => $stData['full_name'],
                'dob' => $stData['dob'],
                'gender' => $stData['gender'],
                'email' => $stData['email'],
                'mobile' => $stData['mobile'],
                'address' => $stData['address'],
                'city' => $stData['city'],
                'state' => $stData['state'],
                'pincode' => $stData['pincode'],
                'parent_name' => $stData['parent_name'],
                'parent_contact' => $stData['parent_contact'],
                'parent_relation' => $stData['parent_relation'],
                'parent_email' => $stData['parent_email'],
                'admission_date' => $stData['admission_date'],
                'status' => $stData['status'],
                'counselor_id' => $stData['counselor_id'],
                'notes' => $stData['notes'],
            ]);

            // StudentCourse
            $totalCourseFee = $stData['course']->total_fees;
            $discount = $stData['discount'];
            $finalPayable = $totalCourseFee - $discount;

            $studentCourse = StudentCourse::create([
                'student_id' => $student->id,
                'course_id' => $stData['course']->id,
                'batch_id' => $stData['batch']->id,
                'trainer_id' => $stData['batch']->trainer_id,
                'enrollment_date' => $stData['admission_date'],
                'status' => 'Active',
                'total_course_fee' => $totalCourseFee,
                'discount' => $discount,
                'final_payable_amount' => $finalPayable,
            ]);

            // StudentFee
            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'student_course_id' => $studentCourse->id,
                'total_amount' => $totalCourseFee,
                'admission_fee' => $stData['course']->admission_fees,
                'discount' => $discount,
                'net_amount' => $finalPayable,
                'paid_amount' => 0.00,
                'pending_amount' => $finalPayable,
                'fee_status' => 'Pending',
            ]);

            // Installments
            $instCount = $stData['course']->installment_count;
            $instAmount = round(($finalPayable - $stData['course']->admission_fees) / max(1, ($instCount - 1)), 2);

            // Installment 1: Admission fee
            FeeInstallment::create([
                'student_fee_id' => $studentFee->id,
                'installment_no' => 1,
                'due_date' => $stData['admission_date'],
                'amount' => $stData['course']->admission_fees,
                'paid_amount' => 0.00,
                'status' => 'Pending',
            ]);

            for ($i = 2; $i <= $instCount; $i++) {
                FeeInstallment::create([
                    'student_fee_id' => $studentFee->id,
                    'installment_no' => $i,
                    'due_date' => Carbon::parse($stData['admission_date'])->addMonths($i - 1),
                    'amount' => $instAmount,
                    'paid_amount' => 0.00,
                    'status' => 'Pending',
                ]);
            }

            // Record Payments
            foreach ($stData['payments'] as $pay) {
                $payCode = sprintf('PAY-2026-%04d', $paymentCounter++);
                $recNo = sprintf('REC-2026-%04d', $receiptCounter++);

                Payment::create([
                    'payment_code' => $payCode,
                    'receipt_no' => $recNo,
                    'student_id' => $student->id,
                    'student_fee_id' => $studentFee->id,
                    'installment_id' => null,
                    'amount' => $pay['amount'],
                    'payment_date' => $pay['date'],
                    'payment_method' => $pay['method'],
                    'transaction_reference' => $pay['ref'],
                    'remarks' => $pay['remarks'],
                    'recorded_by' => $accountantUser->id,
                    'status' => 'Successful',
                ]);
            }

            // Recalculate fee status
            $studentFee->recalculate();

            // Progress assessment
            StudentProgress::create([
                'student_id' => $student->id,
                'course_id' => $stData['course']->id,
                'batch_id' => $stData['batch']->id,
                'assessment_date' => Carbon::now()->subDays(4),
                'assessment_type' => 'Subconscious Mind Activation',
                'performance_score' => 88.5,
                'trainer_id' => $trainerUser->id,
                'trainer_remarks' => 'Exceptional mental relaxation response and visual focus in Alpha wave state.',
                'improvement_notes' => 'Practice daily 15-minute rhythmic breathing exercise.',
                'next_assessment_date' => Carbon::now()->addDays(14),
            ]);

            // Sample attendance
            for ($d = 10; $d >= 1; $d--) {
                StudentAttendance::create([
                    'student_id' => $student->id,
                    'course_id' => $stData['course']->id,
                    'batch_id' => $stData['batch']->id,
                    'date' => Carbon::now()->subDays($d)->format('Y-m-d'),
                    'status' => $d === 5 ? 'Absent' : ($d === 2 ? 'Late' : 'Present'),
                    'trainer_id' => $trainerUser->id,
                    'remarks' => $d === 5 ? 'Informed leave due to family function' : 'Participated enthusiastically',
                ]);
            }
        }

        // 9. Staff Attendance for today
        foreach ($staffModels as $sm) {
            StaffAttendance::create([
                'staff_id' => $sm->id,
                'date' => Carbon::today(),
                'check_in' => '09:00:00',
                'check_out' => null,
                'status' => 'Present',
                'remarks' => 'On-time biometric check-in',
            ]);
        }

        // 10. Sample Certificate
        Certificate::create([
            'certificate_number' => 'MG-CERT-2026-001',
            'verification_code' => Str::upper(Str::random(10)),
            'student_id' => 1,
            'course_id' => $course1->id,
            'completion_date' => Carbon::now()->subDays(2),
            'issue_date' => Carbon::now(),
            'authorized_signatory' => 'Dr. Madhav Sharma (Founder & Director)',
            'grade_score' => 'Grade A+ (Exemplary Mastery)',
            'status' => 'Issued',
        ]);

        // 11. Media Files
        MediaFile::create([
            'title' => 'Medhaversum Official Gurukulam Emblem',
            'category' => 'Course Materials',
            'file_path' => 'assets/images/logo.png',
            'file_name' => 'logo.png',
            'file_type' => 'image',
            'file_size' => 102400,
            'course_id' => $course1->id,
            'batch_id' => $batches[0]->id,
            'student_id' => null,
            'uploaded_by' => $superAdmin->id,
            'is_private' => false,
        ]);

        // 12. Notifications
        Notification::create([
            'user_id' => $superAdmin->id,
            'title' => 'Upcoming Fee Installment Due',
            'message' => 'Student Aarav Sharma (MG-2026-0001) has an installment due next week.',
            'type' => 'Fee Due',
            'icon' => 'bi-cash-coin',
            'link' => '/fees',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $superAdmin->id,
            'title' => 'Enquiry Follow-up Required',
            'message' => 'Follow up with Rohan Mehta (ENQ-2026-003) for Brain Waves Mapping demonstration.',
            'type' => 'Follow-up',
            'icon' => 'bi-telephone-outbound',
            'link' => '/enquiries',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $superAdmin->id,
            'title' => 'New Batch Launching Soon',
            'message' => 'Brain Sync Evening Track (BWM-BATCH-01) commences in 5 days.',
            'type' => 'Batch Alert',
            'icon' => 'bi-calendar-check',
            'link' => '/batches',
            'is_read' => false,
        ]);

        // 13. System Settings
        $settingsData = [
            ['key' => 'institute_name', 'value' => 'Medhaversum Gurukulam', 'group' => 'general'],
            ['key' => 'institute_tagline', 'value' => 'A Universe of Infinite Potential - हर मन में एक नया ब्रह्मांड', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'contact@medhaversum.com', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+91 98765 43210', 'group' => 'general'],
            ['key' => 'address', 'value' => 'Mind Science & Neuro-Cognitive Gurukulam Campus, New Delhi, India', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'fees'],
            ['key' => 'currency_code', 'value' => 'INR', 'group' => 'fees'],
            ['key' => 'gst_number', 'value' => '07AAAAA0000A1Z5', 'group' => 'fees'],
            ['key' => 'receipt_prefix', 'value' => 'REC-2026-', 'group' => 'fees'],
            ['key' => 'student_id_prefix', 'value' => 'MG-2026-', 'group' => 'general'],
        ];

        foreach ($settingsData as $st) {
            Setting::create($st);
        }

        // 14. Activity Log
        ActivityLog::create([
            'user_id' => $superAdmin->id,
            'action' => 'System Initialized',
            'module' => 'System',
            'record_id' => 1,
            'description' => 'Medhaversum Gurukulam CRM successfully seeded and configured.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Seeder',
        ]);
    }
}
