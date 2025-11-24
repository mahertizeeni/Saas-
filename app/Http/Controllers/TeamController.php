<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Validation\Validator;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = FacadesAuth::user();
        $ownerteams = $user->ownedTeams;
        $teams= $user->teams;
        $data =  [
            'ownerteams' => $ownerteams,
            'teams' => $teams
        ];
        return ApiResponse::sendResponse(200,'Teams retrieved successfully', $data);
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
    public function store(Request $request)
    {
        $user = FacadesAuth::user();
        $team =Team::create([
            'name' => $request->name ,
            'owner_id' => $user->id
        ]);
        return ApiResponse::sendResponse(201,'Team created successfully', $team);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = Team::with('owner','members')->findOrFail($id);
        $data =[
            'name' => $team->name ,
            'owner' => $team->owner,
            'members' => $team->members
        ];
        return ApiResponse::sendResponse(200,'Team retrieved successfully', $data);
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
    public function update(Request $request, string $id)
    {    $data = $request->validate([
            'name' => 'required|string|max:255'
        ]);
         $user = FacadesAuth::user();
        $team = Team::findOrFail($id);
        if($user->id == $team->owner_id)
        {
        $team->update(['name' => $data['name']]);
        return ApiResponse::sendResponse(200,'Team updated successfully', $team);
        }
        return ApiResponse::sendResponse(403,'Unauthorized to update this team', null);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {  $user = FacadesAuth::user();
        $team = Team::findOrFail($id);
        if($user->id == $team->owner_id)
        {
        $team->members()->detach();
        $team->delete();
        return ApiResponse::sendResponse(200,'Team deleted successfully', $team);
        }
        return ApiResponse::sendResponse(403,'Unauthorized to delete this team', null); 
    }

public function addMember(Request $request, string $id)
{
    // Validate Request
    $data = $request->validate([
        'member_id' => 'required|exists:users,id',
        'role'      => 'required|string'
    ]);

    $authUser = FacadesAuth::user();
    $team = Team::findOrFail($id);

    // Allow only the owner to add members
    if ($authUser->id !== $team->owner_id) {
        return ApiResponse::sendResponse(403, 'Unauthorized to add member to this team', null);
    }

    // Prevent adding the same member twice
    if ($team->members()->where('user_id', $data['member_id'])->exists()) {
        return ApiResponse::sendResponse(409, 'This user is already a member of the team', null);
    }

    // Attach member with role
    $team->members()->attach($data['member_id'], ['role' => $data['role']]);

    return ApiResponse::sendResponse(200, 'Member added successfully', $team->fresh(['members']));
}
public function updateMemberRole(Request $request, string $id, string $memberId)
{  
     $data = $request->validate([
    'role' => 'required|string'
 ]);
 $authUser = FacadesAuth::user();
 $team = Team::findOrFail($id);
 abort_if($authUser->id !== $team->owner_id, 403, 'Unauthorized to update member role in this team');
 
 abort_if(!$team->members()->where('user_id',$memberId)->exists(), 404, 'Member not found in the team');

 $team->members()->updateExistingPivot($memberId, ['role' => $data['role']]);
    return ApiResponse::sendResponse(200, 'Member role updated successfully', $team->fresh(['members']));

}
public function removeMember(string $id, string $memberId)
{ $authUser = FacadesAuth::user();
    $team = Team::findOrFail($id);
    abort_if($authUser->id !== $team->owner_id,403, 'Unauthorized to remove member from this team');

    abort_if(!$team->members()->where('user_id',$memberId)->exists(), 404, 'Member not found in the team');

    $team->members()->detach($memberId);
    return ApiResponse::sendResponse(200, 'Member removed successfully', $team->fresh(['members']));
}  
   

}