
	;(function()
	{
		'use strict';

		angular.module('xiaohu',[
				'ui.router',

			])
			.config([
				'$interpolateProvider',
				'$stateProvider',
				'$urlRouterProvider',
				function($interpolateProvider,
							 $stateProvider,
							 $urlRouterProvider)
				{
					$interpolateProvider.startSymbol('[:');
					$interpolateProvider.endSymbol(':]');

					$urlRouterProvider.otherwise('/home');

					$stateProvider
						.state('home',{
							url: '/home',
							templateUrl: 'home.tpl', //如果没有，则会在跟目录下找文件 localhost:8000/home.tpl					
						})
						.state('signup',{
							url: '/signup',
							templateUrl: 'signup.tpl',						
						})
						.state('login',{
							url: '/login',
							templateUrl: 'login.tpl',						
						})
						.state('question',{
							abstract: true,
							url: '/question',
							template: '<div ui-view></div>',						
						})

						.state('question.add',{
							url: '/add',
							templateUrl: 'question.add.tpl',						
						})
				}
			])

			// .controller('TestController', function($scope){
			// 	$scope.name = 'Bob';
			// })
			.service('UserService', [
				'$state',
				'$http',
				function($state,$http){
					var me = this;
					me.signup_data = {};
					me.signup = function()
					{
						$http.post('/api/signup', me.signup_data)
							.then(function(res){
								if(res.data.status){
									me.signup_data = {};
									$state.go('login');
								}else{
									$state.go('signup');
								}
								
							},function(err){
								console.log('error',err);
							})
					}

					me.login = function()
					{
						$http.post('/api/login', me.signup_data)
							.then(function(res){
								if(res.data.status)
								{
									$state.go('home');
									// location.href='/';
								}else{
									me.login_failed=true;
								}
							},function(err){
								console.log('error',err);
							})
					}

					me.username_exists = function()
					{
						$http.post('/api/user/exist', {username: me.signup_data.username})
							 .then(function(res){
							 	if(res.data.status && res.data.data.count)
							 		me.signup_username_exists = true;
							 	else
							 		me.signup_username_exists = false;
							 },function(err){
							 	console.log('error',err);
							 })
					}
				}
			])

			.controller('SignupController',[
				'$scope',
				'UserService',
				function($scope, UserService)
				{
					$scope.User = UserService;
					$scope.$watch(function(){
						return UserService.signup_data;
					}, function(n,o){
						if (n.username != o.username)
							UserService.username_exists();
					},true);
				}
			])

			.controller('LoginController',[
				'$scope',
				'UserService',
				function($scope, UserService){
					$scope.User = UserService;
				}

			])


			.service('QuestionService',[
				'$state',
				'$http',
				function($state,$http){
					var me = this;
					me.new_question = {};

					me.go_add_question = function(){
						$state.go('question.add');
					}

					me.add = function()
					{
						if(!me.new_question.title)
							return;
						$http.post('/api/question/add',me.new_question)
							.then(function(res){
								if(res.data.status)
									me.new_question = {};
									$state.go('home');
							},function(err){

							})
					}
				}

			])

			.controller('QuestionAddController',[
				'$scope',
				'QuestionService',
				function($scope, QuestionService){
					$scope.Question = QuestionService;
				}

			])

		/*rootScope*/


	})();