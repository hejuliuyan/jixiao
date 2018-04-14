<?php
Route::get('/', 'IndexController@entry')->name('entry');
Route::get('/index', 'IndexController@index')->name('index');

/* 登录验证 */
Route::get('/login', 'Auth\AuthController@getLogin')->name('login'); //登录页面
Route::get('/change_data', 'ProjectController@change_data');
Route::post('/auth/login', 'Auth\AuthController@postLogin')->name('auth.login'); //登录验证
Route::get('/login_out', 'Auth\AuthController@logout')->name('logout'); //退出

/* 用户 */
Route::get('/user', 'UserController@index')->name('user'); //用户列表
Route::post('/user/search', 'UserController@search')->name('user.search'); //检索用户
Route::get('/user/add', 'UserController@add')->name('user.add'); //添加用户
Route::post('/user/create', 'UserController@create')->name('user.create'); //新增用户
Route::get('/user/{id}/edit', 'UserController@edit')->name('user.edit'); //编辑用户
Route::post('/user/{id}/update', 'UserController@update')->name('user.update'); //更新用户
Route::post('/user/{id}/delete', 'UserController@delete')->name('user.del'); //删除用户
Route::get('/user/edit_password', 'UserController@editPassword')->name('user.edit_password'); //修改密码
Route::post('/user/update_password', 'UserController@updatePassword')->name('user.update_password'); //更新密码

/* 角色 */
Route::get('/role', 'RoleController@index')->name('role'); //角色列表
Route::get('/role/add', 'RoleController@add')->name('role.add'); //添加角色
Route::post('/role/create', 'RoleController@create')->name('role.create'); //新增角色
Route::get('/role/{id}/edit', 'RoleController@edit')->name('role.edit'); //编辑角色
Route::post('/role/{id}/update', 'RoleController@update')->name('role.update'); //更新角色
Route::post('/role/{id}/delete', 'RoleController@delete')->name('role.del'); //删除角色

/* 项目 */
Route::get('/project_flow', 'ProjectController@index_flow')->name('project.index_flow');
Route::get('/project_division', 'ProjectController@index_division')->name('project.index_division');
Route::post('/project/search', 'ProjectController@search')->name('project.search'); //检索项目
Route::get('/project/export', 'ProjectController@export')->name('project.export'); //导出已终年审表
Route::get('/project/add', 'ProjectController@add')->name('project.add'); //添加项目
Route::post('/project/create', 'ProjectController@create')->name('project.create'); //新增项目
Route::get('/project/{id}/edit', 'ProjectController@edit')->name('project.edit'); //编辑项目
Route::post('/project_delete', 'ProjectController@delete')->name('project.del'); //删除项目
Route::post('/project/{id}/update', 'ProjectController@update')->name('project.update'); //更新项目
Route::post('/project/order', 'ProjectController@order')->name('project.order'); //下单项目

Route::get('/logs', 'ProjectController@get_money')->name('get_money_logs'); //编辑收入的费用页面
Route::post('/post_money', 'ProjectController@post_money')->name('post_money_logs'); //编辑收入的费用页面

/* 项目详情（基本信息） */
Route::get('/project/{id}/detail/base', 'DetailController@base')->name('project.detail.base'); //基本信息
Route::post('/project/{id}/detail/base_update', 'DetailController@baseUpdate')->name('project.detail.base_update'); //更新基本信息
Route::post('/project/{id}/detail/base_file', 'DetailController@baseFile')->name('project.detail.base_file'); //归档员填写合同编号

/* 项目详情（收款信息） */
Route::get('/project/{id}/detail/payment', 'DetailController@payment')->name('project.detail.payment'); //收款信息
Route::post('/project/{id}/detail/payment_update', 'DetailController@paymentUpdate')->name('project.detail.payment_update'); //更新收款信息
Route::post('/project/{id}/detail/payment_submit', 'DetailController@paymentSubmit')->name('project.detail.payment_submit'); //提交收款

/* 项目详情（分配信息） */
Route::get('/project/{id}/detail/distribution', 'DetailController@distribution')->name('project.detail.distribution'); //分配人员信息
Route::post('/project/{id}/detail/distribution_update', 'DetailController@distributionUpdate')->name('project.detail.distribution_update'); //更新分配信息
Route::post('/project/{id}/detail/distribution_submit', 'DetailController@distributionSubmit')->name('project.detail.distribution_submit'); //提交分配
Route::post('/project/{id}/detail/distribution_operate', 'DetailController@distributionOperate')->name('project.detail.distribution_operate'); //总工填写经营人奖金

/* 项目详情（流转信息） */
Route::get('/project/{id}/detail/flow', 'DetailController@flow')->name('project.detail.flow'); //流转信息
Route::post('/project/{id}/detail/flow_submit', 'DetailController@flowSubmit')->name('project.detail.flow_submit'); //提交流转操作
Route::post('/project/{id}/detail/flow_reset', 'DetailController@flowReset')->name('project.detail.flow_reset'); //驳回流转至奖金分配
Route::post('/project/{id}/detail/flow_remark', 'DetailController@flowRemark')->name('project.detail.flow_remark'); //修改流转备注
Route::post('/project/{id}/detail/flow_confirm', 'DetailController@flowConfirm')->name('project.detail.flow_confirm'); //确认流转操作
