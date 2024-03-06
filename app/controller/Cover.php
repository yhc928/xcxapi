<?php

namespace app\controller;
use think\facade\Filesystem;
use think\Request;
use think\facade\Session;
use app\model\Upload as UploadModel;
use think\Response;

class Cover extends AdminBase
{
    public function upload(Request $request){
        $files = $request->file();
      // $file = Request::file('image');
        $info = [];
        //dump($files);

        foreach ($files as $file){

            $info[] = Filesystem::putfile('topic',$file);
        }
        //echo app()->getRootPath() . 'public/storage/'. $info[0];

        $ENVID = 'prod-4g7ozm3t0ab77771';
        $position = strpos($info[0], "\\");
        $fileName = substr($info[0],$position+1,strlen($info[0]));
        echo "fileName 文件名：".$fileName;
        $PATH = 'cover/'.$fileName;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.weixin.qq.com/tcb/uploadfile',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"env":"'.$ENVID.'","path":"'.$PATH.'"}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response,true);
//        $res -> key = $PATH;
//        echo json_encode($res);
        echo "获取上传文件地址：".json_encode($res);
        if ($res['errcode'] == 0){
            //上传文件到云托管

            $key = $PATH;
            $Signature = $res['authorization'];
            $security_token = $res['token'];
            $meta_fileid =$res['cos_file_id'];
            $file = app()->getRootPath().'public/storage/'.$info[0];

            echo "本地上传文件路径:".$file;

            $ucurl = curl_init();
            curl_setopt_array($ucurl, array(
                CURLOPT_URL => $res['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"Content-Type":"image/jpeg","key":"'.$key.'","Signature":"'.$Signature.'","x-cos-security-token":"'.$security_token.'","x-cos-meta-fileid":"'.$meta_fileid.'","file":"'.$fileName,$file.'"}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response1 = curl_exec($ucurl);
            curl_close($ucurl);
            echo $response1;
//            return json_encode($res1);
        }




//        if (count($info) != 0){
//            $currentUrl = \think\facade\Request::instance()->domain();
//            $data = ['imageUrl' => '/storage/'.$info[0]];
//            $id = UploadModel::create($data)->getData('id');
//            if (!empty($id)){
//
//                $result = [
//                    //状态码
//                    'errno' => 0,
//                    //返回数据
//                   'url'=> '/storage/'.$info[0]
//                ];
//                //返回api接口
//                return Response::create($result,'json');
//
//
//            }else{
//                $result = [
//                    //状态码
//                    'errno' => 1,
//                    //返回数据
//                    'message' => '失败了'
//                ];
//                //返回api接口
//                return Response::create($result,'json');
//            }
//        }else{
//            $result = [
//                //状态码
//                'errno' => 1,
//                //返回数据
//                'message' => '没有获取到图片'
//            ];
//            //返回api接口
//            return Response::create($result,'json');
//        }

    }

    public function  getaccess_token(){

        $errno = 1;
        $errmsg = '';
        if (Session::has('tokenTime')){
            $current_time = date('Y-m-d H:i:s', time());
            $timestamp1 = strtotime($current_time);
            $timestamp2 = strtotime(Session::get('tokenTime'));
            $minutesDiff = abs(round((($timestamp2 - $timestamp1) / 60)));
            if ($minutesDiff > 100){
                //大于100分钟重新获取
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxf4f70898440144ec&secret=c3814851eaf904b16bfbc568f87f76aa"; // 要访问的URL地址
                $result = json_decode(file_get_contents($url),true); // 发送GET请求并获取返回结果

                if (!empty($result['access_token'])){
                    Session::set('access_token',$result['access_token']);
                    Session::set('tokenTime',date('Y-m-d H:i:s'));
                    $errno = 0;
                }else{
                    $errno = $result['errcode'];
                    $errmsg =  $result['errmsg'];
                }
            }else{
                $errno = 0;
            }
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxf4f70898440144ec&secret=c3814851eaf904b16bfbc568f87f76aa"; // 要访问的URL地址
            $result = json_decode(file_get_contents($url),true); // 发送GET请求并获取返回结果

            if (!empty($result['access_token'])){
                Session::set('access_token',$result['access_token']);
                Session::set('tokenTime',date('Y-m-d H:i:s'));
                $errno = 0;
            }else{
                $errno = $result['errcode'];
                $errmsg =  $result['errmsg'];
            }
        }

        $resultdata = [
            //状态码
            'errno' => $errno,
            '$errmsg' => $errmsg,
            //返回数据
            'access_token' => Session::get('access_token')
        ];
        //返回api接口
        return Response::create($resultdata,'json');

    }
}