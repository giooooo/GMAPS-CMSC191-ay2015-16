
<?php
    $db = mysqli_connect('localhost','root','','gmaps')
    or die('Error connecting to MySQL server.');
?>

<?php
    $query = "SELECT * FROM markers";
    mysqli_query($db, $query) or die('Error querying database.');

    $result = mysqli_query($db, $query);

    mysqli_close($db);
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <script src="http://maps.googleapis.com/maps/api/js?key=API_KEY"></script>
    <script>


        var map, infowindows = [], markers = [];

        let place_colors = {};

        place_colors['Restaurant'] = 'FF0000'; //red
        place_colors['Auditorium'] = '00FF00'; //green
        place_colors['Mall'] = '0000FF'; //blue
        place_colors['Inn'] = 'FFFF00'; //yellow
        place_colors['Bank'] = '800080'; //purple
        place_colors['Municipal Hall'] = '008080'; //teal
        place_colors['Resort'] = 'F4A460'; //brown
        place_colors['Amusement Park'] = 'FFFFFF'; //white
        
        var places = [];

        <?php while($row = mysqli_fetch_array($result)) { ?>
                places.push(<?php echo json_encode($row)?>);
        <?php }?>

        function addPolylines(path) {
            var polyline = new google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });

            polyline.setMap(map);
        }

        function addMarkers(place) {

            var location = {lat: Number(place.lat), lng: Number(place.lng)};

            var contentString = 
            '<h4> Name: ' + place.name  + '</h4>' +
            '<h4> Address: ' + place.address + '</h4>' +
            '<h4> Latitude: ' + location.lat + '</h4>' +
            '<h4> Longitude: ' + location.lng + '</h4>' +
            '<h4> Type: ' + place.type + '</h4>'

            var markerImage =
                new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + place_colors[place.type],
                new google.maps.Size(21, 34),
                new google.maps.Point(0,0),
                new google.maps.Point(10, 34));

            var marker = new google.maps.Marker({
                map      : map,
                position : location,
                title    : place.name,
                icon     : markerImage
            });

            var infowindow = new google.maps.InfoWindow({
                content: contentString,
                maxWidth: 200,
                maxHeight: 50
            });

            infowindows.push(infowindow);
            markers.push(marker);

            marker.addListener('click', function(){
                map.panTo(marker.getPosition());
                infowindow.open(map, marker);
                infowindows.forEach((e) => {
                    if(e != infowindow) e.close();
                });
            });

        }   

        function initialize() {
            var mapProp = {
                center    : new google.maps.LatLng(14.208888, 121.155655),
                zoom      : 15,
                mapTypeId : google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
            places.forEach(e => {return addMarkers(e)});

            addPolylines(places.filter(e => {return e.type === 'Mall'}).map(e=> {return { lat: Number(e.lat), lng: Number(e.lng) }}));
            var cityCircle = new google.maps.Circle({
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                map: map,
                center: {lat: 14.202888, lng: 121.155655},
                radius: 250
              });
        }

        google.maps.event.addDomListener(window, 'load', initialize);

        

</script>
</head>

<body>
    <div id="googleMap" style="width:1500px;height:1500px;"></div>
</body>
</html>