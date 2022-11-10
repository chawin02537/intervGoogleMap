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
    <div class="container mt-5">
        <h2>How to Add Google Map in Laravel? - ItSolutionStuff.com</h2>
        <div id="map"></div>
    </div>

    <script type="text/javascript">
        function initMap() {
            // map multiple marker
            var markers = [
                [
                    "ร้านอาหาร ศิริชัยไก่ย่าง สาขาบางซื่อ",
                    13.8302636,
                    100.5347839,
                    1
                ],
                [
                    "S&amp;P วงศ์สว่าง",
                    13.8276971,
                    100.5283258,
                    2
                ],
                [
                    "อีสานเพลซ ร้านอาหาร",
                    13.8290149,
                    100.5293492,
                    3
                ],
                [
                    "ร้าน โมโมจิ",
                    13.8212047,
                    100.5315726,
                    4
                ],
                [
                    "Jack&amp;Dea Store ตลาดสยามยิปซี",
                    13.8219275,
                    100.5261268,
                    5
                ],
                [
                    "Nittaya Grilled Chicken",
                    13.830124,
                    100.5366414,
                    6
                ],
                [
                    "เจริญชัยไก่ตอน",
                    13.8247708,
                    100.5375653,
                    7
                ],
                [
                    "Chimethai",
                    13.8237164,
                    100.5372815,
                    8
                ],
                [
                    "About Beef",
                    13.8307766,
                    100.5386816,
                    9
                ],
                [
                    "Come Home",
                    13.8189777,
                    100.5360268,
                    10
                ],
                [
                    "อิ่มจัง",
                    13.8166304,
                    100.5309249,
                    11
                ],
                [
                    "ซาลาเปาคุณเหวิน (ด่านประชาชื่นขาเข้า)",
                    13.8371503,
                    100.5369666,
                    12
                ],
                [
                    "GyuYoshi",
                    13.8254006,
                    100.515345,
                    13
                ],
                [
                    "S&amp;P รพ.เกษมราษฎร์ ประชาชื่น",
                    13.8318612,
                    100.5387861,
                    14
                ],
                [
                    "บจก. สยามคาร์ดอทคอม",
                    13.836747,
                    100.523254,
                    15
                ],
                [
                    "ร้านแฮมเบอร์เกอร์ bless me bergur",
                    13.8222844,
                    100.5322586,
                    16
                ],
                [
                    "Black Canyon รพ.เกษมราษฎร์ ประชาชื่น",
                    13.8317952,
                    100.5387918,
                    17
                ],
                [
                    "Shabushi by Oishi Big C Wongsawang",
                    13.827828,
                    100.5284016,
                    18
                ],
                [
                    "KFC Wongsawang Center",
                    13.8278652,
                    100.5286791,
                    19
                ],
                [
                    "Noodle boat pranakon wongsawang",
                    13.8277983,
                    100.5286326,
                    20
                ]
            ];

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: new google.maps.LatLng(13.828253, 100.5284507),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            // foreach marker
            for (var i = 0; i < markers.length; i++) {
                console.log(markers[i][0], markers[i][1], markers[i][2], markers[i][3]);
                var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: markers[i][0]
                });
            }

        }

        window.initMap = initMap;
    </script>

    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap">
        </script>

    <script type="text/javascript">
        // press enter change focus
        $(document).on('keypress', function (e) {
            if (e.which == 13) {
                // random lat lng
                var lat = Math.random() * (90 - (-90)) + (-90);
                var lng = Math.random() * (180 - (-180)) + (-180);
                console.log(lat, lng);
                // re render map
                const myLatLng = { lat: lat, lng: lng };
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 5,
                    center: myLatLng,
                });

                new google.maps.Marker({
                    position: myLatLng,
                    map,
                    title: "Hello Rajkot!",
                });

                window.initMap = initMap;
            }
        });

        // press ctrl + enter change focus
        $(document).on('keydown', function (e) {
            if (e.ctrlKey && e.which == 13) {
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 10,
                    center: new google.maps.LatLng(13.828253, 100.5284507),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                new google.maps.Marker({
                    position: new google.maps.LatLng(13.828253, 100.5284507),
                    map,
                    title: "Bang sue",
                });
            }
        });
    </script>

</body>

</html>