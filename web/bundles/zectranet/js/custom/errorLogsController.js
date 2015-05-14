Zectranet.controller('ErrorLogsController', ['$scope', '$http',
    function($scope, $http) {
        $scope.errors = null;
        var urlGetErrors = JSON_URLS.getErrors;

        function highlightSyntax(text) {
            var sqlKeywords = [
                'INSERT', 'INTO', 'SELECT', 'FROM', 'VALUES', 'DELETE'
            ];

            text = text.replace(new RegExp('class', 'gi'), '<span class="class">class</span>');
            text = text.replace(new RegExp('line', 'gi'), '<span class="line">line</span>');
            text = text.replace(new RegExp('[?]', 'gi'), '<span class="question">?</span>');
            text = text.replace(new RegExp('null', 'gi'), '<span class="orange">null</span>');
            text = text.replace(new RegExp('exception', 'gi'), '<span class="exception">exception</span>');
            text = text.replace(new RegExp('file', 'gi'), '<span class="file">file</span>');
            text = text.replace(new RegExp('function', 'gi'), '<span class="function">function</span>');
            for (var i = 0; i < sqlKeywords.length; i++) {
                text = text.replace(new RegExp(sqlKeywords[i], 'gi'),
                    '<span class="SQL">' + sqlKeywords[i] + '</span>')
            }

            return text;
        }

        function prepareErrors(errors) {
            for (var i = 0; i < errors.length; i++) {
                errors[i].message = highlightSyntax(errors[i].message);
                errors[i].where = highlightSyntax(errors[i].where);
                errors[i].from = highlightSyntax(errors[i].from);
            }
            return errors;
        }

        $scope.getErrors = function () {
            $http.get(urlGetErrors)
                .success(function (response) {
                    $scope.errors = prepareErrors(response);
                }
            );
        };
    }
]);