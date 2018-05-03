<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    public function add()
    {
    	if (!user_ins()->is_logged_in()) 
    		return ['status'=>0, 'msg'=>'请先登录'];
    	
    	if (!rq('content') ) {
    		return ['status'=>0, 'msg'=>'require content']; 
    	}

    	if (
    		(!rq('question_id') && !rq('answer_id')) || 
    		(rq('question_id') && rq('answer_id'))
    	) 
    		return ['status'=>0, 'msg'=>'require question_id or answer_id']; 
		
		if(rq('question_id'))    	
		{
			$question = question_ins()->find(rq('question_id'));
			if(!$question) return ['status'=>0, 'msg'=>'question is not exists'];
			$this->question_id = rq('question_id');
		}else{
			$answer = answer_ins()->find(rq('answer_id'));
			if(!$answer) return ['status'=>0, 'msg'=>'answer is not exists'];
			$this->answer_id = rq('answer_id');
		}

		if(rq('reply_to'))
		{
			$target = $this->find(rq('reply_to'));
			if(!$target) return ['status'=>0, 'msg'=>'target comment is not exists'];
			if($target->user_id==session('user_id'))
				return ['status'=>0, 'msg'=>'invalid reply_to'];
			$this->reply_to = rq('reply_to');
		}

		$this->content = rq('content');
		$this->user_id = session('user_id');

		return $this->save() ? ['status'=>1,'id'=>$this->id] : ['status'=>0, 'msg'=>'db insert failed'];
    }


   	public function read()
    {
    	if(!rq('question_id') && !rq('answer_id'))
    		return ['status'=>0, 'msg'=>'require question_id or answer_id']; 

    	if(rq('question_id')){
    		$question = question_ins()->find(rq('question_id'));
    		if(!$question) return ['status'=>0, 'question not exists'];
    		$data = $this->where('question_id', rq('question_id'));

    	}else{
    		$answer = answer_ins()->find(rq('answer_id'));
    		if(!$answer) return ['status'=>0, 'answer not exists'];
    		$data = $this->where('answer_id', rq('answer_id'));
    	}
    	$data = $data->get()->keyBy('id');
    	return ['status'=>1, 'data'=>$data];

    }

    public function remove()
    {
        if (!user_ins()->is_logged_in()) 
            return ['status'=>0, 'msg'=>'请先登录'];

        if(!rq('id'))
            return ['status'=>0, 'msg'=>'id is required'];

        $comment = $this->find(rq('id'));
        if(!$comment) return ['status'=>0, 'msg'=>'comment not exists'];

        if($comment->user_id != session('user_id'))
            return ['status'=>0, 'msg'=>'permission denied'];

        $this->where('reply_to', rq('id'))->delete();

        return $comment->delete() ? ['status'=>1] : ['status'=>0, 'msg'=>'db delete failed'];

    }



}
