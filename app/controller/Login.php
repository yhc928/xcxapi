<?php
declare (strict_types = 1);

namespace app\controller;

use think\facade\Session;
use think\Request;
use app\model\User as UserModel;

class Login extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
    /**
     * 登录
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function login(Request $request)
    {
       $data = $request->param();
       $data['password'] = md5($data['password']);
       $result =  UserModel::where('username',$data['username'])->where('password',$data['password'])->count();

       if ((int)$result)
       {
//           Session::set('token', $this->createToken());
           // 开始会话
           session_start();
           $_SESSION['token'] = $this->createToken();
           $data['token'] =  $_SESSION['token'];
           return $this->create($data,'登录成功!',200);

       }else{
           return $this->create($data,'用户名或密码不正确',201);
       }
    }

}
