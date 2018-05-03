<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
	public function timeline()
	{
		list($limit,$skip) = paginate(rq('page'), rq('limit'));
		$questions = question_ins()->limit($limit)
								  ->skip($skip)
								  ->orderBy('created_at','desc')
								  ->get();

		$answers = answer_ins()->limit($limit)
								  ->skip($skip)
								  ->orderBy('created_at','desc')
								  ->get();

		$data = $questions->merge($answers);
		$data = $data->sortBy(function($item){
			return $item->created_at;
		});
		$data = $data->values()->all();
		return ['status'=>'1','data'=>$data];

	}

}
