<?php
/*
 Plugin Name: KVS FLV Player
 Version: 1.1
 Author: CyberSEO.net
 Author URI: http://www.cyberseo.net/
 Plugin URI: http://www.cyberseo.net/kvs-flv-player/
 Description: The KVS FLV Player plugin allows one to easily embed videos (FLV, F4V and MP4 files) into WordPress blogs using the universal FLV tag style: <strong>[flv:url image width height link player]</strong>. Where <strong>url</strong> - URL of the FLV video file you want to embed; <strong>image</strong> - URL of a preview image (shown in display and playlist); <strong>width</strong> - width of an FLV video (optional parameter, default: 450); <strong>height</strong> - height of an FLV video (optional parameter, default: 317); <strong>link</strong> - URL to an external page the display, controlbar and playlist can link to (optional parameter, default: #); <strong>player</strong> - URL to FLV player (optional parameter, default: http://yourblog.com/wp-content/plugins/kvs-flv-player/kt_player.swf).
 */

if (!function_exists("get_option") || !function_exists("add_filter")) {
	die();
}

define('KVS_FLV_DEFAULT_WIDTH', '450');
define('KVS_FLV_DEFAULT_HEIGHT', '317');
define('KVS_FLV_DEFAULT_PLAYER', get_option('siteurl') . "/wp-content/plugins/" . dirname(plugin_basename(__FILE__)) . '/kt_player.swf');

function KVSFlvUrlFix($url) {
	if (stripos($url, 'http://') === false && !file_exists($url)) {
		$url = 'http://' . $url;
	}
	return $url;
}

function KVSFlvInsert($string) {
	@list($url, $thumbnail, $width, $height, $link, $player) = explode(" ", $string);
	$url = KVSFlvUrlFix($url);
	$thumbnail = KVSFlvUrlFix($thumbnail);
	if (!isset($width) || $width == "0") {
		$width = KVS_FLV_DEFAULT_WIDTH;
	}
	if (!isset($height) || $height == "0") {
		$height = KVS_FLV_DEFAULT_HEIGHT;
	}
	if (!isset($link) || $link == "#") {
		$video_click_url = "";
	} else {
		$video_click_url = "&video_click_url=" . urlencode(html_entity_decode($link));
	}
	if (!isset($player)) {
		$player = KVS_FLV_DEFAULT_PLAYER;
	}
	if (is_single() || is_page()) {
		$autoplay = 'true';
	} else {
		$autoplay = 'false';
	}
	return "\n" . '<object id="kt_player" name="kt_player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="' . $width . '" height="' . $height . '">
    <param name="allowscriptaccess" value="always"/>
    <param name="allowFullScreen" value="true"/>
    <param name="movie" value="' . $player . '" />
    <param name="flashvars" value="video_url=' . $url . '&preview_url=' . $thumbnail . '&autoplay=' . $autoplay . $video_click_url . '"/>
    <embed src="' . $player . '?video_url=' . $url . '&preview_url=' . $thumbnail . $video_click_url . '&autoplay=' . $autoplay . '" width="' . $width . '" height="' . $height . '" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>
</object>' . "\n";
}

function KVSFlvContent($content) {
	$content = preg_replace("'\[flv:(.*?)\]'ie", "stripslashes(KVSFlvInsert('\\1'))", $content);
	return $content;
}

add_filter('the_content', 'KVSFlvContent');
add_filter('the_excerpt', 'KVSFlvContent');
?>