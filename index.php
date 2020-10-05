<!DOCTYPE html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>POC - Geolocation API</title>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js" integrity="sha512-efUTj3HdSPwWJ9gjfGR71X9cvsrthIA78/Fvd/IN+fttQVy7XWkOAXb295j8B3cmm/kFKVxjiNYzKw9IQJHIuQ==" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
  <style>
    /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
    #map {
      height: 100%;
    }

    /* Optional: Makes the sample page fill the window. */
    html,
    body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    #register {
      position: fixed;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);

      -webkit-transition: all 0.5s ease-in-out;
      -moz-transition: all 0.5s ease-in-out;
      -o-transition: all 0.5s ease-in-out;
      -ms-transition: all 0.5s ease-in-out;
      transition: all 0.5s ease-in-out;

      -webkit-transform: translate(0px, -100%) scale(0, 0);
      -moz-transform: translate(0px, -100%) scale(0, 0);
      -o-transform: translate(0px, -100%) scale(0, 0);
      -ms-transform: translate(0px, -100%) scale(0, 0);
      transform: translate(0px, -100%) scale(0, 0);

      opacity: 0;
    }

    #register.open {
      -webkit-transform: translate(0px, 0px) scale(1, 1);
      -moz-transform: translate(0px, 0px) scale(1, 1);
      -o-transform: translate(0px, 0px) scale(1, 1);
      -ms-transform: translate(0px, 0px) scale(1, 1);
      transform: translate(0px, 0px) scale(1, 1);
      opacity: 1;
    }

    #register input[type="register"] {
      position: absolute;
      top: 40%;
      width: 100%;
      color: rgb(255, 255, 255);
      background: rgba(0, 0, 0, 0);
      font-size: calc(2em + 1vw);
      font-weight: 300;
      text-align: center;
      border: 0px;
      outline: none;
    }

    #register .btn {
      position: absolute;
      top: 60%;
      left: 50%;
      margin-left: -90px;
    }

    #register .close {
      position: absolute;
      top: 20px;
      right: 45px;
      font-size: calc(2em + 1vw);
      cursor: pointer;
      color: white;
    }

    #popup_content_wrap {
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      position: fixed;
      background: rgba(0, 0, 0, 0.74);
      z-index: 9999999;
    }

    #popup_content {
      width: 50%;
      height: 300px;
      padding: 20px;
      position: relative;
      top: 15%;
      left: 25%;
      background: #1b100ed9;
      border: 8px solid #cccccc;
    }
  </style>
</head>

<html>

<body>

  <div id="map"></div>

  <div id="register">
    <span class="close" title="Saltear registro"><i class="fa fa-times"></i></span>
    <form>
      <input id="name" type="register" value="" placeholder="Ingresa tu nombre aquí" />
      <button type="button" onclick="saveMarker();" class="btn btn-primary">Registrar mi ubicacion</button>
    </form>
  </div>

  <script>
    let map;
    let infoWindow;
    let pos;

    function initMap() {
      // Iniciar mapa
      map = new google.maps.Map(document.getElementById('map'), {
        center: {
          lat: -38.0071684,
          lng: -57.5465526
        },
        zoom: 13
      });
      // Iniciar ventana de información
      infoWindow = new google.maps.InfoWindow;

      // Cargar los marcadores guardados
      downloadUrl('getmarkers.php', function(data) {
        var markers = JSON.parse(data.responseText);
        Array.prototype.forEach.call(markers, function(markerElem) {
          let displayInfo = markerElem.name === getCookie("name");
          createMarker(markerElem.name, {
            lat: markerElem.lat,
            lng: markerElem.lng,
            accuracy: markerElem.accuracy
          }, markerElem.time, displayInfo);
        });
      });

      // Geolocalización
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          console.log(position);
          pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: position.coords.accuracy
          };
          map.setCenter(pos);
        }, function() {
          handleLocationError(true, infoWindow, map.getCenter());
        });
      } else {
        handleLocationError(false, infoWindow, map.getCenter());
      }
    }

    function downloadUrl(url, callback) {
      var request;
      if (window.XMLHttpRequest) {
        request = new XMLHttpRequest(); // IE7+, Firefox, Chrome, Opera, Safari
      } else {
        request = new ActiveXObject("Microsoft.XMLHTTP"); // IE6, IE5
      }
      request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
          callback(request);
        }
      }
      request.open("GET", url, true);
      request.send();
    }

    function doNothing() {}

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
      infoWindow.setPosition(pos);
      infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
      infoWindow.open(map);
    }

    $(document).ready(function() {
      let name = getCookie("name");
      if (name.length == 0) {
        $('#register').addClass('open');
        $('#register > form > input[type="register"]').focus();
      } else {
        $.notify(`${name}, tu geolocalización fue cargada correctamente.`, "info");
      }
    });

    $('#register, #register span.close').on('click keyup', function(event) {
      if (event.target == this || event.target.className == 'fa fa-times' || event.keyCode == 27) {
        $(this).removeClass('open');
      }
    });

    $('form').submit(function(event) {
      event.preventDefault();
      return false;
    });

    function saveMarker() {
      let name = $("#name").val();
      if (name.length == 0) {
        $("#name").notify("Ingresa un nombre para registrarte.", {
          position: "top center"
        }, "error");
        return;
      }

      $.ajax({
        type: "POST",
        url: "savemarker.php",
        data: {
          name: name,
          lat: pos.lat,
          lng: pos.lng,
          accuracy: pos.accuracy
        },
        success: function(response) {
          $.notify(`Bienvenido/a ${name}, tu geolocalización fue guardada correctamente.`, "success");
          setCookie('name',name,365);
          $('#register').removeClass('open');
          createMarker(name, pos, null, true);
        }
      });
    }

    function createMarker(name, pos, time, displayInfo) {
      let point = new google.maps.LatLng(
        parseFloat(pos.lat),
        parseFloat(pos.lng));
      let date = time == null ? new Date() : new Date(time);

      var infowincontent = document.createElement('div');
      var strong = document.createElement('strong');
      strong.textContent = name;
      infowincontent.appendChild(strong);
      infowincontent.appendChild(document.createElement('br'));

      var divDescription = document.createElement('div');
      divDescription.innerHTML = `Coordenadas: ${pos.lat},${pos.lng}<br>Exactitud: ${Math.round(pos.accuracy)} metros<br>Creado: ${date.toLocaleString()}`
      infowincontent.appendChild(divDescription);

      let marker = new google.maps.Marker({
        map: map,
        position: point
      });
      marker.addListener('click', function() {
        infoWindow.setContent(infowincontent);
        infoWindow.open(map, marker);
      });
      if (displayInfo) new google.maps.event.trigger(marker, 'click');
    }

    function setCookie(cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
      var expires = "expires=" + d.toUTCString();
      document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
      var name = cname + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var ca = decodedCookie.split(';');
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }
  </script>
  <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRgK3LhRQrlxsm1xrPNwdtW-akcbhps08&callback=initMap">
  </script>
</body>

</html>