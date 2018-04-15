<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportExcelRequest;
use App\Http\Requests\SubmitProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Distribution;
use App\Models\Flow;
use App\Models\Logs;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectDistribution;
use App\Models\ProjectFlow;
use App\Models\ProjectPayment;
use App\Models\Role;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Flash;
use Exception;
use StarHub\Excel\Excel;
use StarHub\Secret\Secret;
use Validator;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public $allowed_fields = [
        'num','name',
        'contract_num','cost','contract_price',
        'depute_name','depute_user', 'depute_phone',
        'category_text', 'single','unit','extract','record'
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'side']);
    }

    /**
     * 收款记录
     */
    public function get_money()
    {
        $payment_id = request('num');
        $type = request('type');
        $project_id = request('pid');
        $logs = Logs::where('payment_id',$payment_id)
                ->where('project_id',$project_id)
                ->where('type',$type)
                ->get();
            $total = Logs::where('payment_id',$payment_id)
                ->where('project_id',$project_id)
                ->where('type',$type)
                ->sum('money');
        return view('log.income',compact('logs','total'));
    }

    /**
     *  添加记录
     */
    public function post_money()
    {
        $money = request('money');
        $create_time = request('create_time');
        $type = request('type','income');
        $invoice_num = request('invoice_num');
        $remarks = request('remarks','');
        $payment_id = request('payment_id','');
        $project_id = request('pid');
        $id = Logs::insertGetId([
            'money' => $money,
            'payment_id' => $payment_id,
            'project_id' => $project_id,
            'invoice_num' => $invoice_num,
            'remarks' => $remarks,
            'type' => $type,
            'create_time' => strtotime($create_time)
        ]);
        $total = Logs::where('payment_id',$payment_id)
            ->where('project_id',$project_id)
            ->where('type',$type)
            ->sum('money');

        $shouru = Logs::where('payment_id',$payment_id)
            ->where('project_id',$project_id)
            ->whereIn('type',['income','assess_income','flat_pay'])
            ->sum('money');

        $zhichu = Logs::where('payment_id',$payment_id)
            ->where('project_id',$project_id)
            ->whereIn('type',['other_pay','assess_pay'])
            ->sum('money');

        $xiaoji = $shouru - $zhichu;

        $shouru_zong = Logs::where('project_id',$project_id)
            ->whereIn('type',['income','assess_income','flat_pay'])
            ->sum('money');
        $zhichu_zong = Logs::where('project_id',$project_id)
            ->whereIn('type',['other_pay','assess_pay'])
            ->sum('money');
        $zonge = $shouru_zong - $zhichu_zong;
        if($id){
            return json_encode(['status' => true,
                'info' => '添加成功',
                'total' => $total,
                'xiaoji' => $xiaoji,
                'zonge' => $zonge
            ]);
        }else{
            return json_encode(['status' => false,
                'info' => '添加失败',
                'total' => $total,
                'xiaoji' => $xiaoji,
                'zonge' => $zonge
            ]);
        }
    }

    /*
     * 流转列表
     */
    public function index_flow(Request $request)
    {
        $currentUser = Auth::user();
        $info = $request->only('start_date', 'end_date', 's_division', 's_state', 's_word');
        $div_check = in_array($currentUser->division, [4, 5, 6, 7]);
        $role_check = $currentUser->hasRole('division_manager');

            //流转项目
            $projects = Project::search($info['s_word'], null, true)
                ->where(function ($query) use ($currentUser, $role_check, $div_check) {
                    if(!$div_check && !$role_check) { //既不是特殊人员，也不是该部门的项目经理
                        $query->where('id', 0);
                    }else if(!$div_check && $role_check) { //不是特殊人员，但是该部门的项目经理
                        $query->where('division', $currentUser->division);
                    }else{
                        $query->run();
                    }
                })
                ->where(function ($query) use ($info) {
                    if(!empty($info['start_date']) && !empty($info['end_date'])) { //下单时间
                        $query->datecreate([$info['start_date'], $info['end_date']]);
                    }

                    if(!empty($info['s_division'])) { //部门
                        $query->where('division', $info['s_division']);
                    }

                    if($info['s_state'] != '' && $info['s_state'] != 3) { //流转状态
                        $query->where('state', $info['s_state']);
                    }

                    if($info['s_state'] == 3){
                        $query->where('is_filed', '>',0);
                    }
                })
                ->orderBy('num', 'desc')
                ->paginate(10);


        foreach ($projects as $project) {
            //担当身份
            $assume = [];
            $current_leaders = [];
            $role_data = $currentUser->roles()->get();
            foreach ($role_data as $role) {
                $assume[] = $role->display_name;
            }

            if($project->user_id == $currentUser->id) {
                $assume[] = '订单发起人';
            }

            //参与分配的角色
            $distributions = Distribution::whereHas('projectDistribution', function ($query) use ($project){
                $query->where('project_id', $project->id)->where('num', $project->max_num);
            })->where('user_id', $currentUser->id)->get();

            foreach ($distributions as $distribution) {
                $position = $distribution->position;

                if(in_array($position, [1,2])) {
                    $current_leaders[] = $distribution;
                }
            }

            $project->current_leaders = $current_leaders;

            $assume = array_unique($assume); //去掉重复的身份
            $project->assume = implode('，', $assume);
        }
//        $projects->orderBy('num','desc');

        return view('projects.index_flow', compact('info', 'projects'));
    }

    public function change_data()
    {
        $project_data = Project::all();
        foreach ($project_data as $project){
            $is_filed = Flow::whereHas('projectFlow', function($query) use ($project){
                $query->where('project_id', $project->id)->where('num', $project->max_num);
            })->filed()->count();
            $project->is_filed = $is_filed;
            $project->save();
        }

    }

    /*
     * 分工列表
     */
    public function index_division(Request $request)
    {
        $currentUser = Auth::user();
        $info = $request->only('start_date', 'end_date', 's_division', 's_state', 's_word');

        //分工列表
        $projects = Project::search($info['s_word'], null, true)
            ->where(function ($query) use ($info) {
                if(!empty($info['start_date']) && !empty($info['end_date'])) { //下单时间
                    $query->datecreate([$info['start_date'], $info['end_date']]);
                }

                if(!empty($info['s_division'])) { //部门
                    $query->where('division', $info['s_division']);
                }

                if($info['s_state'] != '' && $info['s_state'] != 3) { //流转状态
                    $query->where('state', $info['s_state']);
                }

                if($info['s_state'] == 3){
                    $query->where('is_filed', '>',0);
                }
            })
            ->member($currentUser)
            ->run()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($projects as $project) {
            //担当身份
            $assume = [];
            $current_leaders = [];

            //参与分配的角色
            $distributions = Distribution::whereHas('projectDistribution', function ($query) use ($project){
                $query->where('project_id', $project->id)->where('num', $project->max_num);
            })->where('user_id', $currentUser->id)->get();

            foreach ($distributions as $distribution) {
                $position = $distribution->position;

                if(in_array($position, [1,2])) {
                    $assume[] = '项目负责人';
                    $current_leaders[] = $distribution;
                }

                if(in_array($position, [3,4])) {
                    $assume[] = '业务助理';
                }

                if(in_array($position, [5,6])) {
                    $assume[] = '项目审核人';
                }

                if(in_array($position, [7])) {
                    $assume[] = '项目经营人';
                }

                if(in_array($position, [8,9,10,11,12,13])) {
                    $assume[] = '专业工程师';
                }
            }

            $project->current_leaders = $current_leaders;

            $assume = array_unique($assume); //去掉重复的身份
            $project->assume = implode('，', $assume);
        }

        return view('projects.index_division', compact('info', 'projects'));
    }

    /*
     * 检索项目
     */
    public function search(Request $request)
    {
        $type = $request->type;
        $info = $request->only('start_date', 'end_date', 's_division', 's_state', 's_word');
        if($type == 'division') {
            return redirect()->action('ProjectController@index_division', $info);
        }else {
            return redirect()->action('ProjectController@index_flow', $info);
        }
    }

    /*
     * 导出已终年审表
     */
    public function export(ExportExcelRequest $request)
    {
        if(!Auth::user()->can('project.export')) {
            return back()->withErrors('没有权限导出终审表');
        }

        //部门
        $division = $request->division;
        $division_text = config('admin.sites.division')[$division];

        //日期区间
        $start_date = empty($request->start_date) ? Carbon::now()->format('Y-m-d'): $request->start_date;
        $end_date = empty($request->end_date) ? $start_date: date('Y-m-d H:i:s',strtotime($request->end_date) + 3600*23 + 3599);
        //文件名
        $start_date_n = Carbon::parse($start_date)->format('Ymd');
        $end_date_n = Carbon::parse($end_date)->format('Ymd');
        $name = $division_text.'奖金核算统计表_'.$start_date_n.'_'.$end_date_n;

        //项目标题（前半部分）
        $project_title = ['序号', '项目'.PHP_EOL.'流转单编号', '项目名称', '奖金合计'.PHP_EOL.'（元）'];

        //项目相关人员
        $relate_users_id = [];
        $relate_users = User::relate($division)->get();
        foreach ($relate_users as $user) {
            $relate_users_id[] = $user->id;
            $project_title[] = $user->name;
        }
        //获取项目信息
        $content = [];
        $projects = Project::whereHas('projectFlow', function($query){
            $query->where('status', 1);
        })->where('division', $division)
//            ->date([$start_date, $end_date])
            ->whereBetween('created_at',[$start_date, $end_date])
            ->get();

        foreach ($projects as $project) {
            $num_count = ProjectFlow::where('project_id', $project->id)
                ->where('status', 1)
                ->pluck('num');

            $projectDistribution = ProjectDistribution::with(['distribution' => function($query){
                    $query->with(['user' => function($que){
                    $que->withTrashed();
                }]);
                }])
                ->where('project_id', $project->id)
                ->whereIn('num', $num_count)
                ->where('status', 1)->get();

            $project->projectDistribution = $projectDistribution;
        }

        //分配人员
        foreach ($projects as $project) {
            foreach ($project->projectDistribution as $projectDistribution) {
                $distributions = $projectDistribution->distribution;
                foreach ($distributions as $distribution) {
                    $member = $distribution->user;

                    if(!in_array($member->id, $relate_users_id)) {
                        $relate_users_id[] = $member->id;
                        $project_title[] = $member->name;
                    }
                }
            }
        }

        //所有项目的全部人员奖金
        foreach ($projects as $num => $project) {
            $item = [
                $num + 1,
                $project->num,
                $project->name,
                0
            ];

            foreach ($relate_users_id as $user_id) {
                $bonus = 0;

                foreach ($project->projectDistribution as $projectDistribution) {

                    foreach ($projectDistribution->distribution as $distribution) {

                        if($distribution->user_id == $user_id) {
                            $bonus += $distribution->bonus;
                        }
                    }

                }

                $item[] = $bonus == 0 ? '':strval($bonus);
            }

            array_push($content, $item);
        }

        //每个项目的全部人员奖金总和
        $relate_users_length = count($relate_users_id);
        foreach ($content as $key => $value) {
            $sum = 0;

            for ($i = 4; $i < $relate_users_length + 4; $i++) {
                $sum += intval($value[$i]);
            }

            $content[$key][3] = $sum == 0 ? '':strval($sum);
        }

        //总计
        $total = ['', '', '合计'];
        for ($i = 3; $i < $relate_users_length + 4; $i++) {
            $sum = 0;

            foreach ($content as $key => $value) {
                $sum += intval($value[$i]);
            }

            $total[$i] = $sum == 0 ? '':strval($sum);
        }
        array_push($content, $total);

        //dd($content);

        //组合数据
        $start_date_t = Carbon::parse($start_date)->format('Y年m月d日');
        $end_date_t = Carbon::parse($end_date)->format('Y年m月d日');

        $cellData = [];
        $cellData[] = ['部门人员奖金核算统计表'];
        $cellData[] = ['部门：'.$division_text.' 区间：'.$start_date_t.'-'.$end_date_t];
        $cellData[] = $project_title;
        $cellData = array_merge($cellData, $content);

        //生成表格并下载
        try {
            $excel = new Excel();
            $excel->passTable($name, $cellData, count($content));
            exit();
        }catch (Exception $exception) {
            return back()->withErrors('导出失败');
        }
    }

    /*
     * 项目添加
     */
    public function add(Request $request)
    {
        if(!Auth::user()->ability('division_manager', 'project.create')) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $request->session()->put('back_url', url()->previous());

        //生成编号
        $num = Secret::num($request->year);

        //所有人员
        $members = User::members()->get();

        return view('projects.add', compact('num', 'members'));
    }

    /*
     * 项目新增
     */
    public function create(StoreProjectRequest $request)
    {
        $currentUser = Auth::user();
        if(!$currentUser->ability('division_manager', 'project.create')) {
            return back()->withInput()->withErrors('没有权限保存项目');
        }

        //事务开始
        DB::beginTransaction();

        try {
            //创建项目
            $data = array_filter($request->only($this->allowed_fields), 'except_empty');
            $data['type'] = Secret::type($request->category);
            $data['category'] = implode(',', $request->category);
            $data['user_id'] = $currentUser->id;
            $data['division'] = $currentUser->division;
            $project = Project::create($data);

            //创建分配信息
            $project_distribution = ProjectDistribution::create([
                'project_id'=> $project->id,
                'num'       => 1,
                'status'    => 0
            ]);

            //创建分配详情
            $dis_length = count($request->dis_position);
            for($i = 0; $i < $dis_length; $i++) {
                if(!empty($request->dis_position[$i]) || !empty($request->dis_member[$i])) {
                    Distribution::create([
                        'project_distribution_id'   => $project_distribution->id,
                        'position'                  => $request->dis_position[$i],
                        'user_id'                   => $request->dis_member[$i],
                        'remark'                    => $request->dis_remark[$i],
                        'bonus'                     => $request->dis_bonus[$i]
                    ]);
                }
            }

            DB::commit();
            Flash::success('保存成功');
            return redirect()->route('project.index_flow');
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withInput()->withErrors('保存失败');
        }
    }

    /*
     * 项目编辑
     */
    public function edit(Request $request, $id)
    {
        if(!Auth::user()->ability('division_manager', 'project.update')) {
            return back()->withErrors('没有权限查看');
        }

        //保存上一页地址
        $request->session()->put('back_url', url()->previous());

        //所有人员
        $members = User::members()->get();

        //项目信息
        $project = Project::findOrFail($id);
        $project->category_arr = explode(',', $project->category);

        //分配信息
        $distributions = Distribution::whereHas('projectDistribution', function ($query) use ($id){
            $query->where('project_id', $id)->where('num', 1);
        })->get();

        return view('projects.edit', compact('members', 'project', 'distributions'));
    }

    public function delete()
    {
        $project_id = request('id');


        $project = Project::where('id',$project_id)->first();

        $logs = ProjectFlow::where('project_id',$project_id)->where('num','>',1)->get();

        if(!$logs->isEmpty()) {
            return json_encode(['msg' => '已流转过的项目无法撤回', 'result' => 2]);
        }
        Project::find($project_id)->update(['state' => 0,'is_filed' => 0]);
        $logs = ProjectFlow::where('project_id',$project_id)->delete();

        $project_flow = ProjectFlow::where('project_id',$project_id)->first();
        $rest = Flow::where('project_flow_id',$project_flow)->where('result',3)->update([
            'user_id' => '',
            'remark' => ''
        ]);
        return json_encode(['msg' => '操作成功','result' => 1]);

    }

    /*
     * 项目更新
     */
    public function update($id, UpdateProjectRequest $request)
    {
        if(!Auth::user()->ability('division_manager', 'project.update')) {
            return back()->withErrors('没有权限更新项目');
        }

        //事务开始
        DB::beginTransaction();

        try {
            $data = array_filter($request->only($this->allowed_fields), 'except_empty');
            $data['type'] = Secret::type($request->category);
            $data['category'] = implode(',', $request->category);
            if(!isset($data['single'])){
                $data['single'] = 0;
            }

            if(!isset($data['unit'])){
                $data['unit'] = 0;
            }
            Project::find($id)->update($data);

            //清理分配人员
            $project_distribution = ProjectDistribution::where('project_id', $id)->where('num', 1)->first();
            if(!empty($project_distribution)) {
                Distribution::where('project_distribution_id', $project_distribution->id)->delete();
            }else {
                $project_distribution = ProjectDistribution::create([
                    'project_id'=> $id,
                    'num'       => 1,
                    'status'    => 0
                ]);
            }

            //创建分配详情
            $dis_length = count($request->dis_position);
            for($i = 0; $i < $dis_length; $i++) {
                if(!empty($request->dis_position[$i]) || !empty($request->dis_member[$i])) {
                    Distribution::create([
                        'project_distribution_id'   => $project_distribution->id,
                        'position'                  => $request->dis_position[$i],
                        'user_id'                   => $request->dis_member[$i],
                        'remark'                    => $request->dis_remark[$i],
                        'bonus'                     => $request->dis_bonus[$i]
                    ]);
                }
            }

            DB::commit();
            Flash::success('保存成功');
            return back();
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('保存失败');
        }
    }

    /*
     * 项目下单
     */
    public function order(SubmitProjectRequest $request)
    {
        $projectType = Secret::type($request->category);
        if(!$request->performCheck($projectType)) {
            return back()->withErrors('项目负责人和项目审核人必须添加');
        }

        $currentUser = Auth::user();
        if(!Auth::user()->ability('division_manager', 'project.order')) {
            return back()->withInput()->withErrors('没有权限下单项目');
        }

        //收集分配人员
        $member_arr = [];
        $count = count($request->dis_position);
        for ($i = 0; $i < $count; $i++) {
            $member_arr[] = $request->dis_member[$i];
        }

        //事务开始
        DB::beginTransaction();

        try {
            $data = array_filter($request->only($this->allowed_fields), 'except_empty');
            $data['type'] = $projectType;
            $data['category'] = implode(',', $request->category);
            $data['state'] = 1; //当前流转状态，已发起

            if(isset($request->id)) {
                $project_id = $request->id;

                Project::find($project_id)->update($data);
                //清理分配人员
                $project_distribution = ProjectDistribution::where('project_id', $project_id)->where('num', 1)->first();
                if(!empty($project_distribution)) {
                    Distribution::where('project_distribution_id', $project_distribution->id)->delete();
                }else {
                    $project_distribution = ProjectDistribution::create([
                        'project_id'=> $project_id,
                        'num'       => 1,
                        'status'    => 0
                    ]);
                }
            }else {
                $data['user_id'] = $currentUser->id;
                $data['division'] = $currentUser->division;
                $project = Project::create($data);

                //创建分配信息
                $project_distribution = ProjectDistribution::create([
                    'project_id'=> $project->id,
                    'num'       => 1,
                    'status'    => 0
                ]);

                $project_id = $project->id;
            }

            //创建分配详情
            $dis_length = count($request->dis_position);
            for($i = 0; $i < $dis_length; $i++) {
                if(!empty($request->dis_position[$i]) && !empty($request->dis_member[$i])) {
                    Distribution::create([
                        'project_distribution_id'   => $project_distribution->id,
                        'position'                  => $request->dis_position[$i],
                        'user_id'                   => $request->dis_member[$i],
                        'remark'                    => $request->dis_remark[$i],
                        'bonus'                     => $request->dis_bonus[$i]
                    ]);
                }
            }

            //创建流转信息
            $project_flow = ProjectFlow::create([
                'project_id'=> $project_id,
                'num'       => 1,
                'status'    => 0
            ]);

            //创建流转详情
            $remark = empty($request->remark) ? '完成' : $request->remark;
            Flow::create([
                'project_flow_id'   => $project_flow->id,
                'user_id'           => $currentUser->id,
                'result'            => 1,
                'remark'            => $remark
            ]);

            for ($i = 2; $i <= 8; $i++) {
                Flow::create([
                    'project_flow_id'   => $project_flow->id,
                    'result'            => $i
                ]);
            }

            //激活用户
            User::whereIn('id', $member_arr)->update(['is_banned' => 0]);

            DB::commit();
            Flash::success('项目已发起');
            return redirect()->route('project.index_flow');
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withInput()->withErrors('下单失败');
        }
    }

}