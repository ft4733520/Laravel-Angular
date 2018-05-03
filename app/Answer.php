<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function add()
    {
    	if (!user_ins()->is_logged_in()) {
    		return ['status'=>0, 'msg'=>'请先登录'];
    	}

    	if (!rq('content') || !rq('question_id')) {
    		return ['status'=>0, 'msg'=>'require content or question_id']; 
    	}

    	$question = question_ins()->find(rq('question_id'));
    	if(!$question) return ['status'=>0, 'msg'=>'question_id not exists'];

    	//是否已经回答过了
    	$answered = $this->where(['question_id'=>rq('question_id'),'user_id'=>session('user_id')])
    					 ->count();
    	if ($answered) {
    		return ['status'=>0, 'msg'=>'have answered'];
    	}


    	$this->content 	= rq('content');
    	$this->question_id 	= rq('question_id');
    	$this->user_id = session('user_id');


    	return $this->save() ? ['status'=>1,'id'=>$this->id] : ['status'=>0, 'msg'=>'db inset failed'];
    }

    public function change()
    {
    	if (!user_ins()->is_logged_in()) 
    		return ['status'=>0, 'msg'=>'请先登录'];
    	

    	if (!rq('id')) 
    		return ['status'=>0, 'msg'=>'require id']; 
    	
    	$answer = $this->find(rq('id'));
    	if($answer->user_id!=session('user_id'))
    		return ['status'=> 0, 'msg'=> 'permission denied'];

    	$answer->content = rq('content');
    	$answer->updated_at = time();

    	return $answer->save() ? ['status'=>1] : ['status'=>0, 'msg'=>'db update failed'];

    }

    public function read()
    {
    	if(!rq('id') && !rq('question_id'))
    		return ['status'=>0, 'msg'=> 'require id or question_id'];

    	if(rq('id'))
    	{
    		$answer = $this->find(rq('id'));
    		if (!$answer) {
    			$answer = 'answer not exists';
    		}
    		return ['status'=>1, 'data'=> $answer];
    		
    	}

    	if(!question_ins()->find(rq('question_id')))
    		return ['status'=>0, 'msg'=>'question is not exists'];
    	$answeres = $this->where('question_id',rq('question_id'))
    				->get()
    				->keyBy('id');

    	return ['status'=>1, 'data'=>$answeres];
    }

    public function vote()
    {
        if (!user_ins()->is_logged_in()) 
            return ['status'=>0, 'msg'=>'请先登录'];
        
        if(!rq('id') || !rq('vote'))
            return ['status'=>0, 'msg'=>'id and vote are requited'];

        $answer = $this->find(rq('id'));
        if(!$answer) return ['status'=>0, 'msg'=>'answer not exists'];

        //1赞同 2反对
        $vote = rq('vote') <= 1 ? 1 : 2;

        //检查此用户在相同的问题下投过票 投过就删除
        $answer->users()
               ->newPivotStatement()
               ->where('user_id',session('user_id'))
               ->where('answer_id', rq('id'))
               ->delete();

        //再增加数据
        $answer->users()
               ->attach(session('user_id'),['vote'=>$vote]);

        return ['status'=>1];

    }

    public function users()
    {
        return $this->belongsToMany('App\User')
                    ->withPivot('vote')
                    ->withtimestamps();
    }


}
