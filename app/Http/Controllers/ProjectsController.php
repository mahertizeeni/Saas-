<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectsController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index($teamId)
    {
        $team = Team::with('projects')->findOrFail($teamId);
        $this->authorize('access', $team);
        
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
        $team = Team::findOrFail($teamId);
        $this->authorize('access', $team);
        $project = $team->projects()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'status' => $data['status'],
            'created_by' => Auth::id()
        ]);

        return ApiResponse::sendResponse(201, 'Project created successfully', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $this->authorize('access', $team);
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
        $team = Team::findOrFail($teamId);
        $this->authorize('access', $team);
        $project=$team->projects()->findOrFail($id);
        $project->update($data);
        return ApiResponse::sendResponse(200, 'Project updated successfully', $project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $teamId)
    {
      $user = Auth::user();
        $team = Team::findOrFail($teamId);
        $this->authorize('access', $team);
        $project=$team->projects()->findOrFail($id);
        $project->delete();
        return ApiResponse::sendResponse(200, 'Project deleted successfully', null);
    }
}
