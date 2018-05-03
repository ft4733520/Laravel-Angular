<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function add()
    {
    	if (!user_ins()->is_logged_in()) {
    		return ['status'=>0, 'msg'=>'请先登录'];
    	}

    	if (!rq('title')) {
    		return ['status'=>0, 'msg'=>'require title']; 
    	}

    	$this->title 	= rq('title');
    	$this->user_id 	= session('user_id');
    	if(rq('desc'))
    		$this->desc = rq('desc');

    	return $this->save() ? ['status'=>1,'id'=>$this->id] : ['status'=>0, 'msg'=>'db inset failed'];
    }

    public function change()
    {
    	if (!user_ins()->is_logged_in()) {
    		return ['status'=>0, 'msg'=>'请先登录'];
    	}

    	if(!rq('id'))
    		return ['status'=>0, 'msg'=>'问题id'];

    	$question = $this->find(rq('id'));
    	if(!$question)
    		return ['status'=>0, 'msg'=>'question is not exists'];

    	if($question->user_id!=session('user_id'))
    		return ['status'=>0, 'msg'=>'permission denied'];
    	

    	if(rq('title'))
    		$question->title = rq('title');

    	if(rq('desc'))
    		$question->desc = rq('desc');

    	return $question->save() ? ['status'=>1] : ['status'=>0, 'msg'=>'db update failed'];
    }

    public function read()
    {
    	if(rq('id'))
    		return ['status'=>1, 'data'=> $this->find(rq('id'))];

    	//默认显示条数
        list($limit,$skip) = paginate(rq('page'), rq('limit'));


    	$list = $this->orderBy('created_at')
    				->limit($limit)
    				->skip($skip)
    				->get(['id','title','desc','created_at','updated_at'])
    				->keyBy('id');

    	return ['status'=>1, 'data'=>$list];

    }

    public function remove()
    {
    	if (!user_ins()->is_logged_in()) {
    		return ['status'=>0, 'msg'=>'请先登录'];
    	}

    	if(!rq('id'))
    		return ['status'=>0, 'msg'=>'id is required'];

    	$question = $this->find(rq('id'));
    	if(!$question)
    		return ['status'=>0, 'msg'=>'question is not exists'];
    	if($question->user_id!=session('user_id'))
    		return ['status'=>0, 'msg'=>'permission denied'];

    	return $question->delete() ? ['status'=>1] : ['status'=>0, 'msg'=>'db delete error'];


    }


}
