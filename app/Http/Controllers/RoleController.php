<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Auth;

class RoleController extends Controller
{
    public $allowed_fields = [
        'name', 'display_name', 'description'
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'side']);
    }

    /*
     * 角色列表
     */
    public function index()
    {
        if(!Auth::user()->can('role')) {
            return back()->withErrors('没有权限查看');
        }

        $roles = Role::paginate(10);
        return view('roles.index', compact('roles'));
    }

    /*
     * 添加角色
     */
    public function add(Request $request)
    {
        if(!Auth::user()->can('role.add')) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $request->session()->put('back_url', url()->previous());

        $perms = Permission::all();

        //一级权限
        $perms_parent = [];
        foreach ($perms as $perm) {
            if($perm->pid == 0) {
                $perms_parent[] = $perm;
            }
        }

        //权限数据处理
        $perms_data = [];
        foreach ($perms_parent as $parent) {
            $child_content = [];
            $parent_content = [
                'id'            => $parent->id,
                'name'          => $parent->name,
                'display_name'  => $parent->display_name,
                'description'   => $parent->description,
            ];

            foreach ($perms as $perm_one) {
                $child_list = [];

                if($perm_one->pid == $parent->id) {
                    array_push($child_list, [
                        'id'            => $perm_one->id,
                        'name'          => $perm_one->name,
                        'display_name'  => $perm_one->display_name,
                        'description'   => $perm_one->description,
                    ]);

                    foreach ($perms as $perm_two) {
                        if($perm_two->pid == $perm_one->id) {
                            array_push($child_list, [
                                'id'            => $perm_two->id,
                                'name'          => $perm_two->name,
                                'display_name'  => $perm_two->display_name,
                                'description'   => $perm_two->description,
                            ]);
                        }
                    }

                    array_push($child_content, $child_list);
                }
            }

            array_push($perms_data, [
               'parent' => $parent_content,
                'child' => $child_content,
            ]);
        }

        return view('roles.add', compact('perms_data'));
    }

    /*
     * 新增角色
     */
    public function create(StoreRoleRequest $request)
    {
        if(!Auth::user()->can('role.add')) {
            return back()->withInput()->withErrors('没有权新增角色');
        }

        //创建角色
        $data = array_filter($request->only($this->allowed_fields));
        $role = Role::create($data);

        //关联权限
        $perms = Permission::whereIn('id', $request->permission)->get();
        $role->attachPermissions($perms);

        flash('新增成功')->success();
        return redirect()->route('role');
    }

    /*
     * 编辑角色
     */
    public function edit($id, Request $request)
    {
        if(!Auth::user()->can('role.edit')) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $request->session()->put('back_url', url()->previous());

        //获取角色数据
        $role = Role::findOrFail($id);

        //超级用户禁止角色操作
        if($role->name == 'admin') {
            abort(404);
        }

        //获取角色的权限
        $role_perms_id = [];
        $role_perms = $role->perms()->get();
        foreach ($role_perms as $role_perm) {
            $role_perms_id[] = $role_perm->id;
        }

        //权限处理
        $perms = Permission::all();

        //一级权限
        $perms_parent = [];
        foreach ($perms as $perm) {
            if($perm->pid == 0) {
                $perms_parent[] = $perm;
            }
        }

        //权限数据处理
        $perms_data = [];
        foreach ($perms_parent as $parent) {
            $child_content = [];
            $parent_content = [
                'id'            => $parent->id,
                'name'          => $parent->name,
                'display_name'  => $parent->display_name,
                'description'   => $parent->description,
                'check'         => in_array($parent->id, $role_perms_id)
            ];

            foreach ($perms as $perm_one) {
                $child_list = [];

                if($perm_one->pid == $parent->id) {
                    array_push($child_list, [
                        'id'            => $perm_one->id,
                        'name'          => $perm_one->name,
                        'display_name'  => $perm_one->display_name,
                        'description'   => $perm_one->description,
                        'check'         => in_array($perm_one->id, $role_perms_id)
                    ]);

                    foreach ($perms as $perm_two) {
                        if($perm_two->pid == $perm_one->id) {
                            array_push($child_list, [
                                'id'            => $perm_two->id,
                                'name'          => $perm_two->name,
                                'display_name'  => $perm_two->display_name,
                                'description'   => $perm_two->description,
                                'check'         => in_array($perm_two->id, $role_perms_id)
                            ]);
                        }
                    }

                    array_push($child_content, $child_list);
                }
            }

            array_push($perms_data, [
                'parent' => $parent_content,
                'child' => $child_content,
            ]);
        }

//        dd($perms_data);

        return view('roles.edit', compact('role', 'perms_data'));
    }

    /*
     * 更新角色
     */
    public function update($id, UpdateRoleRequest $request)
    {
        if(!Auth::user()->can('role.edit')) {
            return back()->withInput()->withErrors('没有权限编辑角色');
        }

        //更新角色
        $data = array_filter($request->only($this->allowed_fields));

        $role = Role::findOrFail($id);
        $role->update($data);

        //关联权限
        $perms = Permission::whereIn('id', $request->permission)->get();
        $role->savePermissions($perms);

        flash('编辑成功')->success();
        return redirect()->route('role');
    }

    /*
     * 删除角色
     */
    public function delete($id)
    {
        if(!Auth::user()->can('role.del')) {
            return response(['result' => 0, 'msg' => '没有权限删除用户']);
        }

        $role = Role::findOrFail($id);

        if($role->forceDelete()) {
            return response(['result' => 1, 'msg' => '删除成功']);
        }else {
            return response(['result' => 0, 'msg' => '删除失败']);
        }
    }
}
