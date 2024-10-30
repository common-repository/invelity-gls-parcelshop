<?php


function invelityGlsParcelShopCronAction()
{
    $log = fopen(__DIR__.'/log.txt', 'a+');
    fwrite($log, date('d.m.Y H:i:s'));

    $response_data = invelityGlsParcelShopGetShops();
    if($response_data){
        invelityUpdateInvelityGlsParcelShopTable($response_data);
        fwrite($log, 'GLS Parcel Shop updated');
        fclose($log);
    }else{
        fwrite($log, 'GLS Parcel Shop not updated');
        fclose($log);
    }

}

function invelityGlsParcelShopFirstInitShops()
{
    if ( ! $_POST['method_id']) {
        return;
    }
    if ($_POST['method_id'] == 'inv_gls_parcel_shop') {
        $response_data = invelityGlsParcelShopGetShops();

        if ($response_data) {
            invelityUpdateInvelityGlsParcelShopTable($response_data);
        } else {
            if ($_POST['method_id']) {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-warning is-dismissible">
             <p>Chyba pri uploade pobočiek.</p>
         </div>';
                });
            }
        }
    }
}

function invelityGlsParcelShopGetShops()
{
    $url    = 'https://datarequester.gls-hungary.com/glsconnect/getDropoffPoints.php?ctrcode=sk';
    $remote = wp_remote_get($url, ['timeout' => 120, 'method' => 'POST', 'redirection' => 5]);

    try {
        $xmlContent = simplexml_load_string(gzdecode($remote['body']));
        if (isset($xmlContent->Data)) {
            return get_object_vars($xmlContent->Data);
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo $e->getMessage();

        return false;
    }
}

function invelityUpdateInvelityGlsParcelShopTable($response_data)
{
    global $wpdb;

    $table = $wpdb->prefix.'inv_gls_parcel_shop';
    $wpdb->delete($table, ['country' => 'SK']);

    foreach ($response_data['DropoffPoint'] as $item) {
        $data      = [];
        $openHours = [];

        $serializedOpenHours = '';
        foreach ($item->Openings as $opening) {
            foreach ($opening as $open) {
                $toArray     = (array)$open;
                $openHours[] = $toArray;
            }
            $serializedOpenHours = serialize($openHours);
        }


        foreach ($item->attributes() as $a => $b) {
            if ($a == 'ID') {
                $data['ID_PARCEL_SHOP']  = (string)$b;
                $data['GLS_PARCEL_SHOP'] = (string)$b;
            } elseif ($a == 'Name') {
                $data['NAME'] = (string)$b;
            } elseif ($a == 'Address') {
                $data['ADDRESS'] = (string)$b;
            } elseif ($a == 'CityName') {
                $data['CITY'] = (string)$b;
            } elseif ($a == 'ZipCode') {
                $data['ZIP'] = (string)$b;
            } elseif ($a == 'GeoLat') {
                $data['GEO_LAT'] = (float)$b;
            } elseif ($a == 'GeoLng') {
                $data['GEO_LONG'] = (float)$b;
            } elseif ($a == 'IsParcelLocker') {
                if ($b == 0) {
                    $data['STATUS'] = 'parcelshop';
                } else {
                    $data['STATUS'] = 'balikomat';
                }
            }
            $data['CP'] = '-';
        }

        $data['PARCEL_SHOP'] = (string)$data['ADDRESS'].' '.$data['CITY'].' '.$data['ZIP'];
        $data['COUNTRY']     = 'SK';
        $data['DATA']        = $serializedOpenHours;


        $wpdb->insert($wpdb->prefix.'inv_gls_parcel_shop', $data);
    }
}

function displayGlsParcelShops()
{
    global $wpdb;
    $table_name = $wpdb->prefix.'inv_gls_parcel_shop';
    $result     = $wpdb->get_results('SELECT * FROM '.$table_name.'');

    return $result;
}


function displayParcelShopData($data)
{
    $array = unserialize($data);
    $hours = [];

    foreach ($array as $key => $value) {
        $day   = $value['@attributes']['Day'];
        $open  = $value['@attributes']['OpenHours'];
        $break = $value['@attributes']['MidBreak'];

        $hours[$day] =
            ['OpenHours' => $open, 'MidBreak' => $break];
    }

    $day_map = [
        'Monday'    => 'Pondelok',
        'Tuesday'   => 'Utorok',
        'Wednesday' => 'Streda',
        'Thursday'  => 'Štvrtok',
        'Friday'    => 'Piatok',
        'Saturday'  => 'Sobota',
        'Sunday'    => 'Nedeľa'
    ];

    $result = [];

    foreach ($hours as $i => $array2) {
        $translatedKey          = array_key_exists($i, $day_map) ?
            $day_map[$i] :
            $i;
        $result[$translatedKey] = $array2;
    }

    $result = sortResultByDays($result, $day_map);


    return $result;
}

function sortResultByDays(array $array, array $orderArray)
{
    $ordered = [];
    foreach ($orderArray as $key) {
        if (array_key_exists($key, $array)) {
            $ordered[$key] = $array[$key];
            unset($array[$key]);
        }
    }

    return $ordered + $array;
}