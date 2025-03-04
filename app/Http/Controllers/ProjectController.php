<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $query = Project::query();

        foreach ($filters as $key => $value) {
            if ($attribute = Attribute::where('name', $key)->first()) {
                $query->whereHas('attributeValues', function ($q) use ($attribute, $value) {
                    list($operator, $actualValue) = $this->parseFilterValue($value);
                    if ($operator === 'LIKE') {
                        $q->where('attribute_id', $attribute->id)
                          ->where('value', 'LIKE', $actualValue);
                    } else {
                        $q->where('attribute_id', $attribute->id)
                          ->where('value', $operator, $actualValue);
                    }
                });
            }
            else {
                list($operator, $actualValue) = $this->parseFilterValue($value);
                $query->where($key, $operator, $actualValue);
            }
        }

        $projects = $query->with('attributeValues.attribute')->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ], 200);
    }

    /**
     * Parse filter values to support operators (=, >, <, LIKE)
     */
    private function parseFilterValue($value)
    {
        if (preg_match('/^(>=|<=|>|<|LIKE)(.+)$/', $value, $matches)) {
            return [$matches[1], trim($matches[2])]; // Extract operator and value
        }

        if (str_contains($value, '*')) {
            return ['LIKE', str_replace('*', '%', $value)]; // Convert * to %
        }
        return ['=', $value];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:projects,name,NULL,id,deleted_at,NULL',
            'status' => 'required|integer|in:1,2,3',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*.id' => 'exists:attributes,id',
            'attribute_values.*.value' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $project = Project::create([
                'name' => $data['name'],
                'status' => $data['status'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            foreach ($data['attribute_values'] as $attr_value) {
                if(isset($attr_value['value'])){
                    AttributeValue::create(
                        [
                            'entity_id' => $project->id,
                            'attribute_id' => $attr_value['id'],
                            'value' => $attr_value['value'],
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Project creation failed', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'name' => $project->name,
            'status' => $project->status,
            'attribute_values' => $project->attributeValues->map(function ($attribute) {
                return [
                    'name' => $attribute->attribute->name,
                    'type' => $attribute->attribute->type,
                    'value' => $attribute->value
                ];
            })
        ], 201);
    }

    public function show($id)
    {
        $project = Project::with('attributeValues.attribute')->find($id);
        if (!$project) {
            return response()->json(['success'=> false, 'message' => 'Project not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $project], 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:projects,name,' . $id . ',id,deleted_at,NULL',
            'status' => 'required|integer|in:1,2,3',
            'attribute_values' => 'array',
            'attribute_values.*.id' => 'exists:attributes,id',
            'attribute_values.*.value' => 'required'
        ]);
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['success'=> false, 'message' => 'Project not found'], 404);
        }

        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $project->update([
                'name' => $data['name'],
                'status' => $data['status'],
                'updated_by' => $userId,
            ]);

            foreach ($data['attribute_values'] as $attr_value) {
                AttributeValue::updateOrCreate(
                    [
                        'entity_id' => $project->id,
                        'attribute_id' => $attr_value['id']
                    ],
                    [
                        'value' => $attr_value['value'],
                        'updated_by' => $userId,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Project update failed', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'name' => $project->name,
            'status' => $project->status,
            'attribute_values' => $project->attributeValues->map(function ($attribute) {
                return [
                    'name' => $attribute->attribute->name,
                    'type' => $attribute->attribute->type,
                    'value' => $attribute->value
                ];
            })
        ], 201);
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['success'=> false, 'message' => 'Project not found'], 404);
        }

        DB::beginTransaction();
        try {
            $project->attributeValues()->delete();
            $project->timesheets()->delete();
            $project->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Project deletion failed', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['success'=> true, 'message' => 'Project and related records deleted'], 200);
    }
}
