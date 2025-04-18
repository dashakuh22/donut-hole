<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount('employees')
            ->withMax('employees', 'salary')
            ->paginate(10);

        return Response::json([
            'message' => 'OK',
            'data' => $departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Department::firstWhere('name', $request->name)) {
            return Response::json([
                'message' => 'Department already exists',
            ], 409);
        }

        $department = Department::create($request->all());

        return Response::json([
            'message' => 'OK',
            'data' => $department
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $department = Department::find($id);

        if (!isset($department)) {
            return Response::json([
                'message' => 'Department not found'
            ], 404);
        }

        return Response::json([
            'message' => 'OK',
            'data' => $department
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Department::firstWhere('name', $request->name)) {
            return Response::json([
                'updated' => false,
                'message' => 'Department already exists',
            ], 409);
        }

        $department = Department::find($id);

        if (!isset($department)) {
            return Response::json([
                'updated' => false,
                'message' => 'Department not found'
            ], 404);
        }
        $department->update(['name' => $request->name]);

        return Response::json([
            'updated' => true,
            'message' => 'OK',
            'data' => $department
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $department = Department::with('employees')->find($id);

        if (!isset($department)) {
            return Response::json([
                'deleted' => false,
                'message' => 'Department not found'
            ], 404);
        }

        if (count($department->employees)) {
            return Response::json([
                'deleted' => false,
                'message' => 'Department has employees'
            ], 409);
        }

        $department->delete();

        return Response::json([
            'deleted' => true,
            'message' => 'No Content'
        ], 204);
    }
}
