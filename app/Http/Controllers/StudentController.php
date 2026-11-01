<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveStudentRequest;
use App\Models\courses;
use App\Models\laravelstudents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Allowed gender values (must match the <option> values in the forms).
     */
    public const GENDERS = laravelstudents::GENDERS;

    /**
     * Show all students (paginated) together with their course and full registry statistics.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', laravelstudents::class);

        $students = laravelstudents::with('course')
            ->filter($request->only(['search', 'q', 'course_id', 'gender', 'sort']))
            ->paginate(10)
            ->withQueryString();

        $courses = courses::orderBy('course_name')->get();
        $dashboardStats = laravelstudents::dashboardStats();

        return view('students.index', compact('students', 'courses', 'dashboardStats'));
    }

    /**
     * Show create form (needs the course list for the dropdown).
     */
    public function create(): View
    {
        $this->authorize('create', laravelstudents::class);

        $courses = courses::orderBy('course_name')->get();

        return view('students.create', compact('courses'));
    }

    /**
     * Save new student to database.
     */
    public function store(SaveStudentRequest $request): RedirectResponse
    {
        $this->authorize('create', laravelstudents::class);

        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $student = laravelstudents::create([
                ...$data,
                'student_code' => 'TEMP-'.Str::uuid(),
            ]);

            $student->update([
                'student_code' => laravelstudents::generateNextCode($student->id),
            ]);
        });

        return redirect()->route('students.index')
            ->with('success', 'Student record has been created successfully.');
    }

    /**
     * View single student details.
     */
    public function show(int|string $id): View
    {
        $student = laravelstudents::with('course')->findOrFail($id);
        $this->authorize('view', $student);

        return view('students.show', compact('student'));
    }

    /**
     * Show edit form.
     */
    public function edit(int|string $id): View
    {
        $student = laravelstudents::findOrFail($id);
        $this->authorize('update', $student);
        $courses = courses::orderBy('course_name')->get();

        return view('students.edit', compact('student', 'courses'));
    }

    /**
     * Update student in database.
     */
    public function update(SaveStudentRequest $request, int|string $id): RedirectResponse
    {
        $student = laravelstudents::findOrFail($id);
        $this->authorize('update', $student);

        $student->update($request->validated());

        return redirect()->route('students.index')
            ->with('success', 'Student record has been updated successfully.');
    }

    /**
     * Delete student from database.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $student = laravelstudents::findOrFail($id);
        $this->authorize('delete', $student);
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student record has been deleted successfully.');
    }
}
