'use strict';

angular.module('miniFace')
.controller('allUsersController', [ '$scope', '$http', function($scope, $http){
    $scope.addedUsers = {};
    $http.get('/api/countFriends').then(function(d){
        $scope.friendsOfFriends = d.data;

        $http.post('/api/friendsInNetwork', { keys: d.data.ids.split(',') }).then(function(d){
            $scope.available_friends = d.data;

        });
    });

    $scope.addFriend = function(friend){
        if (typeof friend.name === 'undefined'){
            return;
        }

        $http.post('/api/friends', { name: friend.name }).then(function(d){
            $scope.$parent.friends.push(d.data);
            $scope.addedUsers[friend.name] = true;
        });
    };

    $scope.isAdded = function(f){
        return typeof $scope.addedUsers[f.name] !== 'undefined' && $scope.addedUsers[f.name] === true;
    };
}]);
