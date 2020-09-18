<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;

class RolesController extends Controller
{

    public function __construct() 
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // get all roles
        $roles = Role::all();

        return view('pages.roles.index', compact('roles'));
    }


    public function create()
    {
        // get all permissions
        $permissions = Permission::all();

        return view('pages.roles.create', compact('permissions'));
    }


    public function store(Request $request)
    {
        // validate inputs
        $validator = $request->validate([
            'roleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permission' => ['required']
        ]);

        // add role name
        $role = Role::create([
            'name' => $request['roleName'],
        ]);

        // add permission to this role
        $role->givePermissionTo($request['permission']);

        
        return redirect()->route('roles.index')
            ->with('success', 'Role successfuly created!');
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        // find role
        $role = Role::where('id', $id)->firstOrFail();

        // get all permissions
        $permissions = Permission::all();

        return view('pages.roles.edit', compact('role', 'permissions'));
    }


    public function update(Request $request, $id)
    {
        // validate inputs
        $validator = $request->validate([
            'roleName' => ['required', 'string', 'max:255', 'unique:roles,name,' . $id],
            'permission' => ['required']
        ]);

        // find role and update
        $role = Role::where('id', $id)
            ->update([
                'name' => $request['roleName']
            ]);

        // find role data
        $role = Role::find($id);

        // get role all permissions
        $all_permission = $role->getAllPermissions();

        // then remove all permission to role
        $role->revokePermissionTo($all_permission);

        // then add new permission
        $role->givePermissionTo($request['permission']);

        return redirect()->route('roles.index')
            ->with('success', 'Role successfully updated!');
    }


    public function destroy($id)
    {
        //
    }
}
