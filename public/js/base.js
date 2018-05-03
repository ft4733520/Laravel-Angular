
	;(function()
	{
		'use strict';

		angular.module('xiaohu',[
				'ui.router',

			])
			.config(function($interpolateProvider){
				$interpolateProvider.startSymbol('[:');
				$interpolateProvider.endSymbol(':]');
			})

			.controller('TestController', function($scope){
				$scope.name = 'Bob';
			})

		/*rootScope*/


	})();