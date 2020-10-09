<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\Bottle;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\User;

class ContactController extends Controller
{
    //
    public function get()
    {
        $user = Auth::user();
        // return Contact::where('u_id', $user->id)->get();
        $contactdata = Contact::where('u_id', $user->id)->get();
        if ($contactdata != '[]') {
            $newdata = $contactdata->map(function ($item) {
                $contactdata = Bottle::where('status', 'No')->Where('c_id', $item->id)->get();

                $newdata2 = $contactdata->map(function ($rec) {
                    return $rec->num_of_bottle * $rec->price;
                });

                $total = $newdata2->sum();

                $item->pendingamount = $total > 0 ? $total : 0;
                return $item;
            });

            return response()->json([
                'contactdata' => $newdata,
            ], 200);
        } else {
            return response()->json(['message' => 'No record found'], 422);
        }
    }
    public function specific($id)
    {
        $user = Auth::user();
        $contactdata = Contact::where('u_id', $user->id)->Where('id', $id)->get();

        if ($contactdata != '[]') {
            return $contactdata;
        } else {
            return response()->json(['message' => 'No record found'], 422);
        }
        // return Contact::find($id);

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
            $user = Auth::user();

            $item = new Contact;
            $item->name = $request->name;
            $item->address = $request->address;
            $item->phone = $request->phone;
            $item->num_of_bottle = 0;
            $item->u_id = $user->id;
            $item->price_bottle = $request->price_bottle;
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
