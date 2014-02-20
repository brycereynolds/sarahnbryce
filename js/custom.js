$(document).ready(function() {
    var urlCode = 'none';

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
        $('.flex-direction-nav').addClass('hide-for-small-only');
    }

    flexSlider();

    /*-----------------------------------------------------------------------------------*/
    /*  RSVP Form Validation + Submission
    /*-----------------------------------------------------------------------------------*/


    // this is the id of the form
    var formID = $("#js-form"),
        formSubmitted = false;

    // on any input blur we want to update db data
    $('.save_on_change').change(function(){
        if(formSubmitted) return false;

        var formData = formID.serialize();
        formData += '&code=' + urlCode;
        formData += '&submission=false';

        console.log("SAVE ON CHANGE", formData);

        $.ajax({
            url:        "mailer.php",
            type:       "post",
            dataType:   "json",
            data:       formData,
            success: function(data) {
                console.log("data", data, arguments);
                if(data && data.status == 'ok'){
                    document.submissionForm.response_id.value = data.response_id;
                }
            }
        });

        return false; // avoid to execute the actual submit of the form.
    });

    // submits form with ajax method
    formID.on("submit", function() {
        var $fr = $('.form_results');
        $fr.hide().removeClass('error').removeClass('success');

        var formData = formID.serialize();
        formData += '&code=' + urlCode;
        formData += '&submission=true';

        console.log("FORM DATA", formData);

        // _gaq.push(['_trackEvent', 'Form', 'Submission', $('#firstname').val() + ' ' + $('#lastname').val()]);

        $.ajax({
            url:        "mailer.php",
            type:       "post",
            dataType:   "json",
            data:       formData,

            success: function(data) {
                $('#js-submit-btn').fadeOut();
                formSubmitted = true;

                console.log("data", data, arguments);

                if(data && data.status == 'ok'){
                    var wedding_rsvp = $('input:radio[name=wedding_rsvp]:checked').val(),
                        montana_rsvp = $('input:radio[name=montana_rsvp]:checked').val();

                    if(wedding_rsvp == 'decline' && montana_rsvp == 'decline'){

                        $fr.html('We are sorry you can not make it but appreciate your response. Thanks!');

                    }else if(wedding_rsvp == 'accept' && montana_rsvp == 'accept'){

                        $fr.html('Woohoo we are happy you\'re coming!<br />Expect a formal invitation in the mail as well as more details about the Montana trip!');

                    }else if(wedding_rsvp == 'accept'){

                        $fr.html('Woohoo we are happy you\'re coming! Expect a formal invitation in the mail soon.<br />If you change your mind about Montana let us know!');

                    }else{

                        $fr.html('We\'re sad you can\'t make the California trip but are still really excited to see you in Montana!<br />We will be sending out more details about that trip soon. Thanks!');

                    }

                    $fr.addClass('success');

                }else{

                    if(data.msg){
                        $fr.html(data.msg + '<br />If you still have trouble please send us an email instead using the link below. Thanks!');
                    }else{
                        $fr.html('Hmm looks like we have a problem on our end. Bryce probably screwed something up :)<br />Please send us an email instead using the link below. Thanks!');
                    }

                    $fr.addClass('error');

                }

                $fr.slideDown();
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
        layout: '<span class="number">{dn}</span> {dl} <span class="number">{hn}</span> {hl} <span class="number">{mn}</span> MIN <span class="number">{sn}</span> SEC'
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

    function addAdult(e){
        e && e.preventDefault();
        $('.clear-guests.hide').addClass('display').removeClass('hide');
        $('.guest-list').append(_.template($('#guest-tmpl').html())).show();
    }

    function addChild(e){
        e && e.preventDefault();
        $('.clear-guests.hide').addClass('display').removeClass('hide');
        $('.guest-list').append(_.template($('#guest-child-tmpl').html())).show();
    }

    function clearGuests(e){
        e && e.preventDefault();
        $('.guest-list').fadeOut(300, function(){
            $('.guest-list').html('');
        });

        $('.clear-guests').addClass('hide').removeClass('display');
    }



    // Events
    $('.add-adult').on('click',addAdult);
    $('.add-child').on('click', addChild);
    $('.clear-guests').on('click', clearGuests);

    $('#firstname').blur(function(){
        //_gaq.push(['_trackEvent', 'Form', 'First Name - Blur', $('#firstname').val()]);
    });

    // (none) : no add guest option
    // sago : single add guest only (classic +1)
    // sccc : add guest(s) and children (wording will imply guests are meant to be a +1)

    var params = getURLParameters(location.href),
        show = params.code ? params.code : false,
        guestText = null;

    urlCode = show ? show : urlCode;

    if(show == 'sago'){

        guestText = 'Will you be bringing a plus-one?'
        $('.add-child').remove();
        $('.add-adult').remove();
        $('.clear-guests').remove();
        addAdult(null);

    }else if(show == 'sccc'){

        guestText = 'Will you be bringing a plus-one and your children?'

    }

    $('.rsvp-guests legend').text(guestText);

    if(show == 'sago' || show == 'sccc'){

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