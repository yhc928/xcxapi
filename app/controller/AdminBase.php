<?php

namespace app\controller;

use think\facade\Request;
use think\Response;
abstract class AdminBase
{
    protected $page;
    protected $pageSize;

    public function __construct(){
        //获取分页
        $this->page = (int)Request::param('page');
        //获取每页条数
        $this->pageSize = (int)Request::param('pageSize',3);
    }
 protected function create($data,$msg ='',$code = 200,$type = 'json')
 {
     //api结构
     $result = [
         //状态码
         'code' => $code,
         //消息
         'msg' => $msg,
         //返回数据
         'data' =>$data
     ];
     //返回api接口
     return Response::create($result,$type);
 }
    /**
     * 生成 Token
     * @return string
     */
    protected  function createToken()
    {
        return md5(uniqid(md5(microtime(true)), true));
    }
    /**
     * 生成 存储图片
     * @return string
     */
    protected function avatar($image){
        $decode_img = base64_decode(str_replace('data:image/jpeg;base64', '', $image));

        $rootPath =  'uploads/images' . DIRECTORY_SEPARATOR;
        $subPath = date('Ymd') . "/";
        $savePath = $rootPath . $subPath;

        // 如果目录不存在，则创建目录
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }

        $filename = uniqid() . '.jpeg'; // 生成唯一文件名
        file_put_contents($savePath.$filename, $decode_img);

        $avatar_path = '/uploads/images/'.$subPath.$filename;
        return $avatar_path;

    }

}