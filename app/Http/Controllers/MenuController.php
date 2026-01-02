<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return response()->json(Menu::with('children','permission')->orderBy('order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'route' => 'nullable|string',
            'icon' => 'nullable|string',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'permission_id' => 'nullable|exists:permissions,id',
        ]);

        $menu = Menu::create($data);

        return response()->json($menu,201);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'route' => 'nullable|string',
            'icon' => 'nullable|string',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'permission_id' => 'nullable|exists:permissions,id',
        ]);

        $menu->update($data);

        return response()->json($menu);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['deleted' => true]);
    }
}
