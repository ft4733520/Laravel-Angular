
	<!DOCTYPE html>
	<html lang="zh" ng-app="xiaohu">
	<head>
		<meta charset="utf-8">
		<title>晓乎</title>
		<link rel="stylesheet" type="text/css" href="/node_modules/normalize-css/normalize.css">
		<link rel="stylesheet" type="text/css" href="/css/base.css">
		<script type="text/javascript" src="/node_modules/jquery/dist/jquery.js"></script>
		<script type="text/javascript" src="/node_modules/angular/angular.js"></script>
		<script type="text/javascript" src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
		<script type="text/javascript" src="/js/base.js"></script>
	</head>
	<body>
		
		<div class="navbar clearfix">
			<div class="container">
				<div class="fl">
					<div class="navbar-item brand">晓乎</div>
					<form ng-submit="Question.go_add_question()" id="quick_ask" ng-controller="QuestionAddController">
						<div class="navbar-item">
							<input ng-model="Question.new_question.title" type="text" name="title">
						</div>
						<div class="navbar-item">
							<button type="submit">提问</button>	
						</div>
					</form>
				</div>
				<div class="fr">
					<a ui-sref="home" class="navbar-item">首页</a>
					@if(is_logged_in())
						<a ui-sref="login" class="navbar-item">{{session('username')}}</a>
						<a href="{{url('/api/logout')}}" class="navbar-item">登出</a>
					@else
						<a ui-sref="login" class="navbar-item">登录</a>
						<a ui-sref="signup" class="navbar-item">注册</a>
					@endif
				</div>
				
			</div>
		</div>
		
		<div class="page">
			<div ui-view></div>
		</div>
	</body>

	<!-- .page .home -->
	<script type="text/ng-template" id="home.tpl">
		<div class="home card container">
			<h1>最新动态</h1>
			<div class="hr"></div>
			<div class="item-set">
				<div class="item">
					<div class="vote"></div>
					<div class="item-content">
						<div class="content-act">XX赞同了该内容</div>
						<div class="title">我真的帅吗</div>
						<div class="content-owner">小明</div>
						<div class="content-main">
							12313123
						</div>
						<div class="action-set">
							<div class="comment">评论</div>
						</div>
					</div>
				</div>
			
			</div>
		</div>
	</script>

	<script type="text/ng-template" id="login.tpl">
		<div  ng-controller="LoginController" class="login container">
			<div class="card">
				<h1>登录</h1>
				<form name="login_form" ng-submit="User.login()">
					<div class="input-group">
						<label>用户名: </label>
						<input name="username" 
							   type="text" 
							   ng-model="User.signup_data.username"
							   required
						>
						<div ng-if="login_form.username.$touched" class="input-error-set">
							<div ng-if="login_form.username.$error.required">用户名为必填项</div>
						</div>
					</div>
					<div  class="input-group">
						<label>密码</label>
						<input name="password" 
							   type="password" 
							   ng-model="User.signup_data.password"
							   required 
						>
						<div ng-if="login_form.password.$touched" class="input-error-set">
							<div ng-if="login_form.password.$error.required">密码为必填项</div>
						</div>
					</div>
					<div ng-if="User.login_failed" class="input-error-set">
						用户名或密码有误 
					</div>
					<div class="input-group">
						<button class="primary" type="submit"
							ng-disabled="login_form.username.$error.required ||login_form.password.$error.required"	
					>
						登录
					</button>
					</div>
				</form>	
			</div>
		</div>
	</script>

	<script type="text/ng-template" id="signup.tpl">
		<div ng-controller="SignupController" class="signup container">
			<div class="card">
				<h1>注册</h1>

				<form name="signup_form" ng-submit="User.signup()">
					<div class="input-group">
						<label>用户名: </label>
						<input name="username" 
							   type="text" 
							   ng-minlength="4"
							   ng-maxleng="24"
							   ng-model="User.signup_data.username"
							   ng-model-options="{debounce:500}"
							   required
						>
						<div ng-if="signup_form.username.$touched" class="input-error-set">
							<div ng-if="signup_form.username.$error.required">用户名为必填项</div>
							<div ng-if="signup_form.username.$error.maxlength||signup_form.username.$error.minlength">用户名长度需在4至24位之间</div>
							<div ng-if="User.signup_username_exists">用户名已存在</div>
						</div>
					</div>
					<div  class="input-group">
						<label>密码</label>
						<input name="password" 
							   type="password" 
							   ng-minlength="6"
							   ng-maxleng="255"
							   ng-model="User.signup_data.password"
							   required 
						>
						<div ng-if="signup_form.password.$touched" class="input-error-set">
							<div ng-if="signup_form.password.$error.required">密码为必填项</div>
							<div ng-if="signup_form.password.$error.maxlength||signup_form.password.$error.minlength">密码长度需在6至255位之间</div>
						</div>
					</div>
					<div  class="input-group">
						<button class="primary" type="submit"
								ng-disabled="signup_form.$invalid"	
						>
							注册
						</button>
					</div>
				</form>

			</div>
		</div>
	</script>


	<script type="text/ng-template" id="question.add.tpl">
		<div  ng-controller="QuestionAddController" class="question-add container">
			<div class="card">
				<form name="question_add_form" ng-submit="Question.add()">
					<div class="input-group">
						<label>问题标题</label>
						<input type="text" 
							   name="title" 
							   ng-minlength="5" 
							   ng-maxlength="255"
							   ng-model="Question.new_question.title" 
							   required>
					</div>
					<div class="input-group">
						<label>问题描述</label>
						<textarea type="text" ng-model="Question.new_question.desc" name="desc" required></textarea>
					</div>
					<div class="input-group">
						<button class="primary" ng-disabled="question_add_form.$invalid" type="submit">提交</button>
					</div>
				
				</form>
			</div>
		</div>
	</script>
	</html>