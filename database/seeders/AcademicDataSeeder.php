<?php

namespace Database\Seeders;

use App\Models\courses;
use App\Models\laravelstudents;
use Illuminate\Database\Seeder;

class AcademicDataSeeder extends Seeder
{
    /**
     * Run the database seeds for courses and students.
     */
    public function run(): void
    {
        $coursesData = [
            [
                'course_name'    => 'Web Application Development with Laravel & Vue.js',
                'course_code'    => 'CS-101',
                'description'    => 'Full-stack web application development covering PHP 8, Laravel 11, Eloquent ORM, REST APIs, and Vue.js frontend integration.',
                'duration_hours' => '120 Hours',
                'course_fee'     => '350,000 MMK',
            ],
            [
                'course_name'    => 'Python for Data Science & Machine Learning',
                'course_code'    => 'CS-102',
                'description'    => 'Practical data analysis, statistics, NumPy, Pandas, Matplotlib, and predictive modeling using Scikit-learn.',
                'duration_hours' => '150 Hours',
                'course_fee'     => '450,000 MMK',
            ],
            [
                'course_name'    => 'Cross-Platform Mobile App Development with Flutter',
                'course_code'    => 'CS-103',
                'description'    => 'Build production-ready native iOS and Android apps using Google Flutter framework, Dart, and Firebase backend.',
                'duration_hours' => '100 Hours',
                'course_fee'     => '320,000 MMK',
            ],
            [
                'course_name'    => 'Cybersecurity & Ethical Hacking Essentials',
                'course_code'    => 'CS-104',
                'description'    => 'Network defense principles, security auditing, vulnerability scanning, penetration testing basics, and incident response.',
                'duration_hours' => '110 Hours',
                'course_fee'     => '400,000 MMK',
            ],
            [
                'course_name'    => 'Modern UI/UX Design & Product Prototyping',
                'course_code'    => 'CS-105',
                'description'    => 'User experience research, wireframing, component design systems, and responsive interactive prototypes using Figma.',
                'duration_hours' => '80 Hours',
                'course_fee'     => '280,000 MMK',
            ],
        ];

        $savedCourses = [];
        foreach ($coursesData as $c) {
            $course = courses::firstOrCreate(
                ['course_name' => $c['course_name']],
                $c
            );
            $savedCourses[$c['course_code']] = $course;
        }

        $studentsData = [
            [
                'student_code' => 'STU-001',
                'student_name' => 'Aung Myo Thant',
                'email'        => 'aungmyothant@example.com',
                'phone_number' => '09450012341',
                'gender'       => 'Male',
                'course_code'  => 'CS-101',
            ],
            [
                'student_code' => 'STU-002',
                'student_name' => 'Su Myat Noe',
                'email'        => 'sumyatnoe@example.com',
                'phone_number' => '09450012342',
                'gender'       => 'Female',
                'course_code'  => 'CS-101',
            ],
            [
                'student_code' => 'STU-003',
                'student_name' => 'Kyaw Zin Htet',
                'email'        => 'kyawzinhtet@example.com',
                'phone_number' => '09450012343',
                'gender'       => 'Male',
                'course_code'  => 'CS-102',
            ],
            [
                'student_code' => 'STU-004',
                'student_name' => 'Hnin Wut Yee',
                'email'        => 'hninwutyee@example.com',
                'phone_number' => '09450012344',
                'gender'       => 'Female',
                'course_code'  => 'CS-102',
            ],
            [
                'student_code' => 'STU-005',
                'student_name' => 'Zayar Min Lwin',
                'email'        => 'zayarminlwin@example.com',
                'phone_number' => '09450012345',
                'gender'       => 'Male',
                'course_code'  => 'CS-103',
            ],
            [
                'student_code' => 'STU-006',
                'student_name' => 'Thet Htar San',
                'email'        => 'thethtarsan@example.com',
                'phone_number' => '09450012346',
                'gender'       => 'Female',
                'course_code'  => 'CS-103',
            ],
            [
                'student_code' => 'STU-007',
                'student_name' => 'Kaung Sett Naing',
                'email'        => 'kaungsettnaing@example.com',
                'phone_number' => '09450012347',
                'gender'       => 'Male',
                'course_code'  => 'CS-104',
            ],
            [
                'student_code' => 'STU-008',
                'student_name' => 'Phyu Phyu Thin',
                'email'        => 'phyuphyuthin@example.com',
                'phone_number' => '09450012348',
                'gender'       => 'Female',
                'course_code'  => 'CS-104',
            ],
            [
                'student_code' => 'STU-009',
                'student_name' => 'Min Thuta Zaw',
                'email'        => 'minthutazaw@example.com',
                'phone_number' => '09450012349',
                'gender'       => 'Male',
                'course_code'  => 'CS-105',
            ],
            [
                'student_code' => 'STU-010',
                'student_name' => 'Aye Myat Mon',
                'email'        => 'ayemyatmon@example.com',
                'phone_number' => '09450012350',
                'gender'       => 'Female',
                'course_code'  => 'CS-105',
            ],
        ];

        foreach ($studentsData as $s) {
            $courseId = $savedCourses[$s['course_code']]->id ?? null;
            laravelstudents::firstOrCreate(
                ['email' => $s['email']],
                [
                    'student_code' => $s['student_code'],
                    'student_name' => $s['student_name'],
                    'phone_number' => $s['phone_number'],
                    'gender'       => $s['gender'],
                    'course_id'    => $courseId,
                ]
            );
        }
    }
}

