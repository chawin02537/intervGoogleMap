<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How to Add Google Map in Laravel? - ItSolutionStuff.com</title>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style type="text/css">
        #map {
            height: 800px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div id="map"></div>
    </div>

    <script type="text/javascript">
        function initMap() {
            var markers = [];

            // foreach dataForSend add name lat lng to markers
            @foreach($dataForSend as $value)
            var name = '{{$value['name']}}';
            var lat = {{$value['lat']}};
            var lng = {{$value['lng']}};
            var count = {{$value['count']}};

            // make array
            markers.push([name, lat, lng, count]);

            @endforeach

            console.log(markers);
            console.log(markers.length);

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 20,
                center: new google.maps.LatLng(13.828253, 100.5284507),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            // foreach markers add marker
            for (let i = 1; i <= markers.length; i++) {
                
                var name = markers[i][0];
                var lat = markers[i][1];
                var lng = markers[i][2];
                var count = markers[i-1][3];
                console.log(i, count);

                var myLatLng = new google.maps.LatLng(lat, lng);
                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    title: name
                });

                var infowindow = new google.maps.InfoWindow({
                    content: name + ' ' + count
                });

                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });
            }
        }

        window.initMap = initMap;
    </script>

    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap">
    </script>

</body>

</html>