<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|max:255',
            'slug' => 'required|unique:roles|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Role::create($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function show(Role $role)
    {
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:roles,name,'.$role->id,
            'slug' => 'required|max:255|unique:roles,slug,'.$role->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}