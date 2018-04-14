<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //所有角色
        $roles = array(
            [
                'name' => 'admin',
                'display_name' => '系统管理员',
                'description' => '系统',
                'permissions' => [2,17,18,19,20,21,22,23,24,25]
            ],
            [
                'name' => 'general_manager',
                'display_name' => '总经理',
                'description' => '终审',
                'permissions' => [1,3,4,5,9,16]
            ],
            [
                'name' => 'division_manager',
                'display_name' => '部门经理',
                'description' => '发起',
                'permissions' => [1,3,4,5,6,7,8,9,13]
            ],
            [
                'name' => 'personnel_manager',
                'display_name' => '部门经理（人事行政部）',
                'description' => '查阅',
                'permissions' => [1,3,4,5]
            ],
            [
                'name' => 'project_leader',
                'display_name' => '项目负责人',
                'description' => '核算',
                'permissions' => [1,3,4,9,12,14]
            ],
            [
                'name' => 'chief_engineer',
                'display_name' => '总工',
                'description' => '验收',
                'permissions' => [1,3,4,5,9,15]
            ],
            [
                'name' => 'engineer',
                'display_name' => '未分工',
                'description' => '开发',
                'permissions' => [1,3,4,9]
            ],
            [
                'name' => 'finance',
                'display_name' => '财务',
                'description' => '收款和记账',
                'permissions' => [1,3,4,5,9,10]
            ],
            [
                'name' => 'file',
                'display_name' => '归档员',
                'description' => '归档',
                'permissions' => [1,3,4,9,11]
            ]
        );

        foreach ($roles as $role) {
            $role_data = [
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'description' => $role['description'],
            ];
            $new_role = Role::create($role_data);

            $perms = Permission::whereIn('id', $role['permissions'])->get();
            $new_role->attachPermissions($perms);
        }
    }
}
