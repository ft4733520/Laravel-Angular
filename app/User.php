<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;

class User extends Model
{
	//注册
    public function signup()
    {
    	// dd(Request::get('password'));
    	// dd(Request::has(''));
    	// dd(Request::all());
    	$has_username_and_password = $this->has_username_and_password();
    	if(!$has_username_and_password)
    		return err('用户名和密码皆不可为空');
    	$username = $has_username_and_password[0];
    	$password = $has_username_and_password[1];
    	//检查是否存在
    	$user_exists = $this->where('username',$username)
    						->exists();
    	if ($user_exists)
    		return err('用户名已存在');
    	
    	//加密
    	$hashed_password = Hash::make($password);
    	// $hashed_password = bcrypt($password);

    	//入库
    	$user = $this;
    	$user->password = $hashed_password;
    	$user->username = $username;
    	if ($user->save()) {
    		return suc(['id'=>$user->id]);
    	}else{
    		return err('db insert failed');
    	}
    	// dd($hashed_password);
    	return 1;
    }

    //获取用户信息API
    public function read()
    {
        if(!rq('id'))
            return err('required id');

        $get = ['id','username','avatar_url','intro'];
        $user = $this->find(rq('id'),$get);
        $data = $user->toArray();
                //$this->get($get);
        //$answer_count   = $user->answers()->count();
        //$question_count = $user->questions()->count();
        $answer_count   = answer_ins()->where('user_id', rq('id'))->count();
        $question_count = question_ins()->where('user_id', rq('id'))->count();
        $data['answer_count']   = $answer_count;
        $data['question_count'] = $question_count;

        return suc($data);

    }

    //登录
    public function login()
    {
    	$has_username_and_password = $this->has_username_and_password();
    	if(!$has_username_and_password)
    		return ['status'=>0, 'msg'=>'用户名和密码皆不可为空'];
    	$username = $has_username_and_password[0];
    	$password = $has_username_and_password[1];

    	$user = $this->where('username', $username)->first();
    	if (!$user)
    		return err('用户名不存在');
    	
    	$hashed_password = $user->password;
    	if (!Hash::check($password, $hashed_password)) {
    		return err('密码有误');
    	}

    	session()->put('username',$user->username);
    	session()->put('user_id',$user->id);

    	return suc(['id'=>$user->id]);

    }

    public function has_username_and_password()
    {
    	$username = rq('username');
    	$password = rq('password');
    	if ($username && $password) 
    		return [$username, $password];
    	return $false;
    }

    //登出
    public function logout()
    {
    	$is_logged_in = $this->is_logged_in();
    	// dd($is_logged_in);
    	// dd(session()->all());

    	// session()->put('username',null);
    	// session()->put('user_id',null);
    	// $username = session()->pull('username');
    	// session()->set('person.name', 'xiaoming');
    	// session()->set('person.friend.xiaoming.age', '20');
    	// session()->flush();
    	session()->forget('user_id');
    	session()->forget('username');

    	return ['status'=>1];
    	// return redirect('/');
    }



    //检测是否登录
    public function is_logged_in()
    {
    	return session('user_id') ?: false;
    }

    public function answers()
    {
        return $this->belongsToMany('App\answer')
                    ->withPivot('vote')
                    ->withtimestamps();
    }

    public function questions()
    {
        return $this->belongsToMany('App\question')
                    ->withPivot('vote')
                    ->withtimestamps();
    }

    //修改密码API
    public function change_password()
    {

        if (!$this->is_logged_in()) 
            return err('login required');
        
        if(!rq('old_password') || !rq('new_password'))
            return err('old_password or new_password are required');

        $user = $this->find(session('user_id'));
        if(!Hash::check(rq('old_password'), $user->password))
            return err('invalid old_password');

        $user->password = bcrypt(rq('new_password'));
        
        return $user->save() ? suc(['msg'=>'修改成功']) : err('db change failed');
        
    }   

    //找回密码
    public function reset_password()
    {
        if($this->is_robot())
            return err('max frequency');
        
        if(!rq('phone'))
            return err('phone is required');

        $user = $this->where('phone', rq('phone'))->first();
        if(!$user)
            return err('invalid phone number');

        //生成验证码
        $captcha = $this->generate_captcha();
        $user->phone_captcha = $captcha;
        if ($user->save()) {
            //如果验证码保存成功,发送短信
            $this->send_sms();
            //为下一次机器调用检查
            $this->update_robot_time();
            return suc();
        }else{
            return err('db update failed');
        }
    }

    public function is_robot($time = 10)
    {
        if(!session('last_sms_time'))
            return false;
        $current_time = time();
        $last_sms_time = session('last_sms_time');
        return !(($current_time - $last_sms_time) > $time);
            
    }

    //更新机器人行为时间
    public function update_robot_time()
    {
        session()->put('last_sms_time', time());
    }

    //验证找回密码API
    public function validate_reset_password()
    {
        if($this->is_robot(2))
            return err('max frequency reached');

        if(!rq('phone') || !rq('phone_captcha') || !rq('new_password'))
            return err('phone and phone_captcha are required');

        $user = $this->where(['phone'=>rq('phone'),'phone_captcha'=>rq('phone_captcha')])
                     ->first();
        if(!$user)
            return err('invalid phone or phone_captcha');

        $user->password = bcrypt(rq('new_password'));
        $this->update_robot_time(); 
        return $user->save() ? suc() : err('db updated failed');

    }


    //发送短信
    public function send_sms()
    {
        return true;
    }

    //生成验证码
    public function generate_captcha()
    {
        return rand(1000,9999);
    }

}
