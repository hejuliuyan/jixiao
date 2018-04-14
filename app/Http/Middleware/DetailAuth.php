<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Route;
use App\Models\Distribution;
use App\Models\Project;

class DetailAuth
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
        $currentUser = Auth::user();
        if(!$currentUser->can('project.detail')) {
            return redirect(back_url('project.index_flow'))->withErrors('没有权限查看或操作详情信息');
        }

        //获取项目
        $id = $request->id;
        $project = Project::where('id', $id)->run()->firstOrFail();

        //权限验证
        $distributions = Distribution::whereHas('projectDistribution', function ($query) use ($project){
            $query->where('project_id', $project->id);
        })->where('user_id', $currentUser->id)->get();

        //项目负责人
        $current_leaders = Distribution::whereHas('projectDistribution', function ($query) use ($project){
            $query->where('project_id', $project->id)->where('num', $project->max_num);
        })->where('user_id', $currentUser->id)->whereIn('position', [1,2])->get();

        //业务经理和项目审核人
        $current_assist = Distribution::whereHas('projectDistribution', function ($query) use ($project){
            $query->where('project_id', $project->id)->where('num', $project->max_num);
        })->where('user_id', $currentUser->id)->whereIn('position', [3,4,5,6])->get();

        //遍历
        $navMenu = [];
        $breakSign = false;
        $currentRoute = Route::currentRouteName();
        $route_names = [
            [
                'title' => '基本信息',
                'route' => 'project.detail.base',
            ],
            [
                'title' => '收款信息',
                'route' => 'project.detail.payment',
            ],
            [
                'title' => '分配信息',
                'route' => 'project.detail.distribution',
            ],
            [
                'title' => '流转信息',
                'route' => 'project.detail.flow',
            ]
        ];
        foreach ($route_names as $route_name) {
            if($route_name['route'] == 'project.detail.base' || $route_name['route'] == 'project.detail.distribution') {
                if(!$currentUser->hasRole(['general_manager', 'personnel_manager', 'chief_engineer', 'finance', 'file','engineer']) &&
                    $distributions->isEmpty() &&
                    !($currentUser->hasRole('division_manager') && $currentUser->division == $project->division)) {

                    if($route_name['route'] == $currentRoute) {
                        $breakSign = true;
                        break;
                    }
                }else {
                    $navMenu[] = $route_name;
                }
            }

            if($route_name['route'] == 'project.detail.payment') {
                if(!$currentUser->hasRole(['general_manager', 'personnel_manager', 'chief_engineer', 'finance','file','engineer']) &&
                    $current_leaders->isEmpty() &&
                    !($currentUser->hasRole('division_manager') && $currentUser->division == $project->division)) {

                    if($route_name['route'] == $currentRoute) {
                        $breakSign = true;
                        break;
                    }
                }else {

                    $navMenu[] = $route_name;
                }
            }
            if($route_name['route'] == 'project.detail.flow') {
                if(!$currentUser->hasRole(['general_manager', 'personnel_manager', 'chief_engineer', 'finance', 'file','engineer']) &&
                    $current_leaders->isEmpty() &&
                    $current_assist->isEmpty() &&
                    !($currentUser->hasRole('division_manager') && $currentUser->division == $project->division)) {

                    if($route_name['route'] == $currentRoute) {
                        $breakSign = true;
                        break;
                    }
                }else {
                    $navMenu[] = $route_name;
                }
            }
        }
        if($breakSign) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $prev_url = url()->previous();
        if(strpos($prev_url, 'detail') === false) {
            $request->session()->put('back_url', $prev_url);
        }

        view()->share('nav_menu', $navMenu);
        view()->share('current_leaders', $current_leaders);
        view()->share('current_assist', $current_assist);
        return $next($request);
    }
}
