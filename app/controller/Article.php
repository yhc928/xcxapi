<?php
declare (strict_types = 1);

namespace app\controller;

use think\Request;
use app\model\Article as ArticleModel;
class Article extends AdminBase
{
    /**hph
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $data = $request->param();

        $result = ArticleModel::order('lasttime','desc')->paginate($this->pageSize);
        //获取当前网址
        // $currentUrl = \think\facade\Request::instance()->domain();
        //组装封面图片的url
//        $result['testurl'] = $currentUrl.$result['testurl'];
        if ($result->isEmpty()){
            return $this->create($result,'暂无数据');
        }else{
            return $this->create($result,'数据请求成功');
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //获取数据
        $data = $request->param();
        if (Empty($data)){
            return $this->create($data,'数据不能为空');
        }else{
            unset($data['id']);
            $data['lasttime'] = date('Y-m-d H:i:s');
            //写入数据库
            $id = ArticleModel::create($data)->getData('id');
            if (empty($id)){
                return $this->create($data,'暂无数据');
            }else{
                return $this->create($data,'数据保存成功');
            }
        }
    }
    /**
     * 显示指定的类型资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function readList(Request $request)
    {
        $data = $request->param();

        $result = ArticleModel::where('typeid',$data['typeid'])->order('lasttime','desc')->paginate($this->pageSize);
        if ($result->isEmpty()){
            return $this->create($result,'暂无数据');
        }else{
            return $this->create($result,'数据请求成功');
        }
    }
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function readDetail($id)
    {
        $result = ArticleModel::find($id);
        if ($result->isEmpty()){
            return $this->create($result,'暂无数据');
        }else{
            return $this->create($result,'数据请求成功');
        }
    }
    /**
     * 显示指定的类型资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function readtuijian($id)
    {
        $result = ArticleModel::where('istuijian',1)->where('id','<>',$id)->order('lasttime','desc')->select();
        if ($result->isEmpty()){
            return $this->create($result,'暂无数据');
        }else{
            return $this->create($result,'数据请求成功');
        }
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
        $data = $request->param();
        //如果有图片，则重新组装url
        if (Empty($data)){
            return $this->create($data,'数据不能为空');

        }else{
            $data['lasttime'] = date('Y-m-d H:i:s');
            $id = ArticleModel::update($data)->getData('id');
            if (empty($id)){
                return $this->create([],'修改失败!',201);
            }else{
                return $this->create($id,'修改成功!');
            }
        }


    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        try {
            ArticleModel::find($id)->delete();
            return $this->create([],'删除成功');
        }catch (\Error $e){
            return  $this->create([],'错误或无法删除~',201);
        }
    }


}
