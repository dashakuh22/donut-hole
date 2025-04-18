<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::paginate(10)
            ->through(fn($employee) => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'gender' => $employee->gender,
                'salary' => $employee->salary,
            ]);

        return Response::json([
            'message' => 'OK',
            'data' => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'patronymic' => 'required',
            'gender' => 'required|in:male,female',
            'salary' => 'required|integer|min:0',
            'departments' => 'required|array|min:1',
            'departments.*' => [
                Rule::in(Department::pluck('id')->toArray()),
                'numeric'
            ]
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (
            Employee::where('name', $request->name)
                ->where('surname', $request->surname)
                ->where('patronymic', $request->patronymic)
                ->first()
        ) {
            return Response::json([
                'message' => 'Employee already exists',
            ], 409);
        }

        $employee = Employee::create($request->all());
        $employee->departments()->sync($request->departments);

        return Response::json([
            'message' => 'Created',
            'data' => $employee
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = Employee::with('departments')->find($id);

        if (!isset($employee)) {
            return Response::json([
                'message' => 'Employee not found'
            ], 404);
        }

        return Response::json([
            'message' => 'OK',
            'data' => $employee
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'patronymic' => 'required',
            'gender' => 'required|in:male,female',
            'salary' => 'required|integer|min:0',
            'departments' => 'required|array|min:1',
            'departments.*' => [
                Rule::in(Department::pluck('id')->toArray()),
                'numeric'
            ]
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = Employee::find($id);

        if (!isset($employee)) {
            return Response::json([
                'updated' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->update($request->all());
        $employee->departments()->sync($request->departments);

        return Response::json([
            'updated' => true,
            'message' => 'OK',
            'data' => $employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!isset($employee)) {
            return Response::json([
                'deleted' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->delete();

        return Response::json([
            'deleted' => true,
            'message' => 'No Content'
        ], 204);
    }
}
