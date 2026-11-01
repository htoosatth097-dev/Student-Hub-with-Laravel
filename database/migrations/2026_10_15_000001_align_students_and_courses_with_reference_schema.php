<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns the database with the schema used in "my-laravel-project":
 *
 *  courses
 *    - course_duration  -> duration_hours
 *    - description      -> nullable, no longer UNIQUE
 *
 *  laravel_students
 *    - name             -> student_name
 *    - phone            -> phone_number
 *    - course (text)    -> course_id (FK to courses.id)  [existing data is converted]
 *    - course (text)    column is dropped afterwards
 *
 * Every step is guarded with hasColumn()/getIndexes(), so this migration is safe
 * to run on a fresh database AND on a database that already contains data.
 */
return new class extends Migration
{
    public function up(): void
    {
        /* ---------------------------------------------------------------
         | 1. courses table
         * ------------------------------------------------------------- */
        if (Schema::hasColumn('courses', 'course_duration') && ! Schema::hasColumn('courses', 'duration_hours')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->renameColumn('course_duration', 'duration_hours');
            });
        } elseif (! Schema::hasColumn('courses', 'duration_hours')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('duration_hours', 100)->nullable();
            });
        }

        // description was NOT NULL + UNIQUE; two courses may share a description
        // and the reference controller treats it as optional.
        $descriptionIsUnique = collect(Schema::getIndexes('courses'))
            ->contains(fn ($index) => $index['unique'] && $index['columns'] === ['description']);

        Schema::table('courses', function (Blueprint $table) use ($descriptionIsUnique) {
            if ($descriptionIsUnique) {
                $table->dropUnique(['description']);
            }
            $table->string('description')->nullable()->change();
        });

        /* ---------------------------------------------------------------
         | 2. laravel_students table - rename columns
         * ------------------------------------------------------------- */
        if (Schema::hasColumn('laravel_students', 'name') && ! Schema::hasColumn('laravel_students', 'student_name')) {
            Schema::table('laravel_students', function (Blueprint $table) {
                $table->renameColumn('name', 'student_name');
            });
        }

        if (Schema::hasColumn('laravel_students', 'phone') && ! Schema::hasColumn('laravel_students', 'phone_number')) {
            Schema::table('laravel_students', function (Blueprint $table) {
                $table->renameColumn('phone', 'phone_number');
            });
        }

        /* ---------------------------------------------------------------
         | 3. Convert the old free-text "course" column into course_id
         * ------------------------------------------------------------- */
        if (Schema::hasColumn('laravel_students', 'course')) {
            $rows = DB::table('laravel_students')
                ->whereNull('course_id')
                ->whereNotNull('course')
                ->get(['id', 'course']);

            $courseIds = []; // cache: course name => course id

            foreach ($rows as $row) {
                $courseName = trim((string) $row->course);

                if ($courseName === '') {
                    continue;
                }

                if (! isset($courseIds[$courseName])) {
                    $existingId = DB::table('courses')->where('course_name', $courseName)->value('id');

                    $courseIds[$courseName] = $existingId ?: DB::table('courses')->insertGetId([
                        'course_name' => $courseName,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }

                DB::table('laravel_students')
                    ->where('id', $row->id)
                    ->update(['course_id' => $courseIds[$courseName]]);
            }

            Schema::table('laravel_students', function (Blueprint $table) {
                $table->dropColumn('course');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('laravel_students', 'course')) {
            Schema::table('laravel_students', function (Blueprint $table) {
                $table->string('course')->nullable();
            });

            // Copy the course name back from the courses table.
            DB::table('laravel_students')
                ->whereNotNull('course_id')
                ->get(['id', 'course_id'])
                ->each(function ($row) {
                    DB::table('laravel_students')
                        ->where('id', $row->id)
                        ->update(['course' => DB::table('courses')->where('id', $row->course_id)->value('course_name')]);
                });
        }

        if (Schema::hasColumn('laravel_students', 'phone_number') && ! Schema::hasColumn('laravel_students', 'phone')) {
            Schema::table('laravel_students', function (Blueprint $table) {
                $table->renameColumn('phone_number', 'phone');
            });
        }

        if (Schema::hasColumn('laravel_students', 'student_name') && ! Schema::hasColumn('laravel_students', 'name')) {
            Schema::table('laravel_students', function (Blueprint $table) {
                $table->renameColumn('student_name', 'name');
            });
        }

        if (Schema::hasColumn('courses', 'duration_hours') && ! Schema::hasColumn('courses', 'course_duration')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->renameColumn('duration_hours', 'course_duration');
            });
        }
    }
};
