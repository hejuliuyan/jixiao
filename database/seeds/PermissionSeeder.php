<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //所有权限
        $permissions = array(
            [
                'name' => 'project.manager',
                'display_name' => '项目管理',
                'description' => '项目管理模块',
                'pid' => 0
            ],
            [
                'name' => 'system.manager',
                'display_name' => '系统管理',
                'description' => '系统管理模块',
                'pid' => 0
            ],
            [
                'name' => 'project',
                'display_name' => '项目列表',
                'description' => '显示项目列表',
                'pid' => 1
            ],
            [
                'name' => 'project.search',
                'display_name' => '项目检索',
                'description' => '检索项目',
                'pid' => 3
            ],
            [
                'name' => 'project.export',
                'display_name' => '导出年审表',
                'description' => '导出已终年审表',
                'pid' => 3
            ],
            [
                'name' => 'project.create',
                'display_name' => '新增项目',
                'description' => '新增项目',
                'pid' => 3
            ],
            [
                'name' => 'project.update',
                'display_name' => '编辑项目',
                'description' => '编辑项目',
                'pid' => 3
            ],
            [
                'name' => 'project.order',
                'display_name' => '发起项目',
                'description' => '发起项目并开始流转',
                'pid' => 3
            ],
            [
                'name' => 'project.detail',
                'display_name' => '项目详情',
                'description' => '查看项目详情',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.charge',
                'display_name' => '收款记账',
                'description' => '收取项目款项',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.file',
                'display_name' => '归档',
                'description' => '归档项目文件',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.finish',
                'display_name' => '申请结算',
                'description' => '申请奖金结算',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.bonus',
                'display_name' => '奖金分配',
                'description' => '分配项目组人员奖金',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.confirm',
                'display_name' => '核算确认',
                'description' => '签发验收',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.examine',
                'display_name' => '审查',
                'description' => '审查项目并分配经营人奖金',
                'pid' => 3
            ],
            [
                'name' => 'project.flow.final',
                'display_name' => '终审',
                'description' => '最后审核项目',
                'pid' => 3
            ],
            [
                'name' => 'user',
                'display_name' => '用户列表',
                'description' => '显示用户列表',
                'pid' => 2
            ],
            [
                'name' => 'user.search',
                'display_name' => '用户检索',
                'description' => '通过部门和姓名检索用户',
                'pid' => 17
            ],
            [
                'name' => 'user.add',
                'display_name' => '新增用户',
                'description' => '新增用户',
                'pid' => 17
            ],
            [
                'name' => 'user.edit',
                'display_name' => '编辑用户',
                'description' => '编辑用户',
                'pid' => 17
            ],
            [
                'name' => 'user.del',
                'display_name' => '删除用户',
                'description' => '删除用户',
                'pid' => 17
            ],
            [
                'name' => 'role',
                'display_name' => '角色列表',
                'description' => '显示角色列表',
                'pid' => 2
            ],
            [
                'name' => 'role.add',
                'display_name' => '新增角色',
                'description' => '新增角色',
                'pid' => 22
            ],
            [
                'name' => 'role.edit',
                'display_name' => '编辑角色',
                'description' => '编辑角色',
                'pid' => 22
            ],
            [
                'name' => 'role.del',
                'display_name' => '删除角色',
                'description' => '删除角色',
                'pid' => 22
            ]
        );

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
