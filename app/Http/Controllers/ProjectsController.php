<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($teamId)
    { $user = Auth::user();
      $team = Team::with('projects')->findOrFail($teamId);
      $is_owner = $team->owner_id === $user->id;
      $isMember = $team->members()->where('user_id', $user->id)->exists();


          if (!$is_owner && !$isMember) {
        return ApiResponse::sendResponse(403, 'You are not allowed to view this team projects', null);
        }
        return ApiResponse::sendResponse(200, 'Team projects retrieved successfully', $team->projects);
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
    public function store(Request $request,$teamId)
    {  $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|string|in:pending,active,completed',
                                ]);
        $user = Auth::user();
        $team = Team::findOrFail($teamId);
        $is_owner = $team->owner_id === $user->id;
        $isMember = $team->members()->where('user_id', $user->id)->exists();


          if (!$is_owner && !$isMember) {
        return ApiResponse::sendResponse(403, 'You are not allowed to view this team projects', null);
        }
        $project = $team->projects()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'status' => $data['status'],
            'team_id' => $teamId,
            'created_by' => $user->id
        ]);

        return ApiResponse::sendResponse(201, 'Project created successfully', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, $teamId)
    {
        $user = Auth::user();
        $team = Team::findOrFail($teamId);
        $is_owner = $team->owner_id === $user->id;
        $isMember = $team->members()->where('user_id', $user->id)->exists();


          if (!$is_owner && !$isMember) {
        return ApiResponse::sendResponse(403, 'You are not allowed to view this team projects', null);
        }
        $project = $team->projects()->findOrFail($id);
        return ApiResponse::sendResponse(200, 'Project retrieved successfully', $project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, $teamId)
    {   $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,active,completed',
                                ]);
        $user = Auth::user();
        $team = Team::findOrFail($teamId);
        $is_owner = $team->owner_id === $user->id;
        $isMember = $team->members()->where('user_id', $user->id)->exists();


          if (!$is_owner && !$isMember) {
        return ApiResponse::sendResponse(403, 'You are not allowed to view this team projects', null);
        }
        $project=$team->projects()->findOrFail($id);
        $project->update($data);
        return ApiResponse::sendResponse(200, 'Project updated successfully', $project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
