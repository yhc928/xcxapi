<?php

namespace app\controller;
use think\facade\Filesystem;
use think\Request;
use think\facade\Session;
use app\model\Upload as UploadModel;
use think\Response;

class UploadImg extends AdminBase
{
    public function upload(Request $request){
        $files = $request->file();
      // $file = Request::file('image');
        $info = [];
        //dump($files);
        foreach ($files as $file){

            $info[] = Filesystem::putfile('topic',$file);
        }

       // dump($info);

        if (count($info) != 0){
            $currentUrl = \think\facade\Request::instance()->domain();
            $data = ['imageUrl' => '/storage/'.$info[0]];
            $id = UploadModel::create($data)->getData('id');
            if (!empty($id)){

                $result = [
                    //状态码
                    'errno' => 0,
                    //返回数据
                    'data' =>['url'=> $currentUrl.'/storage/'.$info[0],'alt'=>'','href'=>'']
                ];
                //返回api接口
                return Response::create($result,'json');


            }else{
                $result = [
                    //状态码
                    'errno' => 1,
                    //返回数据
                    'message' => '失败了'
                ];
                //返回api接口
                return Response::create($result,'json');
            }
        }else{
            $result = [
                //状态码
                'errno' => 1,
                //返回数据
                'message' => '没有获取到图片'
            ];
            //返回api接口
            return Response::create($result,'json');
        }

    }
}