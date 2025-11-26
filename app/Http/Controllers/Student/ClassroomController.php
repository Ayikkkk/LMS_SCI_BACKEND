<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $classrooms = Classroom::where('id', $student->classroom_id)->get();

        return response()->json([
            'classrooms' => $classrooms
        ]);
    }

    public function show($id)
    {
        $classroom = Classroom::with('students', 'teacher')
            ->where('id', $id)
            ->first();

        return response()->json($classroom);
    }
}

