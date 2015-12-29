'use strict';

angular.module('miniFace')
.controller('friendController', [ '$scope', '$http', function($scope, $http){

    $scope.friends = [];
    $scope.newFriend = {};

    $http.get('/api/friends').then(function(d){
        $scope.friends = d.data;
    });

    $scope.addFriend = function(){
        if (typeof $scope.newFriend.name === 'undefined' || $scope.newFriend.name.length === 0){
            return;
        }

        $http.post('/api/friends', { name: $scope.newFriend.name }).then(function(d){
            $scope.friends.push(d.data);
        });
    };


}]);
