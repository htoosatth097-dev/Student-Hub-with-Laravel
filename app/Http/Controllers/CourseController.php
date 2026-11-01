<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCourseRequest;
use App\Models\courses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', courses::class);

        $courses = courses::withCount('students')->orderBy('id')->get();

        return view('courses.index', compact('courses'));
    }

    public function store(SaveCourseRequest $request): RedirectResponse
    {
        $this->authorize('create', courses::class);
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            courses::create([
                ...$data,
                'course_code' => courses::nextCourseCode(),
            ]);
        });

        return redirect()->route('courses.index')->with('success', 'Course created successfully!');
    }

    public function edit(courses $course): View
    {
        $this->authorize('update', $course);

        return view('courses.edit', compact('course'));
    }

    public function update(SaveCourseRequest $request, courses $course): RedirectResponse
    {
        $this->authorize('update', $course);
        $course->update($request->validated());

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(courses $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        if ($course->students()->exists()) {
            return redirect()->route('courses.index')->withErrors([
                'course' => 'This course cannot be deleted while students are enrolled in it. Reassign those students first.',
            ]);
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
