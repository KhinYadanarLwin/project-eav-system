<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::all();
        return response()->json([
            'success' => true,
            'data' => $attributes
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:attributes,name',
            'type' => 'required|in:text,date,number,select'
        ]);
        $userId = Auth::id();
        $data = array_merge($data, ['created_by' => $userId, 'updated_by' => $userId]);
        $attribute = Attribute::create($data);

        return response()->json(['success' => true, 'name' => $attribute->name, 'type' => $attribute->type], 201);
    }

    public function show($id)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }
        return response()->json($attribute, 200);
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return response()->json(['success'=> false, 'message' => 'Attribute not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|unique:attributes,name,' . $id,
            'type' => 'required|in:text,date,number,select'
        ]);
        $userId = Auth::id();
        $data = array_merge($data, ['updated_by' => $userId]);
        $attribute->update($data);

        return response()->json(['success' => true, 'data' => $attribute], 200);
    }

    public function destroy($id)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }
        $attribute->attributeValues()->delete();
        $attribute->delete();

        return response()->json(['success' => true, 'message' => 'Attribute and related values deleted'], 200);
    }
}
