<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class SideAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //用户权限
        $currentUser = Auth::user();
        $div_check = in_array($currentUser->division, [4, 5, 6, 7]);
        $role_check = $currentUser->hasRole('division_manager');

        //侧边栏生成
        $sideMenu = [];
        if($currentUser->can(['project.manager'])) { //项目管理
            $item = [
                "name" => "项目管理",
                "icon" => "fa-th-list",
                "path" => [],
                "list" => []
            ];

            if($currentUser->can(['project'])) {
                //流转项目个数
                $flow_count = Project::where(function ($query) use ($currentUser, $role_check, $div_check) {
                    if(!$div_check && !$role_check) { //既不是特殊人员，也不是该部门的项目经理
                        $query->where('id', 0);
                    }else if(!$div_check && $role_check) { //不是特殊人员，但是该部门的项目经理
                        $query->where('division', $currentUser->division);
                    }else{
                        $query->run();
                    }
                })
                    ->count();

                //分工项目个数
                $division_count = Project::member($currentUser)
                    ->run()
                    ->count();

                $item["path"] = ['project'];
                $item["list"] = [
                    [
                        "route" => "project.index_flow",
                        "icon"  => "fa-book",
                        "name"  => "流转列表",
                        "count" => $flow_count
                    ],
                    [
                        "route" => "project.index_division",
                        "icon"  => "fa-book",
                        "name"  => "分工列表",
                        "count" => $division_count
                    ]
                ];
            }

            $sideMenu[] = $item;
        }

        if($currentUser->can(['system.manager'])) {  //系统管理
            $item = [
                "name" => "系统管理",
                "icon" => "fa-cog",
                "path" => [],
                "list" => []
            ];

            if($currentUser->can(['user'])) {
                array_push($item["path"], "user");
                array_push($item["list"], [
                    "route" => "user",
                    "icon"  => "fa-user",
                    "name"  => "用户列表"
                ]);
            }

            if($currentUser->can(['role'])) {
                array_push($item["path"], "role");
                array_push($item["list"], [
                    "route" => "role",
                    "icon"  => "fa-pencil",
                    "name"  => "角色列表"
                ]);
            }

            $sideMenu[] = $item;
        }

        view()->share('side_menu', $sideMenu);
        return $next($request);
    }
}
