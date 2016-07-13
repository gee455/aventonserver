<?php
error_reporting(0);
session_start();
include('../Models/ConDB.php');
$db1 = new ConDB();

//$uri = $_SERVER['REQUEST_URI'];
//$apptId = end(explode('/',$uri));

$apptId = $_GET['id'];

$slavequery = "select ap.appointment_id,d.mobile,d.type_id,d.email,ap.status,sl.first_name,sl.slave_id,sl.last_name,sl.phone,ap.appointment_dt,ap.appointment_id,ap.car_id,ap.payment_type,ap.appt_lat,ap.appt_long,ap.drop_lat,ap.drop_long,ap.address_line1,ap.drop_addr1,d.first_name as fname,d.profile_pic from appointment ap,slave sl,master d where ap.slave_id = sl.slave_id  and ap.mas_id=d.mas_id and ap.appointment_id = '" . $apptId . "'";
//echo $slavequery;
$result2 = mysql_query($slavequery, $db1->conn);
while ($row = mysql_fetch_assoc($result2)) {

    $lat1 = $row['appt_lat'];
    $long1 = $row['appt_long'];
    $lat2 = $row['drop_lat'];
    $long2 = $row['drop_long'];
    $driver_name = $row['fname'];
    $driver_pic = $row['profile_pic'];
    $carid = $row['car_id'];
    $type_id = $row['type_id'];
    $driver_email = $row['email'];
    $dph = $row['mobile'];
    $statusresult = $row['status'];
    $appidres = $row['appointment_id'];
    $pickup = $row['address_line1'];
    $drop = $row['drop_addr1'];
}

//print_r($row);
if ($statusresult == 5 || $statusresult == 6 || $statusresult == 7 || $statusresult == 8 || $statusresult == 9) {

    $carplate = "select License_Plate_No,(select type_name from workplace_types where type_id = " . $type_id . ") as type_name,Vehicle_Color,Vehicle_Model from workplace where workplace_id=" . $carid;
//echo $carplate;
    $result = mysql_query($carplate, $db1->conn);
    while ($row3 = mysql_fetch_array($result)) {
        $plateno = $row3['License_Plate_No'];
        $typename = $row3['type_name'];
        $color = $row3['Vehicle_Color'];
        //$no = $row3['License_Plate_No'];
        $model = $row3['Vehicle_Model'];
    }


    $carplatemodel = "select vehiclemodel from vehiclemodel where id=" . $model;
    $result9 = mysql_query($carplatemodel, $db1->conn);
    while ($row9 = mysql_fetch_array($result9)) {

        $carmodel = $row9['vehiclemodel'];
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Share link</title>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
        <meta charset="utf-8">
        <style>
            html, body, #map_canvas {
                margin: 0;
                padding: 0;
                height: 100%;
            }
            #map-canvas {
                height:100% !important;
                margin: 0px;
                padding: 0px;
                width:100%;
            }
            .mainpage {
                height:100%;
                width:100%;
            }
            .head {
                width:100%;
                height:7%;
                // border:solid 1px black;
            }
            .mapslide {
                width:100%;
                height:100%;
                //border:solid 1px black;
            }
            .sidebar1{
                width: 100%;
                bottom:0px;
                position:fixed;
                background-color: #2a2a2a;
                // border:solid 1px black;
            }
            .driver_pic{
                padding-left:0.5%;
                padding-top:0.5%;
                float:left;
                //width:20%;
            }
            .driver_details{
                // width:78%;
                float:left;
                color:white !important;
            }
            .clear{
                clear: both;
            }
            .row-lable-lacation
            {
                width:50%;
                float: left;
                font-size: 10pt;  font-style: roboto regular;color:#969696;
            }
            .row-value-lacation
            {
                width:50%;
                float: left;
                font-size: 10pt;  font-style: roboto regular;color:#969696;
            }
        </style>





        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

        <script>






            google.maps.event.addDomListener(window, 'load', function () {
//                alert('Loading done');
                initialize();
            });




            function initialize() {
                var map;
<?php
if ($statusresult == 5 || $statusresult == 6 || $statusresult == 7 || $statusresult == 8) {
    ?>
                    if (navigator.geolocation) {

                        var mapOptions = {
                            zoom: 14,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };
                        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                        //******************************************************************
    <?php
    if ($lat2 != '0' || $long2 != '0') {
        ?>
                            var markers = [
                                {
                                    "title": '<?php echo $pickup; ?>',
                                    "lat": '<?php echo $lat1; ?>',
                                    "lng": '<?php echo $long1; ?>',
                                    "icon": 'default_marker_p@2x.png',
                                }
                                ,
                                {
                                    "title": '<?php echo $drop; ?>',
                                    "lat": '<?php echo $lat2; ?>',
                                    "lng": '<?php echo $long2; ?>',
                                    "icon": 'default_marker_d@2x.png',
                                }
                            ];

        <?php
    } else {
        ?>
                            var markers = [
                                {
                                    "title": '<?php echo $pickup; ?>',
                                    "lat": '<?php echo $lat1; ?>',
                                    "lng": '<?php echo $long1; ?>',
                                    "icon": 'default_marker_p@2x.png',
                                }
                            ];

        <?php
    }
    ?>
                        var lat_lng = new Array();
                        var latlngbounds = new google.maps.LatLngBounds();
                        for (i = 0; i < markers.length; i++) {
                            var data = markers[i]
                            var myLatlng = new google.maps.LatLng(data.lat, data.lng);
                            lat_lng.push(myLatlng);
                            var marker = new google.maps.Marker({
                                position: myLatlng,
                                map: map,
                                title: data.title,
                                icon: data.icon
                            });

                            latlngbounds.extend(marker.position);
                            (function (marker, data) {
                                google.maps.event.addListener(marker, "click", function (e) {
                                    infoWindow.setContent(data.description);
                                    infoWindow.open(map, marker);
                                });
                            })(marker, data);
                        }
                        map.setCenter(latlngbounds.getCenter());
    <?php
    if ($lat2 != '0' || $long2 != '0') {
        ?>


                            map.fitBounds(latlngbounds);
                            var path = new google.maps.MVCArray();

                            //Intialize the Direction Service
                            var service = new google.maps.DirectionsService();

                            //Set the Path Stroke Color
                            var poly = new google.maps.Polyline({map: map, strokeColor: '#4986E7'});

                            //Loop and Draw Path Route between the Points on MAP
                            for (var i = 0; i < lat_lng.length; i++) {
                                if ((i + 1) < lat_lng.length) {
                                    var src = lat_lng[i];
                                    var des = lat_lng[i + 1];
                                    path.push(src);
                                    poly.setPath(path);
                                    service.route({
                                        origin: src,
                                        destination: des,
                                        travelMode: google.maps.DirectionsTravelMode.DRIVING
                                    }, function (result, status) {
                                        if (status == google.maps.DirectionsStatus.OK) {
                                            for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                                                path.push(result.routes[0].overview_path[i]);
                                            }
                                        }
                                    });
                                }
                            }
    <?php } ?>
                        /*************************************car part**************************/

                        $.ajax({
                            type: "POST",
                            url: "shareFeatureGetlatlong.php",
                            data: {
                                type: 1, lat: 37.786426544189, lon: -122.40463256836, ids: <?php echo $_GET['id']; ?>, carids: <?php echo $carid; ?>
                            },
                            dataType: 'json',
                            success: function (response) {
                                //                        alert(response.data);
                                $.each(response, function (index, row) {

                                    var pos = new google.maps.LatLng(row.lat, row.lon);
                                    var image = 'fender.png'
                                    //                                alert(row.html);
                                    // Initialise the infoWindow
                                    var infoWindow = new google.maps.InfoWindow({
                                        content: row.html
                                    });
                                    var marker = new google.maps.Marker({
                                        position: pos,
                                        map: map,
                                        icon: 'home_caricon.png'
                                    });
                                    //                            map.setCenter(pos);

                                    // Display our info window when the marker is clicked
                                    google.maps.event.addListener(marker, 'click', function () {
                                        infoWindow.open(map, marker);

                                        var driverId = row.id;
                                        var appointmentId = row.apptId;
                                        sample(driverId, appointmentId);
                                        //document.getElementsByClassName('slidebar1')[0].innerHTML = a;

                                    });
                                    //                            alert(row.id);
                                    interval = window.setInterval(function () {

                                        $.ajax({
                                            type: "POST",
                                            url: "shareFeatureGetlatlong.php",
                                            data: {type: 2, id: row.id},
                                            dataType: "JSON",
                                            success: function (res) {

                                                var dpos = new google.maps.LatLng(res.lat, res.lon);
                                                if (marker) {
                                                    // Marker already created - Move it
                                                    //                                            map.setCenter(dpos);
                                                    marker.setPosition(dpos);
                                                }
                                                else {
                                                    // Marker does not exist - Create it
                                                    //                                            map.setCenter(dpos);
                                                    marker = new google.maps.Marker({
                                                        position: dpos,
                                                        map: map,
                                                        icon: 'home_caricon.png'
                                                    });
                                                }

                                            }
                                        });
                                        //                                            navigator.geolocation.getCurrentPosition(function(position) {
                                        //                                            });
                                    }, 10000);
                                });
                            }
                            ,
                            error: function () {
                                alert('error');
                            }
                        });
                    } else {
                        document.getElementById('google_canvas').innerHTML = 'No Geolocation Support.';
                    }

                    /***********************************************************************/

    <?php
} else if ($statusresult == 9) {
    ?>



                    if (navigator.geolocation) {

                        var mapObject;

                        var mapOptions = {
                            zoom: 15,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };


                        mapObject = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                        navigator.geolocation.getCurrentPosition(function (position) {

                            var geolocate = new google.maps.LatLng(<?php echo $lat1; ?>, <?php echo $long1; ?>);


                            // alert(position.coords.latitude);
                            var infowindow = new google.maps.InfoWindow({
                                map: mapObject,
                                position: geolocate,
                                content:
                                        '<h2 style="color:gray;">Appointment Already Completed</h2>'
                            });
                            mapObject.setCenter(geolocate);


                        });

                    } else {
                        document.getElementById('google_canvas').innerHTML = 'No Geolocation Support.';
                    }


    <?php
} else if ($statusresult == 4 || $statusresult == 5) {
    ?>
                    alert("Here2");


                    if (navigator.geolocation) {

                        var mapObject;

                        var mapOptions = {
                            zoom: 15,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };


                        mapObject = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                        navigator.geolocation.getCurrentPosition(function (position) {

                            var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);



                            // alert(position.coords.latitude);
                            var infowindow = new google.maps.InfoWindow({
                                map: mapObject,
                                position: geolocate,
                                content:
                                        '<h2 style="color:gray;">Appointment Cancelled</h2>'
                            });
                            mapObject.setCenter(geolocate);


                        });

                    } else {
                        document.getElementById('google_canvas').innerHTML = 'No Geolocation Support.';
                    }

<?php } else { ?>
                    if (navigator.geolocation) {

                        var mapObject;

                        var mapOptions = {
                            zoom: 15,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };


                        mapObject = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                        navigator.geolocation.getCurrentPosition(function (position) {

                            var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);



                            // alert(position.coords.latitude);
                            var infowindow = new google.maps.InfoWindow({
                                map: mapObject,
                                position: geolocate,
                                content:
                                        '<h2 style="color:gray;">Appointment Not Found</h2>'
                            });
                            mapObject.setCenter(geolocate);


                        });

                    } else {
                        document.getElementById('google_canvas').innerHTML = 'No Geolocation Support.';
                    }

<?php } ?>
//**************************************************************

            }

            function handleNoGeolocation(errorFlag) {
                if (errorFlag) {
                    var content = 'Error: The Geolocation service failed.';
                } else {
                    var content = 'Error: Your browser doesn\'t support geolocation.';
                }

                var options = {
                    map: map,
                    position: new google.maps.LatLng(60, 105),
                    content: content
                };
                var infowindow = new google.maps.InfoWindow(options);
                map.setCenter(options.position);
            }

        </script>   
    </head>
    <body style="margin: 0;">

        <div class="mainpage">
            <div class="head">

                <div style="padding-left:20px;float:left;">
                    <span><img src="images/roadyo_logo.png" width="50" height="50"/></span>

                </div>


                <div style=" background: url(images/roadyo_logo.png) no-repeat; background-size: 109px 18px; background-position: center 35%; height: 50px;background: #fff;background: #2a2a2a;">

                </div>
            </div>



            <div class="mapslide">    
                <div id="map-canvas"></div>
            </div>
            <?php
            if ($statusresult == 5 || $statusresult == 6 || $statusresult == 7 || $statusresult == 8) {
                ?>
                <div id="driver" class="sidebar1">

                    <div class="driver_pic">
                        <img src="../pics/hdpi/<?php echo $driver_pic; ?> " width="90" height="90" />
                    </div>
                    <div class="driver_details">

                        <div class="row-index">
                            <div class="row-lable-lacation">NAME</div>
                            <div class="row-value-lacation"><?php echo $driver_name; ?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row-index">
                            <div class="row-lable-lacation">EMAIL</div>
                            <div class="row-value-lacation"><?php echo $driver_email; ?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row-index">
                            <div class="row-lable-lacation">PHONE</div>
                            <div class="row-value-lacation"> <?php echo $dph; ?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row-index">
                            <div class="row-lable-lacation">PLATE NO</div>
                            <div class="row-value-lacation"> <?php echo $plateno; ?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row-index">
                            <div class="row-lable-lacation">TYPE</div>
                            <div class="row-value-lacation"><?php echo $typename; ?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row-index">
                            <div class="row-lable-lacation"> COLOR</div>
                            <div class="row-value-lacation"> <?php echo $color; ?></div>
                            <div class="clear"></div>
                        </div>

                    </div>

                    <div class="clear"></div>
                </div>
            <?php }
            ?> 

        </div>


    </body>

</html>
