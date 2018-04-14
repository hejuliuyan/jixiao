<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPaymentRequest;
use App\Http\Requests\UpdateBaseRequest;
use App\Http\Requests\UpdateDistributionRequest;
use App\Http\Requests\UpdatePaymentRequest;
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
use DB;
use Flash;
use Exception;
use Validator;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public $allowed_fields = [
        'num','name',
        'contract_num','cost','contract_price',
        'depute_name','depute_user', 'depute_phone',
        'category_text', 'single','unit','extract','record'
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'side', 'detail']);
    }

    /*
     * 基本信息
     */
    public function base($id)
    {
        //项目信息
        $project = Project::where('id', $id)->run()->firstOrFail();
        $project->category_arr = explode(',', $project->category);

        //是否归档
        $is_filed = Flow::whereHas('projectFlow', function($query) use ($project){
            $query->where('project_id', $project->id)->where('num', $project->max_num);
        })->filed()->count();

        return view('projects.partials.base', compact('project', 'is_filed'));
    }

    /*
     * 更新基本信息
     */
    public function baseUpdate($id, UpdateBaseRequest $request)
    {
        try {
            $data = $request->only($this->allowed_fields);
            $data = array_filter($data, 'except_empty');

            $project = Project::findOrFail($id);
            $project->update($data);

            Flash::success('保存成功');
            return back();
        }catch (Exception $exception) {
            return back()->withErrors('保存失败');
        }
    }

    /*
     * 归档员填写合同编号
     */
    public function baseFile($id, Request $request)
    {
        //数据验证
        $validator = Validator::make($request->all(), [
            'max_num'       => 'required',
            'contract_num'  => 'required'
        ]);

        if($validator->fails()) {
            return back()->withErrors('提交数据有误');
        }

        //事务开始
        DB::beginTransaction();

        try {
            //更新项目信息
            $project = Project::findOrFail($id);
            $project->update(['contract_num' => $request->contract_num,'is_filed' => 1]);

            //更新流转信息
            $projectFlow = ProjectFlow::where('project_id', $id)->where('num', $request->max_num)->first();
            $projectFlow->touch();

            //更新流转详情
            $remark = empty($request->remark) ? '完成' : $request->remark;
            Flow::where('project_flow_id', $projectFlow->id)
                ->where('result', 3)
                ->update([
                    'user_id'=> Auth::id(),
                    'remark' => $remark
                ]);

            DB::commit();
            Flash::success('提交成功');
            return redirect()->route('project.detail.flow', $id);
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('提交失败');
        }
    }

    /*
     * 收款信息
     */
    public function payment($id)
    {
        //项目信息
        $project = Project::where('id', $id)->run()->firstOrFail();

        //收款信息
        $payments = Payment::whereHas('projectPayment', function($query) use ($id){
            $query->where('project_id', $id);
        })->with('projectPayment')->get();

        $logs = $payments;
        foreach ($logs as $key => $val){
            $val->detail = json_decode($val->detail);
            $this->check_data($key,'income',$id,$val->detail->income);
            $this->check_data($key,'assess_income',$id,$val->detail->assess_income);
            $this->check_data($key,'assess_pay',$id,$val->detail->assess_pay);
            $this->check_data($key,'flat_pay',$id,$val->detail->flat_pay);
            $this->check_data($key,'other_pay',$id,$val->detail->other_pay);
            $val->detail = json_encode($val->detail);
        }

        foreach ($payments as $key => &$val){
            $val->detail = json_decode($val->detail);
            $income = Logs::where('project_id',$id)
                ->where('payment_id',$key+1)
                ->where('type','income')
                ->sum('money');

            $assess_income = Logs::where('project_id',$id)
                ->where('payment_id',$key+1)
                ->where('type','assess_income')
                ->sum('money');

            $assess_pay = Logs::where('project_id',$id)
                ->where('payment_id',$key+1)
                ->where('type','assess_pay')
                ->sum('money');

            $flat_pay = Logs::where('project_id',$id)
                ->where('payment_id',$key+1)
                ->where('type','flat_pay')
                ->sum('money');

            $other_pay = Logs::where('project_id',$id)
                ->where('payment_id',$key+1)
                ->where('type','other_pay')
                ->sum('money');

            empty($income) ? $income = 0 : $income;
            empty($flat_pay) ? $flat_pay = 0 : $flat_pay;
            empty($assess_income) ? $assess_income = 0 : $assess_income;
            empty($assess_pay) ? $assess_pay = 0 : $assess_pay;
            empty($other_pay) ? $other_pay = 0 : $other_pay;

            $val->detail->income = $income;
            $val->detail->assess_income = $assess_income;
            $val->detail->assess_pay = $assess_pay;
            $val->detail->flat_pay = $flat_pay;
            $val->detail->other_pay = $other_pay;

            $val->subtotal = ($income + $flat_pay+$assess_income) - ($assess_pay + $other_pay);
            $val->detail = json_encode($val->detail);
        }

        return view('projects.partials.payment', compact('project', 'payments','id'));
    }

    public function check_data($payment_id,$type,$id,$money)
    {
        $assess_income = Logs::where('project_id',$id)
            ->where('payment_id',$payment_id+1)
            ->where('type',$type)
//            ->where('money',$money)
            ->first();
        if(empty($assess_income) && $money){
             Logs::insert([
                'project_id' => $id,
                'payment_id' => $payment_id+1,
                'type' => $type,
                'money' => $money,
                'create_time' => time(),
                'remarks' => '历史数据系统自动插入'
            ]);
        }
    }

    /*
     * 更新收款信息
     */
    public function paymentUpdate($id, UpdatePaymentRequest $request)
    {
        //事务开始
        DB::beginTransaction();
        try {
            $project_payment = ProjectPayment::where('project_id', $id)
                ->where('num', $request->max_num)
                ->first();

            if(!empty($project_payment)) {
                Payment::where('project_payment_id', $project_payment->id)->delete();
            }else {
                $project_payment = ProjectPayment::create([
                    'project_id'    => $id,
                    'num'           => $request->max_num,
                    'status'        => 0
                ]);
            }

//            $payment_total = 0;
            //判断是否是全过程造价
//            if($request->max_num == 2){
//                Logs::where('project_id')
//            }

            $shouru_zong = Logs::where('project_id',$id)
                ->whereIn('type',['income','assess_income','flat_pay'])
                ->sum('money');
            $zhichu_zong = Logs::where('project_id',$id)
                ->whereIn('type',['other_pay','assess_pay'])
                ->sum('money');

            $payment_total = $shouru_zong - $zhichu_zong;

            $count = count($request->type);
            for ($i = 0; $i < $count; $i++) {
                //判断是否为全过程
                if($request->max_num > 1){
                    $y = $request->biaozhi[0];
                    $income = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->where('type','income')
                        ->sum('money');

                    $assess_income = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->where('type','assess_income')
                        ->sum('money');

                    $flat_pay = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->where('type','flat_pay')
                        ->sum('money');

                    $other_pay = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->where('type','other_pay')
                        ->sum('money');

                    $assess_pay = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->where('type','assess_pay')
                        ->sum('money');

                    empty($income) ? $income = 0 : $income;
                    empty($assess_income) ? $assess_income = 0 : $assess_income;
                    empty($flat_pay) ? $flat_pay = 0 : $flat_pay;
                    empty($other_pay) ? $other_pay = 0 : $other_pay;
                    empty($assess_pay) ? $assess_pay = 0 : $assess_pay;
                    empty($shouru) ? $shouru = 0 : $shouru;
                    empty($zhichu) ? $zhichu = 0 : $zhichu;

                    $detail = [
                        "income"        => $income,
                        "assess_income" => $assess_income,
                        "assess_pay"    => $assess_pay,
                        "flat_pay"      => $flat_pay,
                        "other_pay"     => $other_pay,
                    ];

                    $shouru = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->whereIn('type',['income','assess_income','flat_pay'])
                        ->sum('money');
                    $zhichu = Logs::where('payment_id',$i+$y)
                        ->where('project_id',$id)
                        ->whereIn('type',['other_pay','assess_pay'])
                        ->sum('money');


                }else{
                    $income = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->where('type','income')
                        ->sum('money');

                    $assess_income = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->where('type','assess_income')
                        ->sum('money');

                    $flat_pay = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->where('type','flat_pay')
                        ->sum('money');

                    $other_pay = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->where('type','other_pay')
                        ->sum('money');

                    $assess_pay = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->where('type','assess_pay')
                        ->sum('money');

                    empty($income) ? $income = 0 : $income;
                    empty($assess_income) ? $assess_income = 0 : $assess_income;
                    empty($flat_pay) ? $flat_pay = 0 : $flat_pay;
                    empty($other_pay) ? $other_pay = 0 : $other_pay;
                    empty($assess_pay) ? $assess_pay = 0 : $assess_pay;
                    empty($shouru) ? $shouru = 0 : $shouru;
                    empty($zhichu) ? $zhichu = 0 : $zhichu;

                    $detail = [
                        "income"        => $income,
                        "assess_income" => $assess_income,
                        "assess_pay"    => $assess_pay,
                        "flat_pay"      => $flat_pay,
                        "other_pay"     => $other_pay,
                    ];

                    $shouru = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->whereIn('type',['income','assess_income','flat_pay'])
                        ->sum('money');

                    $zhichu = Logs::where('payment_id',$i+1)
                        ->where('project_id',$id)
                        ->whereIn('type',['other_pay','assess_pay'])
                        ->sum('money');
                }
//                    $payment_total += $request->subtotal[$i];


//                $detail = [
//                    "income"        => $request->income[$i],
//                    "assess_income" => $request->assess_income[$i],
//                    "assess_pay"    => $request->assess_pay[$i],
//                    "flat_pay"      => $request->flat_pay[$i],
//                    "other_pay"     => $request->other_pay[$i],
//                ];



                $xiaoji = $shouru - $zhichu;

                    Payment::create([
                        'project_payment_id'    => $project_payment->id,
                        'user_id'               => Auth::id(),
                        'type'                  => $request->type[$i],
                        'detail'                => json_encode($detail),
//                        'subtotal'              => $request->subtotal[$i]
                        'subtotal'              => $xiaoji
                    ]);
            }

            //保存收款总额
            $project_payment->update(['total' => $payment_total]);

            DB::commit();
            Flash::success('保存成功');
            return back();
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('保存失败');
        }
    }

    /*
     * 提交收款
     */
    public function paymentSubmit($id, SubmitPaymentRequest $request)
    {
//        dd($request->all());
        $currentUser = Auth::user();

        //事务开始
        DB::beginTransaction();

        try {
            $project_payment = ProjectPayment::where('project_id', $id)
                ->where('num', $request->max_num)
                ->first();

            if(!empty($project_payment)) {
                Payment::where('project_payment_id', $project_payment->id)->delete();
            }else {
                $project_payment = ProjectPayment::create([
                    'project_id'    => $id,
                    'num'           => $request->max_num,
                    'status'        => 0
                ]);
            }

            foreach ($request->subtotal as $key => $val1){
                if($val1 < 0){
                    $error[] = $key;
                }else{
                    $error = [];
                }
            }

            if(count($error) > 0){
                return back()->withErrors('小计不能小于0');
            }
//            $payment_total = 0;
            $shouru_zong = Logs::where('project_id',$id)
                ->whereIn('type',['income','assess_income','flat_pay'])
                ->sum('money');
            $zhichu_zong = Logs::where('project_id',$id)
                ->whereIn('type',['other_pay','assess_pay'])
                ->sum('money');
            $payment_total = $shouru_zong - $zhichu_zong;
            //全过程
            if($request->max_num > 1){
                $y = $request->biaozhi[0];
                $shouru_zong = Logs::where('project_id',$id)
                    ->where('payment_id','>=',$y)
                    ->whereIn('type',['income','assess_income','flat_pay'])
                    ->sum('money');
                $zhichu_zong = Logs::where('project_id',$id)
                    ->where('payment_id','>=',$y)
                    ->whereIn('type',['other_pay','assess_pay'])
                    ->sum('money');
                $payment_total = $shouru_zong - $zhichu_zong;
            }

            $count = count($request->type);
            for ($i = 0; $i < $count; $i++) {
//                if($payment_total != '' && $payment_total != 0){
//                if($request->subtotal[$i] != '' && $request->subtotal[$i] > 0) {
//                    $payment_total += $request->subtotal[$i];
//
//                    $detail = [
//                        "income"        => $request->income[$i],
//                        "assess_income" => $request->assess_income[$i],
//                        "assess_pay"    => $request->assess_pay[$i],
//                        "flat_pay"      => $request->flat_pay[$i],
//                        "other_pay"     => $request->other_pay[$i],
//                    ];
//
//                    Payment::create([
//                        'project_payment_id'    => $project_payment->id,
//                        'user_id'               => $currentUser->id,
//                        'type'                  => $request->type[$i],
//                        'detail'                => json_encode($detail),
//                        'subtotal'              => $request->subtotal[$i]
//                    ]);
                    if($request->max_num > 1){
                        $y = $request->biaozhi[0];
                        $income = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->where('type','income')
                            ->sum('money');

                        $assess_income = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->where('type','assess_income')
                            ->sum('money');

                        $flat_pay = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->where('type','flat_pay')
                            ->sum('money');

                        $other_pay = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->where('type','other_pay')
                            ->sum('money');

                        $assess_pay = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->where('type','assess_pay')
                            ->sum('money');
                        empty($income) ? $income = 0 : $income;
                        empty($assess_income) ? $assess_income = 0 : $assess_income;
                        empty($flat_pay) ? $flat_pay = 0 : $flat_pay;
                        empty($other_pay) ? $other_pay = 0 : $other_pay;
                        empty($assess_pay) ? $assess_pay = 0 : $assess_pay;
                        empty($shouru) ? $shouru = 0 : $shouru;
                        empty($zhichu) ? $zhichu = 0 : $zhichu;
                        $detail = [
                            "income"        => $income,
                            "assess_income" => $assess_income,
                            "assess_pay"    => $assess_pay,
                            "flat_pay"      => $flat_pay,
                            "other_pay"     => $other_pay,
                        ];

                        $shouru = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->whereIn('type',['income','assess_income','flat_pay'])
                            ->sum('money');

                        $zhichu = Logs::where('payment_id',$i+$y)
                            ->where('project_id',$id)
                            ->whereIn('type',['other_pay','assess_pay'])
                            ->sum('money');
                    }else
                        {
                        $income = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->where('type','income')
                            ->sum('money');

                        $assess_income = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->where('type','assess_income')
                            ->sum('money');

                        $flat_pay = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->where('type','flat_pay')
                            ->sum('money');

                        $other_pay = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->where('type','other_pay')
                            ->sum('money');

                        $assess_pay = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->where('type','assess_pay')
                            ->sum('money');

                        $shouru = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->whereIn('type',['income','assess_income','flat_pay'])
                            ->sum('money');

                        $zhichu = Logs::where('payment_id',$i+1)
                            ->where('project_id',$id)
                            ->whereIn('type',['other_pay','assess_pay'])
                            ->sum('money');

                        empty($income) ? $income = 0 : $income;
                        empty($assess_income) ? $assess_income = 0 : $assess_income;
                        empty($flat_pay) ? $flat_pay = 0 : $flat_pay;
                        empty($other_pay) ? $other_pay = 0 : $other_pay;
                        empty($assess_pay) ? $assess_pay = 0 : $assess_pay;
                        empty($shouru) ? $shouru = 0 : $shouru;
                        empty($zhichu) ? $zhichu = 0 : $zhichu;

                        $detail = [
                            "income"        => $income,
                            "assess_income" => $assess_income,
                            "assess_pay"    => $assess_pay,
                            "flat_pay"      => $flat_pay,
                            "other_pay"     => $other_pay,
                        ];

                    }
                $xiaoji = $shouru - $zhichu;
                if($xiaoji < 0 ){
                    return back()->withErrors('小计不能小于0');
                }

                Payment::create([
                    'project_payment_id'    => $project_payment->id,
                    'user_id'               => Auth::id(),
                    'type'                  => $request->type[$i],
                    'detail'                => json_encode($detail),
//                        'subtotal'              => $request->subtotal[$i]
                    'subtotal'              => $xiaoji
                ]);
                }
//                }
//            }

            //保存收款总额
            $project_payment->update([
                'total' => $payment_total,
                'status' => 1
            ]);

            //更新流转信息
            $projectFlow = ProjectFlow::where('project_id', $id)->where('num', $request->max_num)->first();
            $projectFlow->touch();

            //更新流转详情
            $remark = empty($request->remark) ? '完成' : $request->remark;
            Flow::where('project_flow_id', $projectFlow->id)
                ->where('result', 2)
                ->update([
                    'user_id'=> $currentUser->id,
                    'remark' => $remark
                ]);

            //更新项目信息
            Project::where('id', $id)->update(['state' => 2]);

            DB::commit();
            Flash::success('提交成功');
            return redirect()->route('project.detail.flow', $id);
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('提交失败');
        }
    }

    /*
     * 分配信息
     */
    public function distribution($id)
    {
        //项目信息
        $project = Project::where('id', $id)->run()->firstOrFail();

        //所有人员
        $members = User::members()->get();

        //分配信息
        $projectDistributions = ProjectDistribution::with(['distribution' => function($query){
            $query->with(['user' => function($query_s) {
                $query_s->withTrashed();
            }]);
        }])->where('project_id', $id)->get();

        //收款信息
        $projectPayment = ProjectPayment::where('project_id', $id)
            ->where('num', $project->max_num)->first();

        return view('projects.partials.distribution', compact('project', 'members', 'projectDistributions', 'projectPayment'));
    }

    /*
     * 更新分配信息
     */
    public function distributionUpdate($id, UpdateDistributionRequest $request)
    {
        if(!$request->performCheck($request->type)) {
            return back()->withErrors('项目负责人和项目审核人必须添加');
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
            $project_distribution = ProjectDistribution::where('project_id', $id)->where('num', $request->max_num)->first();
            if(!empty($project_distribution)) {
                Distribution::where('project_distribution_id', $project_distribution->id)->delete();
            }else {
                $project_distribution = ProjectDistribution::create([
                    'project_id'    => $id,
                    'num'           => $request->max_num,
                    'status'        => 0
                ]);
            }

            $dis_total = 0;
            $count = count($request->dis_position);
            for ($i = 0; $i < $count; $i++) {
                if(!empty($request->dis_position[$i]) && !empty($request->dis_member[$i])) {
                   if($request->dis_position[$i] != 7) {
                       $dis_total += $request->dis_bonus[$i];
                   }

                   Distribution::create([
                        'project_distribution_id'   => $project_distribution->id,
                        'position'                  => $request->dis_position[$i],
                        'user_id'                   => $request->dis_member[$i],
                        'remark'                    => $request->dis_remark[$i],
                        'bonus'                     => $request->dis_bonus[$i],
                        'formula'                   => $request->dis_formula[$i]
                   ]);
                }
            }

            //更新分配奖金总额
            $project_distribution->update(['total' => $dis_total]);

            //激活用户
            User::whereIn('id', $member_arr)->update(['is_banned' => 0]);

            DB::commit();
            Flash::success('保存成功');
            return back();
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('保存失败');
        }
    }

    /*
     * 提交分配
     */
    public function distributionSubmit($id, UpdateDistributionRequest $request)
    {
        if(!$request->performCheck($request->type)) {
            return back()->withErrors('项目负责人和项目审核人必须添加');
        }

        //本次分配奖金验证
        $projectPayment = ProjectPayment::where('project_id', $id)
            ->where('num', $request->max_num)->first();

        $tender_dis = 0;
        $consult_dis = 0;
        $count = count($request->dis_position);
        for ($i = 0; $i < $count; $i++) {
            if(in_array($request->dis_position[$i], [1,3,5,9])) {
                $tender_dis += $request->dis_bonus[$i];
            }

            if(in_array($request->dis_position[$i], [2,4,6,8,10,11,12,13])) {
                $consult_dis += $request->dis_bonus[$i];
            }
        }

        if($tender_dis > $projectPayment->tender_total * 0.16) {
            return back()->withErrors('招标奖金不能超过总额的16%');
        }
        if($consult_dis > $projectPayment->consult_total * 0.16) {
            return back()->withErrors('咨询奖金不能超过总额的16%');
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
            $project_distribution = ProjectDistribution::where('project_id', $id)->where('num', $request->max_num)->first();
            if(!empty($project_distribution)) {
                Distribution::where('project_distribution_id', $project_distribution->id)->delete();
            }else {
                $project_distribution = ProjectDistribution::create([
                    'project_id'    => $id,
                    'num'           => $request->max_num,
                    'status'        => 0
                ]);
            }

            $dis_total = 0;
            for ($i = 0; $i < $count; $i++) {
                if(!empty($request->dis_position[$i]) && !empty($request->dis_member[$i])) {
                    if($request->dis_position[$i] != 7) {
                        $dis_total += $request->dis_bonus[$i];
                    }

                    Distribution::create([
                        'project_distribution_id'   => $project_distribution->id,
                        'position'                  => $request->dis_position[$i],
                        'user_id'                   => $request->dis_member[$i],
                        'remark'                    => $request->dis_remark[$i],
                        'bonus'                     => $request->dis_bonus[$i],
                        'formula'                   => $request->dis_formula[$i]
                    ]);
                }
            }

            //更新分配奖金总额
            $project_distribution->update([
                'total' => $dis_total,
                'status'=> 1
            ]);

            //更新流转信息
            $projectFlow = ProjectFlow::where('project_id', $id)->where('num', $request->max_num)->first();
            $projectFlow->touch();

            //更新流转详情
            $remark = empty($request->remark) ? '完成' : $request->remark;
            Flow::where('project_flow_id', $projectFlow->id)
                ->where('result', 5)
                ->update([
                    'user_id'=> Auth::id(),
                    'remark' => $remark
                ]);

            //更新项目信息
            Project::where('id', $id)->update(['state' => 5]);

            //激活用户
            User::whereIn('id', $member_arr)->update(['is_banned' => 0]);

            DB::commit();
            Flash::success('提交成功');
            return redirect()->route('project.detail.flow', $id);
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('提交失败');
        }
    }

    /*
     * 总工填写经营人奖金
     */
    public function distributionOperate($id, Request $request)
    {
        //数据验证
        $validator = Validator::make($request->all(), [
            'max_num'   => 'required',
            'dis_bonus'  => 'required|array',
            'dis_bonus.*'=> 'required|numeric'
        ]);

        if($validator->fails()) {
            return back()->withErrors('提交数据有误');
        }

        $project = Project::findOrFail($id);

        //事务开始
        DB::beginTransaction();

        try {
            $projectDistribution = ProjectDistribution::where('project_id', $id)->where('num', $request->max_num)->first();
            $projectDistribution->touch();

            $count = count($request->dis_bonus);
            for ($i = 0; $i < $count; $i++) {
                Distribution::where('project_distribution_id', $projectDistribution->id)
                    ->where('position', 7)
                    ->where('user_id', $request->dis_member[$i])
                    ->update([
                        'bonus'     =>  $request->dis_bonus[$i],
                        'formula'   => ''
                    ]);
            }

            //更新流转信息
            $projectFlow = ProjectFlow::where('project_id', $id)->where('num', $request->max_num)->first();
            $projectFlow->touch();

            //归档验证
            $flow_filed = Flow::where('project_flow_id', $projectFlow->id)->filed()->first();
            if(!empty($flow_filed)) {
                if($flow_filed->confirm != 1) { //已归档但未确认
                    DB::rollback();
                    return back()->withErrors('请先确认归档');
                }
            }else {
                if(!$project->is_whole) {
                    DB::rollback();
                    return back()->withErrors('请等待归档员完成归档');
                }
            }

            //更新流转详情
            $remark = empty($request->remark) ? '完成' : $request->remark;
            Flow::where('project_flow_id', $projectFlow->id)
                ->where('result', 7)
                ->update([
                    'user_id'=> Auth::id(),
                    'remark' => $remark
                ]);

            //更新项目信息
            Project::where('id', $id)->update(['state' => 7]);

            DB::commit();
            Flash::success('提交成功');
            return redirect()->route('project.detail.flow', $id);
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('提交失败');
        }
    }

    /*
     * 流转信息
     */
    public function flow($id)
    {
        //项目信息
        $project = Project::where('id', $id)->run()->firstOrFail();

        //流转信息
        $projectFlows = ProjectFlow::with(['flow' => function($query){
                $query->with(['user' => function($query_s) {
                  $query_s->withTrashed();
                }])->orderBy('result', 'asc');
            }])->where('project_id', $id)->get();

        //是否含有经营人
        foreach ($projectFlows as $projectFlow) {
            $projectDistribution = ProjectDistribution::where('project_id', $id)
                ->where('num', $projectFlow->num)
                ->first();

            if($projectDistribution) {
                $projectFlow->has_operator = $projectDistribution->has_operator;
            }else {
                $projectFlow->has_operator = 0;
            }
        }

        return view('projects.partials.flow', compact('project', 'projectFlows'));
    }

    /*
     * 提交流转操作
     */
    public function flowSubmit($id, Request $request)
    {
        //数据验证
        $validator = Validator::make($request->all(), [
            'max_num'   => 'required',
            'user_id'   => 'required',
            'result'    => 'required|numeric'
        ]);

        if($validator->fails()) {
            return back()->withErrors('请先进行操作后再提交');
        }

        //事务开始
        DB::beginTransaction();

        if($request->result == 8 || $request->result == 7){
            $projectFlow = ProjectFlow::where('project_id', $id)
                ->where('num', $request->max_num)->first();
            $project = Project::findOrFail($id);
            $filed = Flow::where('project_flow_id', $projectFlow->id)->where('confirm', 1)->filed()->count();
            if(($filed == 0 && $project->type == 3) || ($filed == 0 && $project->type == 1)){
                return back()->withErrors('请确认归档后再提交');
            }
        }

        try {
            //更新流转信息
            $projectFlow = ProjectFlow::where('project_id', $id)
                ->where('num', $request->max_num)->first();

            Flow::where('project_flow_id', $projectFlow->id)
                ->where('result', $request->result)
                ->update([
                    'user_id'   => $request->user_id,
                    'remark'    => '完成'
                ]);


            //更新项目信息
            $project = Project::findOrFail($id);

            $project->state = $request->result;
            $project->save();

            //终审
            if($request->result == 8) {
                $project->update(['status' => 1]);
                $projectFlow->update(['status' => 1]);

                //确认过的归档
                $is_filed = Flow::where('project_flow_id', $projectFlow->id)->where('confirm', 1)->filed()->count();

                //全局并未归档
                if($project->is_whole && $is_filed == 0) {

                    $project->update(['state' => 1, 'status' => 0]);

                    //创建新的流转数据
                    $new_projectFlow = ProjectFlow::create([
                        'project_id' => $id,
                        'num' => $request->max_num + 1,
                        'status' => 0
                    ]);

                    Flow::create([
                        'project_flow_id' => $new_projectFlow->id,
                        'user_id' => $project->user_id,
                        'result' => 1,
                        'remark' => '完成'
                    ]);

                    for ($i = 2; $i <= 8; $i++) {
                        Flow::create([
                            'project_flow_id' => $new_projectFlow->id,
                            'result' => $i
                        ]);
                    }

                    //复制前一次的分配信息
                    $new_projectDistribution = ProjectDistribution::create([
                        'project_id' => $id,
                        'num' => $request->max_num + 1,
                        'status' => 0
                    ]);

                    $distributions = Distribution::whereHas('projectDistribution', function ($query) use ($id, $request) {
                        $query->where('project_id', $id)->where('num', $request->max_num);
                    })->get();

                    foreach ($distributions as $distribution) {
                        //不保存旧的奖金和公式
                        Distribution::create([
                            'project_distribution_id' => $new_projectDistribution->id,
                            'position' => $distribution->position,
                            'user_id' => $distribution->user_id,
                            'remark' => $distribution->remark
                        ]);
                    }
                }
            }

            DB::commit();
            Flash::success('提交成功');
            return redirect()->route('project.detail.flow', $id);
        }catch (Exception $exception) {
            DB::rollback();
            return back()->withErrors('提交失败');
        }
    }

    /*
     * 驳回流转至奖金分配
     */
    public function flowReset($id)
    {
        //事务开始
        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);
            $project->update(['state' => 4]);

            $projectFlow = ProjectFlow::where('project_id', $id)
                ->where('num', $project->max_num)->first();

            $projectFlow->touch();

            for ($i = 5; $i <= 8; $i++) {
                Flow::where('project_flow_id', $projectFlow->id)
                    ->where('result', $i)
                    ->update([
                        'user_id'   => null,
                        'remark'    => ''
                    ]);
            }

            ProjectDistribution::where('project_id', $id)
                ->where('num', $project->max_num)
                ->update(['status' => 0]);

            DB::commit();
            return response([
                'result' => 1,
                'msg' => '驳回成功'
            ]);
        }catch (Exception $exception) {
            DB::rollback();
            return response([
                'result' => 0,
                'msg' => '驳回失败'
            ]);
        }
    }

    /*
     * 修改流转备注
     */
    public function flowRemark($id, Request $request)
    {
        //事务开始
        DB::beginTransaction();

        try {
            $projectFlow = ProjectFlow::where('project_id', $id)
                ->where(function($query) {
                    $query->max('num');
                })->first();

            $projectFlow->touch();

            Flow::where('project_flow_id', $request->id)
                ->where('result', $request->result)
                ->update(['remark' => $request->remark]);

            DB::commit();
            return response([
                'result' => 1,
                'msg' => '修改成功',
                'info' => $projectFlow->updated_at->format('Y-m-d H:i:s')
            ]);
        }catch (Exception $exception) {
            DB::rollback();
            return response([
                'result' => 0,
                'msg' => '修改失败'
            ]);
        }
    }

    /*
     * 确认流转操作
     */
    public function flowConfirm($id, Request $request)
    {
        //事务开始
        DB::beginTransaction();

        try {
            $projectFlow = ProjectFlow::where('project_id', $id)
                ->where(function($query) {
                    $query->max('num');
                })->first();

            $projectFlow->touch();
             Flow::where('project_flow_id', $request->id)
                ->where('result', $request->result)
                ->update(['confirm' => 1]);
            DB::commit();
            return response([
                'result' => 1,
                'msg' => '确认成功',
                'info' => $projectFlow->updated_at->format('Y-m-d H:i:s')
            ]);
        }catch (Exception $exception) {
            DB::rollback();
            return response([
                'result' => 0,
                'msg' => '确认失败'
            ]);
        }
    }
}
