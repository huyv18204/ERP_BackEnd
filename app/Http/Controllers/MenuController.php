<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getMenuTree()
    {
        $menus = Menu::buildMenuTree();

        return response()->json($menus);

    }

    public function getMenuRoot()
    {
        $menus = Menu::query()->where('parent', null)->get();

        return response()->json($menus);

    }

    public function index(Request $request)
    {

        $label = $request->query('label');
        $url = $request->query('url');
        $icon = $request->query('icon');
        $fax = $request->query('fax');
        $parent = $request->query('parent');

        $menus = Menu::query();

        if ($label) {
            $menus->where('label', 'like', '%' . $label . '%');
        }
        if ($url) {
            $menus->where('url', 'like', '%' . $url . '%');
        }
        if ($icon) {
            $menus->where('icon', 'like', '%' . $icon . '%');
        }

        if ($parent) {
            $menus->where('parent', $parent);
        }

        $menus = $menus->get();
        return response()->json($menus);

    }

    public function store(Request $request)
    {
        if (empty($request->label)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $menu = Menu::query()->create([
            'label' => $request->label,
            'icon' => $request->icon,
            'parent' => $request->parent,
            'url' => $request->url,
//            'url' =>  env('PRE_URL', 'erp-system/') . $request->url,
        ]);
        if (!$menu) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($menu);
    }

    public function destroy(Request $request, $id)
    {
        $menu = Menu::query()->find($id);
        if (!$menu) {

            return response()->json(["message" => "Menu does not exist"]);
        }
        $menu->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::query()->find($id);
        if (!$menu) {
            return response()->json(["message" => "menu does not exist"]);
        }

        if (empty($request->label)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $menu = Menu::query()->where('id', $id)->update([
            'label' => $request->label,
            'icon' => $request->icon,
            'parent' => $request->parent,
            'url' => $request->url,
        ]);

        if (!$menu) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }


}
