<?php

declare(strict_types=1);

use Segment\Segment;

/**
 * Send JSON response.
 *
 * @param $result
 * @param $message
 * @param $code
 *
 * @return mixed
 */
function sendResponse($result, $message = null, $code = 200): mixed
{
    return Response::json([
        'data' => $result,
        'message' => $message,
    ], $code);
}

/**
 * Send Error Response In JSON.
 *
 * @param $errors
 * @param $message
 * @param $code
 *
 * @return mixed
 */
function sendErrorResponse($errors, $message = null, $code = 422): mixed
{
    return Response::json([
        'message' => $message,
        'errors' => $errors,
    ], $code);
}

/**
 * Send Error Message.
 *
 * @param $error
 * @param $code
 *
 * @return mixed
 */
function sendError($error, $code = 422): mixed
{
    return Response::json([
        'message' => $error,
        'success' => false,
    ], $code);
}

/**
 * Send Success Message.
 *
 * @param $message
 *
 * @return mixed
 */
function sendSuccess($message): mixed
{
    return Response::json([
        'success' => true,
        'message' => $message,
    ], 200);
}

/**
 * It will send track event to Segment portal.
 *
 * @param $event
 * @param $payload
 */
function sendSegmentTrackEvent($user_id, $event, $payload)
{
    try {
        Segment::init(config('segment.key'));

       /* Segment::identify(array(
            "userId" => $user_id
        ));*/

        Segment::track([
            'userId' => $user_id,
            'event' => $event,
            'properties' => $payload
        ]);
        Segment::flush();
    }catch(\Exception $e){
        info("Error while sending segment event",[
            'error' => $e->getMessage(),
            'userId' => $user_id,
            'event' => $event,
            'properties' => $payload
        ]);
    }
}

/**
 * Convert string into some format and return
 *
 * @param $string
 * @return string
 */
function formatString($string){
    $words = explode(' ', $string);
    $formattedString = '';
    foreach ($words as $index => $word) {
        if ($index === 0 || $index === count($words) - 1) {
            $formattedString .= $word . ' ';
        } else {
            $formattedString .= Str::substr($word, 0, 1) . '*** ';
        }
    }
    return trim($formattedString);
}

/**
 * @param $email
 * @return string
 */
function maskEmail($email){
    $parts = explode('@', $email);
    $username = $parts[0];
    $domain = $parts[1];

    $username = $username[0] . str_repeat('*', 3);

    $domainParts = explode('.', $domain);
    foreach ($domainParts as &$part) {
        $part = $part[0] . str_repeat('*', 3);
    }

    $formattedEmail = $username . '@' . implode('.', $domainParts);

    return $formattedEmail;
}

/**
 * @param $name
 * @return string
 */
function maskName($name){
    $words = explode(' ', $name);
    $formattedString = '';

    foreach ($words as $word) {
        $formattedString .= substr($word, 0, 1) . str_repeat('*', 3) . ' ';
    }

    return trim($formattedString);
}


function maskPhone($phone){
    $parts = explode(" ", $phone);
    $str = '';
    foreach($parts as $part){
        $firstDigit = substr($part, 0, substr($part, 0, 1) === '+' ? 2 : 1);
        $maskedValue = $firstDigit . str_repeat('*', 3);
        $str .= " " . $maskedValue;
    }

    return $str;

}


function getIconPosition($position){
    if(!$position){ return "homepage";}
    $positions = [
        'homepage' => 'homepage',
        'site' => 'footer',
        'all_products' => 'all-products',
        'selected_products' => 'selected-products',
        'cartpage' => 'cart',
        'manual' => 'manual',
    ];
    return $positions[$position];
}


function getTextFont($font){
    if(!$font){ return "Poppins"; }
    $fonts = [
        "roboto" => "Roboto",
        "poppins" => "Poppins",
        "pushster" => "Pushster",
        "liquorice" => "Liquorice",
        "licorice" => "Liquorice",
        "open sans" => "Open Sans",
        "vujahday script" => "Vujahday Script",
        "lato" => "Lato",
        "shizuru" => "Shizuru",
        "montserrat" => "Montserrat",
        "oswald" => "Oswald",
        "roboto mono" => "Roboto Mono",
        "raleway" => "Raleway",
        "playfair display" => "Playfair Display",
        "inter" => "Inter",
        "rubik" => "Rubik"
    ];
    return @$fonts[$font] ?? 'Poppins';
}


function getThemeBlockName(){
    return config('shopify-app.embed_block_name', 'app-embed');
}

function getThemeEmbedUuid(){
    $val = config('shopify-app.embed_block_id', '');
    if (empty($val)) {
        return '';
    }
    if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $val, $m)) {
        return $m[0];
    }
    return $val;
}

function getLatestObject($array) {
    $latestObject = null;
    $latestTimestamp = null;

    foreach ($array as $object) {
        $timestamp = strtotime($object->created_at);

        if ($timestamp > $latestTimestamp || $latestTimestamp === null) {
            $latestTimestamp = $timestamp;
            $latestObject = $object;
        }
    }

    return $latestObject;
}
