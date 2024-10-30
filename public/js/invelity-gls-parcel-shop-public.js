window.custom = window.custom || {};

if ('undefined' == typeof window.jQuery) {
    console.error('Plugin Invelity GLS ParcelShop potrebuje jQuery');
}

(function ($, custom) {
    'use strict';

    custom = custom || {};

    custom.map = {};
    custom.map.mapObject;
    custom.map.init = function (options) {
        custom.map.mapObject = L.map(options.container).setView(options.position, 15);


        var iconStyle = new L.icon({
            iconUrl: inv_globals.pluginUrl + '/../img/map/' + 'marker-balikomat.png',
            iconSize: [35, 51],
            iconAnchor: [18, 48],
        });

        var iconStyleParcel = new L.icon({
            iconUrl: inv_globals.pluginUrl + '/../img/map/' + 'marker-parcel.png',
            iconSize: [35, 51],
            iconAnchor: [18, 48],
        });


        var vendors = options.marker;
        const setCoordinate = (gps_lat, gps_long) => {
            custom.map.mapObject.setView([gps_lat, gps_long], 15);
        }

        for (let index = 0; index < vendors.length; index++) {
            var markerIcon = vendors[index].status == 'balikomat' ? iconStyle : iconStyleParcel;
            L.marker([vendors[index].lat, vendors[index].long], {
                icon: markerIcon
            }).bindPopup(vendors[index].name + '<br>' +  vendors[index].address)
                .on('click', selectShop, vendors[index].id)
                .addTo(custom.map.mapObject);
        }

        if (options.style) {
            L.tileLayer.provider(options.style).addTo(custom.map.mapObject);
        } else {
            L.tileLayer.provider('CartoDB.Voyager').addTo(custom.map.mapObject);
        }
    }

    function selectShop() {
        $('.inv_gls_parcelshop_list_shop').css('display', 'none');
        $('.inv_gls_parcelshop_list_shop[data-shopid="' + this + '"]').css('display', 'block').addClass('active');
        $('.all').css('display', 'block');
    }


    $(document).ready(function () {


        $(document).on('change', '#billing_country', function () {
            if (getInvCookie('inv_gls_picked_shop')) {
                var saved_cookie = getInvCookie('inv_gls_picked_shop');
                saved_cookie = saved_cookie.split('|');
                var checkExist = setInterval(function() {
                    if ($('[name="inv_gls_picked_shop_id"]').length) {
                        document.getElementsByName('inv_gls_picked_shop_id')[0].value = saved_cookie[0];
                        document.getElementsByName('inv_gls_picked_shop_name')[0].value = saved_cookie[1];
                        document.getElementsByName('inv_gls_picked_shop_address')[0].value = saved_cookie[2];
                        document.getElementById('inv_gls_parcel_shop_picked_shop_name').innerHTML = 'Vybraná pobočka :<br>' + saved_cookie[1] + ' ' + saved_cookie[2];
                        clearInterval(checkExist);
                    }
                }, 100);
            }
        });
        $(document).on('input', '#billing_address_1_field', function () {
            if (getInvCookie('inv_gls_picked_shop')) {
                var saved_cookie = getInvCookie('inv_gls_picked_shop');
                saved_cookie = saved_cookie.split('|');
                var checkExist = setInterval(function() {
                    if ($('[name="inv_gls_picked_shop_id"]').length) {
                        document.getElementsByName('inv_gls_picked_shop_id')[0].value = saved_cookie[0];
                        document.getElementsByName('inv_gls_picked_shop_name')[0].value = saved_cookie[1];
                        document.getElementsByName('inv_gls_picked_shop_address')[0].value = saved_cookie[2];
                        document.getElementById('inv_gls_parcel_shop_picked_shop_name').innerHTML = 'Vybraná pobočka :<br>' + saved_cookie[1] + ' ' + saved_cookie[2];
                        clearInterval(checkExist);
                    }
                }, 100);
            }
        });

        $(document).on('input', '#billing_postcode_field', function () {
            if (getInvCookie('inv_gls_picked_shop')) {
                var saved_cookie = getInvCookie('inv_gls_picked_shop');
                saved_cookie = saved_cookie.split('|');
                var checkExist = setInterval(function() {
                    if ($('[name="inv_gls_picked_shop_id"]').length) {
                        document.getElementsByName('inv_gls_picked_shop_id')[0].value = saved_cookie[0];
                        document.getElementsByName('inv_gls_picked_shop_name')[0].value = saved_cookie[1];
                        document.getElementsByName('inv_gls_picked_shop_address')[0].value = saved_cookie[2];
                        document.getElementById('inv_gls_parcel_shop_picked_shop_name').innerHTML = 'Vybraná pobočka :<br>' + saved_cookie[1] + ' ' + saved_cookie[2];
                        clearInterval(checkExist);
                    }
                }, 100);
            }
        });


        $(document).on('input', '#billing_city_field', function () {
            if (getInvCookie('inv_gls_picked_shop')) {
                var saved_cookie = getInvCookie('inv_gls_picked_shop');
                saved_cookie = saved_cookie.split('|');
                var checkExist = setInterval(function() {
                    if ($('[name="inv_gls_picked_shop_id"]').length) {
                        document.getElementsByName('inv_gls_picked_shop_id')[0].value = saved_cookie[0];
                        document.getElementsByName('inv_gls_picked_shop_name')[0].value = saved_cookie[1];
                        document.getElementsByName('inv_gls_picked_shop_address')[0].value = saved_cookie[2];
                        document.getElementById('inv_gls_parcel_shop_picked_shop_name').innerHTML = 'Vybraná pobočka :<br>' + saved_cookie[1] + ' ' + saved_cookie[2];
                        clearInterval(checkExist);
                    }
                }, 100);
            }
        });



        $(document).on('click', '#gls_parcel_shop_map_init', function () {
            mapPopUpInit(geoFindMe);
        });

          $(document).on('click', '.all', function () {
              $('.inv_gls_parcelshop_list_shop').css('display', 'block');
              $('.all').css('display', 'none');
        });

        $(document).on('click', '.inv_gls_parcelshop_list_shop', function () {
            var latitude = $(this).data('lat');
            var longitude = $(this).data('long');
            custom.map.mapObject.panTo(new L.LatLng(latitude, longitude));

        });

        $(document).on('click', '#close-invelity-gls-parcel-shop-modal', function () {
            mapPopUpClose();
        });


        $(document).on('click', '.inv_gls_parcelshop_list_shop', function () {
            $(this).toggleClass('active').siblings().removeClass('active');
        });

        $(document).on('click', '.inv_gls_parcelshop_pick_shop', function () {
            var picked = $(this).closest(".inv_gls_parcelshop_list_shop").data('shopid');
            var pickedName = $(this).closest(".inv_gls_parcelshop_list_shop").data('name');
            var pickedAddress = $(this).closest(".inv_gls_parcelshop_list_shop").data('address');
            $('input[name="inv_gls_picked_shop"]').value = '';
            document.getElementsByName('inv_gls_picked_shop_id')[0].value = picked;
            document.getElementsByName('inv_gls_picked_shop_name')[0].value = pickedName;
            document.getElementsByName('inv_gls_picked_shop_address')[0].value = pickedAddress;
            document.getElementById('inv_gls_parcel_shop_picked_shop_name').innerHTML = 'Vybraná pobočka :<br>' + pickedName + ' ' + pickedAddress;

            mapPopUpClose();
            var saveCookie = picked + "|" + pickedName + "|" + pickedAddress;
            document.cookie = "inv_gls_picked_shop=" + saveCookie;
        });


        $(document).on('keydown', '#inv_gls_parcelshop_find', function (e) {
            var value = $(this).val();
            if (value.length >= 3) {
                searchParcelShop();
            } else if (e.which === 13 || e.keyCode === 13) {
                searchParcelShop();
            }

        });

        $(document).on('click', '.checkbox-custom-label', function (e) {
            var checkBox = document.getElementById("inv_gls_geolocation");

            console.log(custom.map.mapObject);
            if (checkBox.checked == true) {
                geoFindMe();
            } else {
                console.log('annonymous');
            }

        });


    })


    function mapPopUpClose() {
        $('body').css('overflow', 'auto');
        $('html').css('overflow-x', 'auto');
        $('#inv_gls_map_init_container').removeClass('show');
    }



    function mapPopUpInit(calback) {

        $.ajax({
            type: 'POST',
            data:{  action: 'invGlsParcelShopOpenMap'},
            url: inv_globals.ajax_url,
            cache: false,
            success: function (result) {
                $('body').css('overflow', 'hidden');
                $('html').css('overflow-x', 'inherit');
                $("#inv_gls_map_init_container").html(result);
                $('#inv_gls_map_init_container').addClass('show');

                initMap([48.148111, 17.104988]);

                calback();
            }
        });


    }

    function initMap(position) {
        var glsMap = $('#map');
        var data = [];
        $('.inv_gls_parcelshop_list_shop').each(function () {

            var shop = {
                lat: $(this).data('lat'),
                long: $(this).data('long'),
                name: $(this).data('name'),
                id: $(this).data('shopid'),
                status: $(this).data('status'),
                address: $(this).data('address'),
            };
            data.push(shop);

        });

        if (glsMap.length) {
            var options = {
                container: 'map',
                position: position,
                marker: [],
            };

            for (let index = 0; index < data.length; index++) {
                options.marker.push(data[index])
            }

            custom.map.init(options);
        }
    }

    function searchParcelShop() {

        let i;
        let input = document.getElementById('inv_gls_parcelshop_find');
        let filter = input.value.toUpperCase();
        let ul = document.getElementById("inv_gls_parcelshop_list_shops");
        let li = ul.querySelectorAll('li:not(.all)');
        let setNewMap = 0;
        // Loop through all list items, and hide those who don't match the search query
        for (i = 0; i < li.length; i++) {
            let a = li[i].getElementsByTagName("h6")[0];
            let txtValue = a.textContent || a.innerText;
            let p = li[i].getElementsByTagName("p")[0];
            let descValue = p.textContent || p.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1 || accentFold(descValue).toUpperCase().indexOf(filter) >= 0)  {

                if( setNewMap === 0) {
                    let findTownLat = li[i].dataset.lat;
                    let findTownLong = li[i].dataset.long;
                    custom.map.mapObject.panTo(new L.LatLng(findTownLat, findTownLong));
                    setNewMap = 1;
                }
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }

    }

    function accentFold(inStr) {
        return inStr.replace(
            /([àáâãäå])|([çčć])|([èéêë])|([ìíîï])|([ľ])|([ň])|([òóôõöø])|([ßš])|([ť])|([ùúûü])|([ÿ])|([ž])|([æ])/g,
            function (str, a, c, e, i, l, n, o, s,t, u, y,z, ae) {
                if (a) return 'a';
                if (c) return 'c';
                if (e) return 'e';
                if (i) return 'i';
                if (l) return 'l';
                if (n) return 'n';
                if (o) return 'o';
                if (s) return 's';
                if (t) return 't';
                if (u) return 'u';
                if (y) return 'y';
                if (z) return 'z';
                if (ae) return 'ae';
            }
        );
    }

    function geoFindMe() {

        function success(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            $('.inv_gls_parcelshop_map').data('lat', latitude);
            $('.inv_gls_parcelshop_map').data('lon', longitude);

            custom.map.mapObject.panTo(new L.LatLng(latitude, longitude));

        }

        function error() {
            console.log('Unable to retrieve your location');
        }

        if (!navigator.geolocation) {
            console.log('Geolocation is not supported by your browser');
        } else {
            console.log('Locating...');
            navigator.geolocation.getCurrentPosition(success, error);
        }

    }

    function getInvCookie(cname) {
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


})(jQuery);