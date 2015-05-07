angular.module('angularFlatCharts', [])
    .directive('drawChart', function () {
        return {
            'restrict': 'AE',
            'template':
                '<canvas style="background-color: #0088CC;"></canvas>' +
                '<span>[[ positionX + ":" + positionY ]]</span>',
            'link': function ($scope, $element, $attrs) {

                function MaxY(data) {
                    var Ymax = 0;
                    for (var i = 1; i < data.length; i++) {
                        if (data[i].y > data[Ymax].y) {
                            Ymax = i;
                        }
                    }
                    return Ymax;
                }

                function calcY(data) {
                    var Ymax = data[MaxY(data)].y;
                    var height = parseInt($attrs.height);
                    if (!isNaN(height)) {
                        for (var i = 0; i < data.length; i++) {
                            data[i].y = Ymax - data[i].y;
                            data[i].y = (data[i].y * height) / Ymax;
                            data[i].x *= 33;
                        }
                    }
                    return data;
                }

                function getMouseCoords(canvas, $event) {
                    var rect = canvas.getBoundingClientRect();
                    return {
                        x: Math.round(($event.clientX-rect.left) / (rect.right-rect.left) * canvas.width),
                        y: Math.round(($event.clientY-rect.top) / (rect.bottom-rect.top) * canvas.height)
                    };
                }

                $scope.positionX = 0;
                $scope.positionY = 0;
                var canvas = $element.find('canvas')[0];
                var data = $scope.data;
                canvas.width = parseInt($attrs.width);
                canvas.height = parseInt($attrs.height);
                data = calcY(data);
                var canvasWrapper = canvas.getContext('2d');
                canvasWrapper.beginPath();
                canvasWrapper.moveTo(0, canvas.height);
                canvasWrapper.strokeStyle = "white";
                for (var i = 0; i < data.length; i++) {
                    canvasWrapper.lineTo(data[i].x, data[i].y);
                }
                canvasWrapper.stroke();

                canvas.addEventListener('mousemove', function ($event) {
                    var mousePosition = getMouseCoords(canvas, $event);
                    $scope.$apply(function () {
                        $scope.positionX = mousePosition.x;
                        $scope.positionY = mousePosition.y;
                    });
                });
            }
        }
    }
);