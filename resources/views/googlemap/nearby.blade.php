<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How to Add Google Map in Laravel? - ItSolutionStuff.com</title>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style type="text/css">
        #map {
            height: 800px;
        }
    </style>
</head>

<body>
    {{-- form input searchName with button submit_search --}}
    <div class="container form mb-3">
        <div class="row d-flex justify-content-center">
            <input class="form-control mb-2" type="text" id="searchName" name="searchName" placeholder="Search Name">
            <button type="button" class="col-6 btn btn-primary" id="submit_search" name="submit_search">Search</button>
        </div>
    </div>

    <div class="container">
        <div id="map"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap">
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: new google.maps.LatLng(13.828253, 100.567481),
                zoom: 15
            });
            window.initMap = initMap;
            initMap('');
        });
        
        $(function () {
            document.getElementById("submit_search").onclick = function () {
                var searchName = $("#searchName").val();
                deleteMarkers();
                initMap(searchName);
            }
        })

        var markers = [];
        var lat = 13.828253;
        var lng = 100.567481;

        function initMap(searchName) {
            // request get data from GoogleMapController
            $.ajax({
                url: "{{ route('map.nearby') }}",
                type: "GET",
                data: {
                    searchName: searchName
                },
                success: function (data) {

                    var totalCount = data.length;
                    var count = totalCount;

                    var infowindow = null;
                    
                    $.each(data, function (index, value) {
                        var name = value.name;
                        var lat = value.lat;
                        var lng = value.lng;
                        var count = totalCount - index;
                        var url = value.url;
                        var adr = value.adr;

                        count = count - 1;

                        var contentString = 
                        '<div id="content">'+
                        '<div id="siteNotice">'+
                        '</div>'+
                        '<h1 id="firstHeading" class="firstHeading">'+name+'</h1>'+
                        '<div id="bodyContent">'+
                        '<p><b>'+name+'</b>, '+adr+'</p>'+
                        '<p><a href="'+url+'">'+
                        'Go to Map</a> '+
                        '</div>'+
                        '</div>';
                        
                        // set marker
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lat, lng),
                            map: map,
                            title: name,
                        });

                        // add event listener
                        google.maps.event.addListener(marker, 'click', (function (marker, count) {
                            return function () {
                                if (infowindow) {
                                    infowindow.close();
                                }
                                infowindow = new google.maps.InfoWindow({
                                    content: contentString
                                });
                                infowindow.open(map, marker);
                            }
                        })(marker, count));

                        markers.push(marker);
                    });
                        
                    window.initMap = initMap;
                }
            });
        }

        function deleteMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        }
        
    </script>
</body>

</html>