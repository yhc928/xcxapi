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

        $ENVID = 'prod-4g7ozm3t0ab77771';
        $position = strpos($info[0], "20240306");
        $fileName = substr($info[0],$position+9,strlen($info[0]));
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

        if ($res['errcode'] == 0){
            //上传文件到云托管

            $key = $PATH;
            $Signature = $res['authorization'];
            $security_token = $res['token'];
            $meta_fileid =$res['cos_file_id'];
            $file = file_get_contents(app()->getRootPath().'/public/storage/'.$info[0]);

            $ucurl = curl_init();
            $postData = array(
                'key' => $key,
                'Signature'=> $Signature,
                'x-cos-security-token' =>$security_token,
                'x-cos-meta-fileid' =>$meta_fileid,
                'file' => $file // 上传文件路径
            );
            curl_setopt($ucurl, CURLOPT_URL, $res['url']); // 设置URL地址
            curl_setopt($ucurl, CURLOPT_RETURNTRANSFER, true); // 返回结果而不直接输出到页面
            curl_setopt($ucurl, CURLOPT_POST, true); // 开启POST请求
            // 设置POST请求体
            curl_setopt($ucurl, CURLOPT_POSTFIELDS, $postData);

            $response1 = curl_exec($ucurl);
            curl_close($ucurl);

        }

        //获取下载链接
        $param = array(
            'env' => $ENVID,
            'file_list' => array(array(
                'fileid' => $res['file_id'],
                'max_age' => 86400
            ))
        );

        $curl1 = curl_init();
        curl_setopt_array($curl1, array(
            CURLOPT_URL => 'http://api.weixin.qq.com/tcb/batchdownloadfile',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($param),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl1);
        curl_close($curl1);
        return json_decode($response);


//        $url = "https://thinkphp-nginx-qrer-95012-8-1324748859.sh.run.tcloudbase.com?cloudid=".$res['file_id']; // 要访问的URL地址
//        $result = json_decode(file_get_contents($url),true); // 发送GET请求并获取返回结果
//        if ($result['errcode'] ==0){
//            //获取到下载链接
//            $data = [
//                //状态码
//                  'errno' => $result['errcode'],
//                  //返回数据
//                  'errmsg' =>$result['file_list'][0]['errmsg'],
//                  'url'=> $result['file_list'][0]['download_url']
//            ];
//            return Response::create($data,'json');
//        }else{
//            $data = [
//                //状态码
//                'errno' => $result['errcode'],
//                'errmsg' => '图片上传失败！',
//                //返回数据
//                'url'=> ''
//            ];
//            return Response::create($data,'json');
//        }

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