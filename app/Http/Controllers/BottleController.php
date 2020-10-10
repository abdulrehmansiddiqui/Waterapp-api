<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\Bottle;
use Validator;
use Illuminate\Support\Facades\Auth;

class BottleController extends Controller
{
    public function index($id = null)
    {
        $user = Auth::user();
        // return $id ? Bottle::find($id) : Bottle::all();
        $contactdata = Bottle::where('u_id', $user->id)->Where('c_id', $id)->get();
        if ($contactdata != '[]') {
            $newdata = $contactdata->map(function ($item) {
                $item->check = false;
                return $item;
            });
            return response()->json([
                'list' => $newdata,
            ], 200);
        } else {
            return response()->json(['message' => 'No record found'], 422);
        }
    }

    public function create(Request $request)
    {
        $rules = array(
            "num_of_bottle" => "required",
            "c_id" => "required",
            "price" => "required"
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $user = Auth::user();

            $item = new Bottle;
            $item->num_of_bottle = $request->num_of_bottle;
            $item->c_id = $request->c_id;
            $item->u_id = $user->id;
            $item->price = $request->price;
            $item->status = 'No';
            $item->save();
            if ($item->save()) {
                return response()->json(['success' => "your data has been added", 'data' => $item], 200);
            } else {
                return response()->json(['hello' => 'asdasdasd'], 422);
            }
        }
    }
    public function amountpaid(Request $request)
    {
        $user = Auth::user();

        $Bottledata = Bottle::where('u_id', $user->id)->where('c_id', $request->c_id)->where('status', "No")->get();

        if ($Bottledata == '[]') {
            return response()->json([
                'success' => false,
                'message' => 'No Record found'
            ], 404);
        }

        $newdata = $Bottledata->map(function ($item) {
            $item->status = 'Yes';
            // $item->update($item->all());
            return  $item->save();
        });

        return response()->json(['success' => true, 'message' => 'Amount updated successfully', 'status' => $newdata]);
    }
    public function amountpaidSingle(Request $request)
    {
        $Bottledata = Bottle::find($request->b_id);
        if ($Bottledata == '') {
            return response()->json([
                'success' => false,
                'message' => 'No Record found'
            ], 404);
        }

        $Bottledata->status = 'Yes';
        $Bottledata->save();
        if ($Bottledata->save()) {
            return response()->json(['success' => true, 'message' => 'Amount updated successfully', 'status' => $Bottledata]);
        } else {
            return response()->json(['message' => 'Error to update'], 422);
        }
    }
    public function bottlehave(Request $request)
    {
        $Bottledata = Contact::find($request->c_id);
        if ($Bottledata == '') {
            return response()->json([
                'success' => false,
                'message' => 'No Record found'
            ], 404);
        }
        $Bottledata->num_of_bottle = $request->bottle;
        $Bottledata->save();
        if ($Bottledata->save()) {
            return response()->json(['success' => "your data has been Update", 'data' => $Bottledata], 200);
        } else {
            return response()->json(['message' => 'Error to update'], 422);
        }
    }
}
