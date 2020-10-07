<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use Validator;

class ContactController extends Controller
{
    //
    public function get()
    {
        return Contact::all();
    }
    public function specific($id)
    {
        return Contact::find($id);
    }
    public function create(Request $request)
    {
        $rules = array(
            "name" => "required",
            "address" => "required",
            "phone" => "required||min:9||max:12"
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $item = new Contact;
            $item->name = $request->name;
            $item->address = $request->address;
            $item->phone = $request->phone;
            $item->save();
            if ($item->save()) {
                return response()->json(['success' => $item], 200);
            } else {
                return response()->json(['hello' => 'asdasdasd'], 422);
            }
        }
    }
    public function update(Request $request)
    {
        $data = Contact::find($request->id);

        if ($data) {
            $data->name = $request->name;
            $data->address = $request->address;
            $data->phone = $request->phone;

            $result = $data->save();
            if ($result) {
                return response()->json(['success' => $result, 'data' => $data], 200);
            } else {
                return response()->json(['error' => 'Operation Fail'], 422);
            }
        } else {
            return response()->json(['error' => 'No Record Found'], 422);
        }
    }
    public function search($name)
    {
        $data = Contact::where("name", "like", "%" . $name . "%")->get();

        if ($data != []) {
            return response()->json(['success' => true, 'data' => $data], 200);
        } else {
            return response()->json(['error' => 'No Record Found'], 422);
        }
    }
    public function delete($id)
    {
        $data = Contact::find($id);
        $result = $data->delete();

        if ($result) {
            return response()->json(['success' => true, 'data' => $data], 200);
        } else {
            return response()->json(['error' => 'unable to delete Found'], 422);
        }
    }
}
