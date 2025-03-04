<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $timesheets = Timesheet::with(['user', 'project'])->where('user_id', $userId)->get();
        // Hide the user_id and project_id fields
        $timesheets->each(function ($timesheet) {
            $timesheet->makeHidden(['user_id', 'project_id']);
            $timesheet->user->makeHidden(['id']);
        });

        return response()->json([
            'success' => true,
            'data' => $timesheets
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_name' => 'required|string',
            'date' => 'required|date',
            'hours' => 'required|integer',
            'project_id' => 'required|exists:projects,id',
        ]);
        $userId = Auth::id();

        $project = Project::find($data['project_id']);
        if(!$project) {
            return response()->json(['success'=> false, 'message' => 'Project not found'], 404);
        }

        $data = array_merge($data, ['user_id' => $userId, 'project_id' => $project->id]);

        $timesheet = Timesheet::create($data);
        $responseData = [
            'id' => $timesheet->id,
            'user_name' => optional($timesheet->user)->first_name . ' ' . optional($timesheet->user)->last_name,
            'project' => $project->name,
            'task_name' => $timesheet->task_name,
            'date' => $timesheet->date,
            'hours' => $timesheet->hours,
        ];
        return response()->json(['success' => true, 'data' => $responseData], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'task_name' => 'required|string',
            'date' => 'required|date',
            'hours' => 'required|integer',
            'project_id' => 'required|exists:projects,id',
        ]);

        $userId = Auth::id();
        $project = Project::find($data['project_id']);
        if(!$project) {
            return response()->json(['success'=> false, 'message' => 'Project not found'], 404);
        }
        $timesheet = Timesheet::find($id);
        if(!$timesheet || $timesheet->user_id != $userId || $timesheet->project_id != $project->id) {
            return response()->json(['success'=> false, 'message' => 'Timesheet not found'], 404);
        }
        $timesheet->update([
            'task_name' => $data['task_name'],
            'date' => $data['date'],
            'hours' => $data['hours'],
        ]);
        $responseData = [
            'id' => $timesheet->id,
            'user_name' => optional($timesheet->user)->first_name . ' ' . optional($timesheet->user)->last_name,
            'project' => $project->name,
            'task_name' => $timesheet->task_name,
            'date' => $timesheet->date,
            'hours' => $timesheet->hours,
        ];
        return response()->json(['success' => true, 'data' => $responseData], 201);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $timesheet = Timesheet::where('user_id', $userId)->find($id);
        if(!$timesheet) {
            return response()->json(['success'=> false, 'message' => 'Timesheet not found'], 404);
        }
        $timesheet->delete();
        return response()->json(['success'=> true, 'message' => 'Timesheet deleted'], 200);
    }

    public function show($id)
    {
        $userId = Auth::id();
        $timesheet = Timesheet::with('user')->where('user_id', $userId)->find($id);
        if(!$timesheet) {
            return response()->json(['success'=> false, 'message' => 'Timesheet not found'], 404);
        }
        $responseData = [
            'id' => $timesheet->id,
            'user_name' => optional($timesheet->user)->first_name . ' ' . optional($timesheet->user)->last_name,
            'task_name' => $timesheet->task_name,
            'date' => $timesheet->date,
            'hours' => $timesheet->hours,
            'project_id' => $timesheet->project_id,
        ];
        return response()->json(['success'=> true, 'data' => $responseData]);
    }
}
