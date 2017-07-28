admin_app.controller('swap_prem', function($scope, $http) {

    $scope.init_prem = {
        resource: {
            'prem': 'resource',
            'name': 'Figther'
        },
        hod: {
            'prem': 'hod',
            'name': 'HoD'
        },
        cs: {
            'prem': 'cs',
            'name': 'CS'
        },
        fc: {
            'prem': 'fc',
            'name': 'FC'
        },
        hr: {
            'prem': 'hr',
            'name': 'HR'
        },
        admin: {
            'prem': 'admin',
            'name': 'Admin'
        },
        csd: {
            'prem': 'csd',
            'name': 'CSD'
        },
        account: {
            'prem': 'account',
            'name': 'Account'
        }
    };
    $scope.selected_prem = {};
    $scope.index_right = {};
    $scope.index_left = {};
    $scope.selected_user = {};
    $scope.index_user_right = {};
    $scope.index_user_left = {};
    $scope.init_user = {};
    $(function() {
        var id = $("#g_id").val();
        var link = $("#g_id").attr("link");
        if (id != "" && link != "") {
            $scope.init_selected_prem(id, link);
        }
        $scope.get_init_user(id, link);
    });
    $scope.init_selected_prem = function(g_id, url) {
        $http.post(url, {
            "flag": 'get_init_selected_prem',
            "g_id": g_id
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                var x;
                for (x in data['data']) {
                    $scope.selected_prem[x] = $scope.init_prem[x];
                    delete $scope.init_prem[x];
                }
                console.log(data['data']);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.get_init_user = function(g_id, url) {
        $http.post(url, {
            "flag": 'get_init_user'
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                //$scope.init_user=data['data'];
                for (x in data['data']) {
                    $scope.init_user[x] = data['data'][x];
                }
                if (g_id != "" && url != "") {

                    $scope.get_init_selected_user(g_id, url);

                }
                console.log(data['data']);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.get_init_selected_user = function(g_id, url) {
        $http.post(url, {
            "flag": 'get_init_selected_user',
            "g_id": g_id
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                var x;
                console.log($scope.init_user);
                //$scope.selected_user=data['data'];
                for (x in data['data']) {
                    $scope.selected_user[x] = data['data'][x];
                }
                console.log($scope.selected_user);
                //console.log($scope.init_user);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.select_right = function(index, $event) {
        if ($scope.index_right.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_right[index];
        } else {
            $scope.index_right[index] = index;
            $($event.target).parent().css("background-color", "red");
        }
    }
    $scope.select_left = function(index, $event) {
        if ($scope.index_left.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_left[index];
        } else {
            $scope.index_left[index] = index;
            $($event.target).parent().css("background-color", "blue");
        }
    }
    $scope.move_left = function(index, $event) {
        var x;
        for (x in $scope.index_right) {
            $scope.selected_prem[x] = $scope.init_prem[x];
            delete $scope.init_prem[x];
            delete $scope.index_right[x];
        }
    }
    $scope.move_right = function(index, $event) {
        var x;
        for (x in $scope.index_left) {
            $scope.init_prem[x] = $scope.selected_prem[x];
            delete $scope.selected_prem[x];
            delete $scope.index_left[x];
        }
    }







    //---------------user function section------------------

    $scope.select_user_right = function(index, $event) {
        if ($scope.index_user_right.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_user_right[index];
        } else {
            $scope.index_user_right[index] = index;
            $($event.target).parent().css("background-color", "red");
        }
    }
    $scope.select_user_left = function(index, $event) {
        if ($scope.index_user_left.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_user_left[index];
        } else {
            $scope.index_user_left[index] = index;
            $($event.target).parent().css("background-color", "blue");
        }
    }
    $scope.move_user_left = function(index, $event) {
        var x;
        for (x in $scope.index_user_right) {
            $scope.selected_user[x] = $scope.init_user[x];
            delete $scope.init_user[x];
            delete $scope.index_user_right[x];
        }
    }
    $scope.move_user_right = function(index, $event) {
        var x;
        for (x in $scope.index_user_left) {
            $scope.init_user[x] = $scope.selected_user[x];
            delete $scope.selected_user[x];
            delete $scope.index_user_left[x];
        }
    }
});





admin_app.filter("toArray", function() {
    return function(obj) {
        var result = [];
        angular.forEach(obj, function(val, key) {
            result.push(val);
        });
        return result;
    };
});






admin_app.controller('swap_hour_rate', function($scope, $http) {

    $scope.init_hour_rate = {};
    $scope.selected_rate = {};
    $scope.index_right = {};
    $scope.index_left = {};
    $(function() {
        var id = $("#username").val();
        var link = $("#username").attr("link");
        var init_link = $("#init_hour").attr("link");
        $scope.init_rate(id, init_link, link);
    });
    $scope.init_rate = function(username, url, link) {
        $http.post(url, {
            "flag": 'get_init_hour_rate',
            "username": username
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                $scope.init_hour_rate = data['data'];
                if (username != "" && link != "") {

                    $scope.init_selected_rate(username, link);

                }
                console.log(data['data']);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.init_selected_rate = function(username, url) {
        $http.post(url, {
            "flag": 'get_init_selected_rate',
            "username": username
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                var x;
                for (x in data['data']) {
                    $scope.selected_rate[x] = $scope.init_hour_rate[x];
                    delete $scope.init_hour_rate[x];
                }
                console.log(data['data']);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.select_right = function(index, $event) {
        if ($scope.index_right.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_right[index];
        } else {
            $scope.index_right[index] = index;
            $($event.target).parent().css("background-color", "red");
        }
    }
    $scope.select_left = function(index, $event) {
        if ($scope.index_left.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_left[index];
        } else {
            $scope.index_left[index] = index;
            $($event.target).parent().css("background-color", "blue");
        }
    }
    $scope.move_left = function(index, $event) {
        var x;
        for (x in $scope.index_right) {
            $scope.selected_rate[x] = $scope.init_hour_rate[x];
            delete $scope.init_hour_rate[x];
            delete $scope.index_right[x];
        }
    }
    $scope.move_right = function(index, $event) {
        var x;
        for (x in $scope.index_left) {
            $scope.init_hour_rate[x] = $scope.selected_rate[x];
            delete $scope.selected_rate[x];
            delete $scope.index_left[x];
        }
    }
});




admin_app.controller('swap_hour_rate_pos', function($scope, $http) {

    $scope.init_hour_rate = {};
    $scope.selected_rate = {};
    $scope.index_right = {};
    $scope.index_left = {};
    $(function() {
        var id = $("#pos_name").attr("pid");
        var init_link = $("#init_hour").attr("link");
        $scope.init_rate(id, init_link);
    });
    $scope.init_rate = function(position_id, url) {
        $http.post(url, {
            "flag": 'get_init_hour_rate'
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                $scope.init_hour_rate = data['data'];
                if (position_id != "") {

                    $scope.init_selected_rate(position_id, url);

                }
                console.log(data['data']);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.init_selected_rate = function(position_id, url) {
        $http.post(url, {
            "flag": 'get_init_selected_rate_position',
            "position_id": position_id
        }).
        success(function(data, status, headers) {
            if (data['flag'] == "OK") {
                var x;
                for (x in data['data']) {
                    $scope.selected_rate[x] = $scope.init_hour_rate[x];
                    delete $scope.init_hour_rate[x];
                }
                console.log(data['data']);

            } else {
                alert(data['flag'])
            }
        }).
        error(function(data, status, headers) {
            alert(headers);
        });
    }
    $scope.select_right = function(index, $event) {
        if ($scope.index_right.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_right[index];
        } else {
            $scope.index_right[index] = index;
            $($event.target).parent().css("background-color", "red");
        }
    }
    $scope.select_left = function(index, $event) {
        if ($scope.index_left.hasOwnProperty(index)) {
            $($event.target).parent().removeAttr("style");
            delete $scope.index_left[index];
        } else {
            $scope.index_left[index] = index;
            $($event.target).parent().css("background-color", "blue");
        }
    }
    $scope.move_left = function(index, $event) {
        var x;
        for (x in $scope.index_right) {
            $scope.selected_rate[x] = $scope.init_hour_rate[x];
            delete $scope.init_hour_rate[x];
            delete $scope.index_right[x];
        }
    }
    $scope.move_right = function(index, $event) {
        var x;
        for (x in $scope.index_left) {
            $scope.init_hour_rate[x] = $scope.selected_rate[x];
            delete $scope.selected_rate[x];
            delete $scope.index_left[x];
        }
    }
});
