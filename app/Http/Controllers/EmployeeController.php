<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Hobbies;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $data['hobbies'] = Hobbies::get();
        return view('employee/home', $data);
    }

    public function loading(Request $request)
    {
        if ($request->ajax()) {
            $data = Employees::latest()->get();
            if (request()->ajax()) {
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('select', function ($data) {
                        $checkbox = '<div style="text-align:center; vertical-align:middle;" class="form-check"> <input  class="form-check-input checkEmployee" type="checkbox" id="checkEmployee_' . uniqid() . '"   data-checkbox-val="' . base64_encode($data->id) . '">  </div>';
                        return $checkbox;
                    })
                    ->addColumn('action', function ($data) {
                        $button = '<button type="button" name="edit" id="' . base64_encode($data->id) . '" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="' . base64_encode($data->id) . '" class="delete btn btn-danger btn-sm">Delete</button>';
                        return $button;
                    })->addColumn('hobby', function ($data) {
                        return $data->Hobby->hobby;
                    })
                    ->rawColumns(['action', 'hobby', 'select'])
                    ->make(true);
            }
        }
    }

    public function save(Request $request)
    {

        $rules = array(
            'full_name'    =>  'required',
            'contactNo'     =>  'required',
            'category'     =>  'required',
            'hobby'     =>  'required',
            'image'         =>  'required|image|max:2048'
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $image = $request->file('image');

        $new_name = rand() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('uploads/employee_uploads'), $new_name);

        $form_data = array(
            'name'               =>  $request->full_name,
            'contactNo'          =>  $request->contactNo,
            'category'           =>  $request->category,
            'hobby_id'           =>  $request->hobby,
            'profile_pic'        =>  $new_name
        );

        Employees::insert([$form_data]);
        return response()->json(['success' => 'Data Added successfully.']);
    }

    public function getDetails(Request $request)
    {

        if (request()->ajax()) {
            $id = base64_decode($request->id);
            if ($id) {
                $data =  Employees::findOrFail($id);
                return response()->json(['data' => $data]);
            }
        }
    }

    public function update(Request $request)
    {
        $image_name = $request->hidden_image;
        $image = $request->file('image');
        if ($image != '') {
            $rules = array(
                'full_name'    =>  'required',
                'contactNo'     =>  'required',
                'image'         =>  'required|image|max:2048'
            );
            $error = Validator::make($request->all(), $rules);
            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $image_name);
        } else {
            $rules = array(
                'full_name'    =>  'required',
                'contactNo'     =>  'required',
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }

        $form_data = array(
            'name'               =>  $request->full_name,
            'contactNo'          =>  $request->contactNo,
            'category'           =>  $request->category,
            'hobby_id'           =>  $request->hobby,
            'profile_pic'        =>  $image_name
        );
        Employees::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }
    public function delete(Request $request)
    {
        if (request()->ajax()) {
            if (!is_array($request->id)) {
                $id = base64_decode($request->id);
                if ($id) {
                    $data = Employees::findOrFail($id);
                    $data->delete();
                    return response()->json(['success' => 'Data is successfully deleted']);
                }
            } else {
                if (!empty($request->id)) {
                    foreach ($request->id as $baseid) {
                        $id = base64_decode($baseid);
                        if ($id) {
                            $data = Employees::findOrFail($id);
                            $data->delete();
                        }
                    }
                    return response()->json(['success' => 'Data is successfully deleted']);
                }
            }
        }
    }
}