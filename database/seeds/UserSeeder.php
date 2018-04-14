<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //系统管理员
        $new_user = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'division' => 0,
            'is_super' => 1,
            'is_banned' => 0,
            'password' => bcrypt('123456'),
            'remember_token' => str_random(10)
        ]);

        $role = Role::findOrFail(1);
        $new_user->attachRole($role);

        //普通用户
        $user_arr = array(
            '4' => array(
                [
                    'name'      => '韦天辰',
                    'role_id'   => 2,
                    'is_banned' => 0
                ]
            ),
            '5' => array(
                [
                    'name'      => '杜莉琴',
                    'role_id'   => 6,
                    'is_banned' => 0
                ]
            ),
            '6' => array(
                [
                    'name'      => '李萍',
                    'role_id'   => 8,
                    'is_banned' => 0
                ],
                [
                    'name'      => '蔡婷婷',
                    'role_id'   => 9,
                    'is_banned' => 0
                ]
            ),
            '7' => array(
                [
                    'name'      => '林建',
                    'role_id'   => 4,
                    'is_banned' => 0
                ],
                [
                    'name'      => '沈青瑶',
                    'role_id'   => 7,
                    'is_banned' => 1
                ]
            ),
            '1' => array(
                [
                    'name'      => '王昊',
                    'role_id'   => 3,
                    'is_banned' => 0
                ],
                [
                    'name'      => '黄少华',
                    'role_id'   => 7,
                    'is_banned' => 0
                ],
                [
                    'name'      => '林蓉',
                    'role_id'   => 7,
                    'is_banned' => 0
                ],
                [
                    'name'      => '武兴志',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '苏昆祺',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '徐佳乐',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '黄静燕',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '李胤',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '吴伟',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '时丽雯',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '张雨婷',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '杨帆',
                    'role_id'   => 7,
                    'is_banned' => 1
                ]
            ),
            '2' => array(
                [
                    'name'      => '周平',
                    'role_id'   => 3,
                    'is_banned' => 0
                ],
                [
                    'name'      => '郭强',
                    'role_id'   => 3,
                    'is_banned' => 0
                ],
                [
                    'name'      => '程惠卿',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '黄晓燕',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '席小静',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '杨丹凤',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '陈林',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '刘俐',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '刘玲',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '许萍',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '李瑞雪',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '何怡',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '李溪',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '王克桢',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '刘齐',
                    'role_id'   => 7,
                    'is_banned' => 1
                ]
            ),
            '3' => array(
                [
                    'name'      => '王峥',
                    'role_id'   => 3,
                    'is_banned' => 0
                ],
                [
                    'name'      => '王桂华',
                    'role_id'   => 3,
                    'is_banned' => 0
                ],
                [
                    'name'      => '刘惠华',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '金雪',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '杜剑滨',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '夜敏',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '王一顺',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '钱陈',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '孙永崇',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '李静怡',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '陈宇涛',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '代书莉',
                    'role_id'   => 7,
                    'is_banned' => 1
                ],
                [
                    'name'      => '钱卫新',
                    'role_id'   => 7,
                    'is_banned' => 1
                ]
            )
        );

        foreach ($user_arr as $key => $users) {
            $division = $key;

            foreach ($users as $user) {
                $new_user = new User();
                $new_user->name = $user['name'];
                $new_user->division = $division;
                $new_user->password = bcrypt('123456'); //默认密码
                $new_user->remember_token = str_random(10);
                $new_user->is_banned = $user['is_banned'];
                $new_user->save();

                $role = Role::findOrFail($user['role_id']);
                $new_user->attachRole($role);
            }
        }

    }
}