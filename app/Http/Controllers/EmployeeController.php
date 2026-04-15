<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Service;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::query()
            ->whereHas('user', function ($q) {
                $q->role('employee');
            })
            ->with(['user' => function ($q) {
                $q->role('employee')
                    ->orderBy('created_at', 'asc'); // 🔑 URUT STUDIO
            }])
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->orderBy('users.created_at', 'asc') // 🔥 KUNCI URUTAN
            ->select('employees.*')
            ->get();

        $employeesData = $employees->map(function ($emp) {
            if (!$emp->user) return null;

            return [
                'id' => $emp->id,
                'role' => 'employee',
                'user' => [
                    'name' => $emp->user->name,
                    'image_url' => $emp->user->profileImage(),
                ],
            ];
        })->filter()->values();


        return response()->json([
            'employees' => $employeesData
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'sometimes|string|max:255|email:rfc,dns',
            'bio' =>  'nullable|string',
            'social' =>  'nullable|string',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }


    public function updateBio(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'bio' => 'nullable|string|max:2000',
            'social' => 'nullable'
        ]);

        $employee->update($data);
        return back()->withSuccess('Bio berhasil diperbarui');
    }

    public function categories(Employee $employee)
    {
        $categories = Category::whereHas('services', function ($q) use ($employee) {
            $q->whereHas('employees', function ($e) use ($employee) {
                $e->where('employees.id', $employee->id);
            });
        })->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    public function servicesByCategory(Employee $employee, $categoryId)
    {
        $services = $employee->services()
            ->where('category_id', $categoryId)
            ->with('category')
            ->get();

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }
}
