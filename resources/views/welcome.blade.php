<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        #map {
            height: 400px;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        #distance{
            font-size: 50px;
        }
    </style>
</head>
<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @if (Auth::check())
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ url('/login') }}">Login</a>
                    <a href="{{ url('/register') }}">Register</a>
                @endif
            </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                Grupo-E-012017
            </div>
            <div id="map" class="m-b-md"></div>
            <div id="distance"></div>
        </div>
    </div>
    <script>
        function initMap() {
            var markerArray = [];

            var directionsService = new google.maps.DirectionsService;

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: {lat: -34.7101435, lng: -58.2858949}
            });

            var directionsDisplay = new google.maps.DirectionsRenderer({map: map});

            var stepDisplay = new google.maps.InfoWindow;

            calculateAndDisplayRoute(directionsDisplay, directionsService, markerArray, stepDisplay, map);
        }

        function calculateAndDisplayRoute(directionsDisplay, directionsService, markerArray, stepDisplay, map) {

            for (var i = 0; i < markerArray.length; i++) {
                markerArray[i].setMap(null);
            }

            directionsService.route({
                origin: 'universidad de quilmes',
                destination: 'estacion de bernal',
                avoidTolls: true,
                travelMode: google.maps.TravelMode.WALKING
            }, function (response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                    document.getElementById('distance').innerHTML = response.routes[0].legs[0].distance.text;
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap"
            async defer></script>
</body>
</html>
