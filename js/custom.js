$(document).ready(function() {

    /*-----------------------------------------------------------------------------------*/
    /*  Smooth Scroll
    /*  Thanks to: https://github.com/davist11/jQuery-One-Page-Nav
    /*-----------------------------------------------------------------------------------*/

    function smoothScroll(){
        $(".nav").onePageNav({
            filter: ':not(.external)',
            scrollSpeed: 1500
        });

        var formTarget = $(".js-form");

        // Scrolls to form section
        $(".js-scroll").on("click", function() {
            $('html, body').animate({
                scrollTop: formTarget.offset().top
            }, 2000);
            return false;
        });

        return false;
    }

    smoothScroll();

    /*-----------------------------------------------------------------------------------*/
    /*  Backstretch
    /*  Thanks to: http://srobbin.com/jquery-plugins/backstretch/
    /*-----------------------------------------------------------------------------------*/

    function backStrech() {
        $("aside").backstretch([
            "img/photos/P1020998.jpg",
            "img/photos/P1020949.jpg",

            ], {duration: 5000, fade: 1000});
    }

    backStrech();

    /*-----------------------------------------------------------------------------------*/
    /*  Flexslider
    /*  Thanks to: http://www.woothemes.com/flexslider/
    /*-----------------------------------------------------------------------------------*/

    function flexSlider(){
        $('.flexslider').flexslider({
            animation: "slide",
            slideshow: false,
            touch: true
        });
    }

    flexSlider();

    /*-----------------------------------------------------------------------------------*/
    /*  RSVP Form Validation + Submission
    /*-----------------------------------------------------------------------------------*/


    // this is the id of the form
    var formID = $("#js-form");

    // submits form with ajax method
    formID.on("submit", function() {

        console.log("FORM DETAILS", formID.serialize());
        return false;

        $.ajax({
            url: "mailer.php",
            type: "POST",
            data: formID.serialize(), // serializes the form's elements.

            success: function(data) {
                $(".js-display")
                            .addClass("message-panel")
                            .html(data); // show response from the php script.
            }

        });

        return false; // avoid to execute the actual submit of the form.
    });

    // Remove opacity override on side nav so it shows subtly
    setTimeout(function(){
        $('.side-nav-container').animate({'opacity': 0.3});
    }, 1500);



    // Setup countdown
    $('#countdown').countdown({
        until: new Date("May 17, 2014 16:00:00"),
        layout: '<span class="number">{dn}</span> {dl} <span class="number">{mn}</span> {ml} <span class="number">{hn}</span> {hl} <span class="number">{sn}</span> {sl}'
    });


    // URL Decoder
    function getURLParameters(url){
        var result = {};
            var searchIndex = url.indexOf("?");
            if (searchIndex == -1 ) return result;
        var sPageURL = url.substring(searchIndex +1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++){
            var sParameterName = sURLVariables[i].split('=');
            result[sParameterName[0]] = sParameterName[1];
        }
        return result;
    }

    var url = "example.com/test?code=tcba";
    var params = getURLParameters(url);

    for (var paramName in params){
            $("#params").append(paramName +" is " + params[paramName] + "</br>");
    }


    // Events
    $('.add-adult').on('click', function(e){
        e && e.preventDefault();
        $('.clear-guests.hide').addClass('display').removeClass('hide');;
        $('.guest-list').append(_.template($('#guest-tmpl').html())).show();
    });

    $('.add-child').on('click', function(e){
        e && e.preventDefault();
        $('.clear-guests.hide').addClass('display').removeClass('hide');;
        $('.guest-list').append(_.template($('#guest-child-tmpl').html())).show();
    });

    $('.clear-guests').on('click', function(e){
        e && e.preventDefault();
        $('.guest-list').fadeOut(300, function(){
            $('.guest-list').html('');
        });

        $('.clear-guests').addClass('hide').removeClass('display');
    });

    var params = getURLParameters(location.href),
        show = params.code ? params.code : false

    if(show == 'sago'){
        $('.add-child').remove();
    }

    if(show){
        // Show/Hide RSVP Menu selection on accept/decline
        $(".decline").on("click", function(){
            $(".rsvp-guests").fadeOut();
        });
        $(".accept").on("click", function(){
            $(".rsvp-guests").fadeIn();
        });
    }

});

/*-----------------------------------------------------------------------------------*/
/*  Google Map API
/*  Credit to: http://stiern.com/tutorials/adding-custom-google-maps-to-your-website/
/*-----------------------------------------------------------------------------------*/

var map;
var myLatlng = new google.maps.LatLng(36.82632, -121.35692); // Specify YOUR coordinates

var MY_MAPTYPE_ID = 'custom_style';

function initialize() {

    /*----------------------------------------------------------------------------*/
    /* Creates a custom color scheme for map
    /* For details on styling go to the link below:
    /* http://www.evoluted.net/thinktank/web-design/custom-google-maps-style-tool */
    /*----------------------------------------------------------------------------*/

    var featureOpts = [
        {
            "featureType": "road",
            "stylers": [
                { "hue": "#c500ff" },
                { "gamma": 0.82 },
                { "visibility": "on" },
                { "saturation": 62 },
                { "lightness": -7 }
            ]
        },{
            "featureType": "poi",
            "stylers": [
                { "hue": "#8800ff" },
                { "lightness": 14 }
            ]
        },{
            "stylers": [
                { "hue": "#8d00ff" }
            ]
        }
    ]

    var mapOptions = {
        zoom: 14,
        center: myLatlng,
        disableDefaultUI: true,
        scrollwheel: false,
        draggable: false,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
        },
        mapTypeId: MY_MAPTYPE_ID
    };

    map = new google.maps.Map(document.getElementById('map-canvas'),
        mapOptions);

    var styledMapOptions = {
        name: 'Custom Style'
    };

    var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);

    var image = new google.maps.MarkerImage("img/map-marker@2x.png", null, null, null, new google.maps.Size(55,57));

    // Includes custom marker on map
    var myLatLng = new google.maps.LatLng(36.82632, -121.35692);
    var beachMarker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        icon: image
    });

    map.mapTypes.set(MY_MAPTYPE_ID, customMapType);
}

google.maps.event.addDomListener(window, 'load', initialize);