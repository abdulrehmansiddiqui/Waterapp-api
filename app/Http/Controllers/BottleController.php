<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bottle;
use Validator;

class BottleController extends Controller
{
    public function index($id = null)
    {
        return $id ? Bottle::find($id) : Bottle::all();
    }

    public function create(Request $request)
    {
        $rules = array(
            "num_of_bottle" => "required",
            "c_id" => "required",
            "u_id" => "required",
            "price" => "required"
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $item = new Bottle;
            $item->num_of_bottle = $request->num_of_bottle;
            $item->c_id = $request->c_id;
            $item->u_id = $request->u_id;
            $item->price = $request->price;
            $item->save();
            if ($item->save()) {
                return response()->json(['success' => "your data has been added", 'data' => $item], 200);
            } else {
                return response()->json(['hello' => 'asdasdasd'], 422);
            }
        }
    }
}
