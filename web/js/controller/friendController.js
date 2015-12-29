'use strict';

angular.module('miniFace')
.controller('friendController', [ '$scope', '$http', function($scope, $http){

    $scope.friends = [];
    $scope.newFriend = {};

    $scope.addFriend = function(){
        if (typeof $scope.newFriend.name === 'undefined' || $scoep.newFriend.name.length === 0){
            return;
        }

        $http.post('/api/friend')
    };


}]);
