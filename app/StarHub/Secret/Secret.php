<?php
namespace StarHub\Secret;

use App\Models\Project;
use Auth;
use Carbon\Carbon;

class Secret {
    /**
     * 创建项目编号
     *
     * @param int $year 年份
     * @return string
     */
    public static function num($year) {
        if(empty($year) || !is_numeric($year) || strlen($year) != 4) {
            $year =  Carbon::now()->year;
        }

        $startYear = Carbon::create($year,1,1,0,0,0)->toDateTimeString();
        $endYear = Carbon::create($year,1,1,0,0,0)->addYears(1)->toDateTimeString();

        $currentUser = Auth::user();
        $project = Project::where('division', $currentUser->division)
            ->where('num','like', substr($startYear,0,4).'%')
            ->orderBy('num', 'desc')
            ->first();

        if(count($project)) {
            $num_prefix = substr($project->num, 0, 5);
            $num_suffix = substr($project->num, 5);
            $num_suffix = $num_suffix + 1;
            $num_suffix = str_pad($num_suffix, 3, '0',STR_PAD_LEFT);

            $num = $num_prefix.$num_suffix;
        }else {
            $division_num = config('admin.projects.division_num')[$currentUser->division];
            $num_suffix = intval($division_num);
            $num_suffix = str_pad($num_suffix, 3, '0',STR_PAD_LEFT);

            $num = $year.$currentUser->division.$num_suffix; //编号：年份+部门编号+增量+1
        }

        return $num;
    }

    /**
     * 获取项目类型
     *
     * @param array $category_arr 项目类别
     * @return string
     */
    public static function type($category_arr) {
        $tender_flag = false;
        $consult_flag = false;
        $tender_arr = [1,2,3,4,12];
        $consult_arr = [5,6,7,8,9,10,11,13];

        foreach ($category_arr as $category) {
            foreach ($tender_arr as $tender) {
                if($tender == $category) {
                    $tender_flag = true;
                }
            }

            foreach ($consult_arr as $consult) {
                if($consult == $category) {
                    $consult_flag = true;
                }
            }
        }

        if($tender_flag && !$consult_flag) {
            return 1;
        }else if(!$tender_flag && $consult_flag){
            return 2;
        }else {
            return 3;
        }
    }
}