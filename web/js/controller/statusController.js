'use strict';

angular.module('miniFace')
.controller('statusController', [ '$scope', '$http', function($scope, $http){
    $scope.status = {};
    $scope.myUser = {};
    $scope.posts = [];

    $http.get('/api/posts').then(function(d){
        $scope.posts = d.data;
    });

    $http.get('/api/myUser').then(function(d){
        $scope.myUser = d.data;
    });

    $scope.postStatus = function(){
        if (typeof $scope.status.input === 'undefined' || $scope.status.input.length === 0){
            return;
        }

        $http.post('/api/status', { status: $scope.status.input}).then(function(result){
            $scope.posts.push(result.data);
        });
    };
}]);
