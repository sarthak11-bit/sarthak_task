<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Yajra\DataTables\DataTables;
use MongoDB\BSON\UTCDateTime;


class EmployeeController extends Controller
{
    public function index()
    {
        return view('content');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'joiningDate' => 'required|date',
            'profileImage' => 'required|image|max:2048',
        ]);

      
        if ($request->hasFile('profileImage')) {
            $file = $request->file('profileImage');
            $path = $file->store('profile_images', 'public'); 
            $validatedData['profileImage'] = $path;
        }

        $validatedData['employeeCode'] = 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT);

        Employee::create($validatedData);

        return response()->json(['success' => true]);
    }

    public function getEmployees(Request $request)
    {
        
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $query = [];

        if ($startDate) {
            $query[] = ['joiningDate' => ['$gte' => new UTCDateTime(strtotime($startDate) * 1000)]];
        }

        if ($endDate) {
            $query[] = ['joiningDate' => ['$lte' => new UTCDateTime(strtotime($endDate) * 1000)]];
        }

       
        if (count($query) > 0) {
            $employees = Employee::where($query)->get();
        } else {
            $employees = Employee::all(); // Fetch all employees if no date filter is applied
        }

        return response()->json($employees);
        // $employees = Employee::all();
        // return response()->json($employees);
    }

   
}
