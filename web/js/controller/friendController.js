'use strict';

angular.module('miniFace')
.controller('friendController', [ '$scope', '$http', function($scope, $http){

    $scope.friends = [];

    $http.get('/api/friends').then(function(d){
        $scope.friends = d.data;
    });

}]);
