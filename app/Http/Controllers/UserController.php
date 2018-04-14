<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public $allowed_fields = [
        'name', 'email', 'password','division','is_banned'
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'side']);
    }

    /*
     * 用户列表
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('user')) {
            return back()->withErrors('没有权限查看');
        }

        $info = $request->only('s_division', 's_name');

        $users = User::where(function ($query) use ($info){
            if(!empty($info['s_division'])) {
                $query->where('division', $info['s_division']);
            }

            if(!empty($info['s_name'])) {
                $query->where('name', 'like', '%'.$info['s_name'].'%');
            }
        })->paginate(10);

        foreach ($users as $user) {
            $user->division_text = $user->division == 0 ? '':config('admin.sites.division')[$user->division];

            $roles_name = [];
            $role_data = $user->roles()->get();
            foreach ($role_data as $role) {
                $roles_name[] = $role->display_name;
            }

            $user->roles_name = implode(",", $roles_name);
        }

        return view('users.index', compact('info', 'users'));
    }

    /*
     * 检索用户
     */
    public function search(Request $request)
    {
        $info = $request->only('s_division', 's_name');
        return redirect()->action('UserController@index', $info);
    }

    /*
     * 添加用户
     */
    public function add(Request $request)
    {
        if(!Auth::user()->can('user.add')) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $request->session()->put('back_url', url()->previous());

        $roles = Role::all();
        return view('users.add', compact('roles'));
    }

    /*
     * 新增用户
     */
    public function create(StoreUserRequest $request)
    {
        if(!Auth::user()->can('user.add')) {
            return back()->withInput()->withErrors('没有权限新增用户');
        }

        //创建用户
        $data = array_filter($request->only($this->allowed_fields));
        if(isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = User::create($data);

        //关联角色（多个）
        $roles = Role::whereIn('id', $request->role)->get();
        if(!empty($roles)) {
            $user->attachRoles($roles);
        }

        flash('新增成功')->success();
        return redirect()->route('user');
    }

    /*
     * 编辑用户
     */
    public function edit($id, Request $request)
    {
        if(!Auth::user()->can('user.edit')) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $request->session()->put('back_url', url()->previous());

        //获取用户数据
        $user = User::with('role')->findOrFail($id);

        //获取关联角色
        $user_role = [];
        foreach ($user->role as $role) {
            $user_role[] = $role->id;
        }

        //处理选中的角色
        $role_data = [];
        $roles = Role::all();
        foreach ($roles as $role) {
            $role_data[] = [
                'id'            => $role->id,
                'name'          => $role->name,
                'display_name'  => $role->display_name,
                'description'   => $role->description,
                'check'         => in_array($role->id, $user_role)
            ];
        }

        return view('users.edit', compact('user', 'role_data'));
    }

    /*
     * 更新用户
     */
    public function update($id, UpdateUserRequest $request)
    {
        if(!Auth::user()->can('user.edit')) {
            return back()->withInput()->withErrors('没有权限编辑用户');
        }

        //更新用户
        $data = array_filter($request->only($this->allowed_fields), 'except_empty');
        if(isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = User::findOrFail($id);
        $user->update($data);

        //移除所有角色
        $user->detachRoles();

        //关联角色（多个）
        $roles = Role::whereIn('id', $request->role)->get();
        if(!empty($roles)) {
            $user->attachRoles($roles);
        }

        flash('编辑成功')->success();
        return redirect()->route('user');
    }

    /*
     * 删除用户
     */
    public function delete($id)
    {
        if(!Auth::user()->can('user.del')) {
            return response(['result' => 0, 'msg' => '没有权限删除用户']);
        }

        $user = User::findOrFail($id);
        $user->delete();

        if($user->trashed()) {
            return response(['result' => 1, 'msg' => '删除成功']);
        }else {
            return response(['result' => 0, 'msg' => '删除失败']);
        }
    }

    /*
     * 编辑密码
     */
    public function editPassword()
    {
        return view('users.edit_password');
    }

    /*
     * 更新密码
     */
    public function updatePassword(ResetPasswordRequest $request)
    {
        $user = Auth::user();

        if(!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors('原密码不正确');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        Auth::logout();  //更改完这次密码后，退出这个用户
        return redirect('/login');
    }
}