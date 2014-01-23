<?php
/*
  Plugin Name: KVS Player
  Author: CyberSEO.net
  Version: 2.1
  Author URI: http://www.cyberseo.net/
  Plugin URI: http://www.cyberseo.net/kvs-flv-player/
  Description: The KVS Player plugin allows one to easily embed videos (FLV, F4V and MP4 files) into WordPress blogs.
 */

if (!function_exists("get_option") || !function_exists("add_filter")) {
    die();
}

define('KVS_PLAYER_DEFAULT_PLAYER_SWF', plugins_url('/kt_player/kt_player.swf', __FILE__));
define('KVS_PLAYER_DEFAULT_PLAYER_JS', plugins_url('/kt_player/kt_player.js', __FILE__));
define('KVS_PLAYER_OPTIONS', 'kvs_player_options');

$kvs_player_default_options = array(
    'height' => '480',
    'width' => '854',
    'hide_controlbar' => '1',
    'hide_style' => 'fade',
    'autoplay' => '0',
    'skin' => '',
    'preview_url' => '',
    'scaling' => '',
    'autoplay' => '0',
    'embed' => '',
    'permalink' => '',
    'sec' => '',
    'bt' => '5',
    'video_click_url' => '',
    'urls_in_same_window' => '',
    'flv_stream' => ''
);

$kvs_players = 0;

$kvs_player_options = get_option(KVS_PLAYER_OPTIONS, array());
foreach ($kvs_player_default_options as $item => $value) {
    if (!isset($kvs_player_options[$item])) {
        $kvs_player_options[$item] = $value;
    };
}

if (is_admin() && isset($_POST['save_changes'])) {
    unset($_POST['save_changes']);
    foreach ($_POST as $option => $value) {
        $kvs_player_options[$option] = $value;
    }
    update_option(KVS_PLAYER_OPTIONS, $kvs_player_options);
}

function kvs_player_general_options_menu() {
    global $kvs_player_options;
    ?>

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <div class="wrap">

        <div id="ad" class="updated">
            <h3>Upgrade to KVS Player Pro</h3>
            <h4>Key Features:</h4>
            <ul style="list-style: circle;margin-left:20px;">
                <li>Advertising on initialization (HTML layer displayed on top of player).</li>
                <li>Pre-roll and Post-roll advertising (HTML / video / image) with limitable duration and customizable click URL.</li>
                <li>On-pause and On-stop advertising (HTML / image) with customizable click URL.</li>
                <li>Customizable click URL for the video display area.</li>
                <li>Advertising image on player mouse over.</li>
                <li>As many flash floating banners as you need. Banners can appear from 4 different sides.</li>
                <li>A short text and click URL can be displayed on the player control bar.</li>
            </ul>                                
            <p><a href="http://www.cyberseo.net/kvs-player-pro/" target="_blank"><strong>Click here for more info and demo.</strong></a></p>
        </div>

        <div class="metabox-holder postbox-container xpinner-settings">
            <form name="general_options_menu" action="" method="post">
                <div class="section" id="player_configurator_visual" style="display: block">
                    <h1>KVS Player Options</h1>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="width" class="required">Player size (*):</label>
                            </th>
                            <td>
                                <input type="text" id="width" name="width" value="<?php echo $kvs_player_options['width']; ?>" size="10"/>
                                x
                                <input type="text" id="height" name="height" value="<?php echo $kvs_player_options['height']; ?>" size="10"/>
                                <input type="button" class="button" value="100%" onclick="$('#width, #height').val('100%')"/>
                                <p class="description">specify player size in pixels or 100% to force player be sized dynamically based on the container</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="hide_controlbar">Toolbar behavior:</label>
                            </th>
                            <td>
                                <select id="hide_controlbar" name="hide_controlbar">
                                    <option value=""<?php echo (($kvs_player_options['hide_controlbar'] == '') ? ' selected' : ''); ?>>Show always</option>
                                    <option value="1"<?php echo (($kvs_player_options['hide_controlbar'] == '1') ? ' selected' : ''); ?>>Autohide</option>
                                    <option value="2"><?php echo (($kvs_player_options['hide_controlbar'] == '2') ? ' selected' : ''); ?>Never Show</option>
                                </select>
                                <p class="description">configures how toolbar behaves in preview mode; doesn't affect fullscreen</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="hide_style">Toolbar autohide style:</label>
                            </th>
                            <td>
                                <select id="hide_style" name="hide_style">
                                    <option value=""<?php echo (($kvs_player_options['hide_style'] == '') ? ' selected' : ''); ?>>Move out</option>
                                    <option value="fade"<?php echo (($kvs_player_options['hide_style'] == 'fade') ? ' selected' : ''); ?>>Fade out</option>
                                </select>
                                <p class="description">configures how toolbar is being hidden in fullscreen and in preview mode when autohide is selected</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="skin">Skin:</label>
                            </th>
                            <td>
                                <select id="skin" name="skin">
                                    <option value=""<?php echo (($kvs_player_options['skin'] == '') ? ' selected' : ''); ?>>Black (default)</option>
                                    <option value="2"<?php echo (($kvs_player_options['skin'] == '2') ? ' selected' : ''); ?>>White</option>
                                </select>
                                <p class="description">select one of the available player skins</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="preview_url">Poster image:</label>
                            </th>
                            <td>
                                <input type="text" id="preview_url" name="preview_url" value="<?php echo $kvs_player_options['preview_url']; ?>" size="40"/>
                                <input type="button" class="button" value="Example" onclick="$('#preview_url').val('<?php echo plugins_url('/images/poster.jpg', __FILE__); ?>')"/>
                                <p class="description">poster image should be hosted on the same domain or should not have hotlink protection in case of crossdomain</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="skin">Video proportions:</label>
                            </th>
                            <td>
                                <select id="scaling" name="scaling">
                                    <option value=""<?php echo (($kvs_player_options['scaling'] == '') ? ' selected' : ''); ?>>Keep video proportions</option>
                                    <option value="fill"<?php echo (($kvs_player_options['scaling'] == 'fill') ? ' selected' : ''); ?>>Scale to fit player size</option>
                                    <option value="crop"<?php echo (($kvs_player_options['scaling'] == 'crop') ? ' selected' : ''); ?>>Crop to fit player size</option>
                                </select>
                                <p class="description">keeping video proportions may result in black bars; scaling video to fit player size may distort original video proportions; cropping video to fit player size may crop edges to adjust video</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label>Autoplay:</label>
                            </th>
                            <td>
                                <select id="autoplay" name="autoplay">
                                    <option value="0"<?php echo (($kvs_player_options['autoplay'] == '') ? ' selected' : ''); ?>>Disabled</option>
                                    <option value="1"<?php echo (($kvs_player_options['autoplay'] == '1') ? ' selected' : ''); ?>>Enabled for single posts and pages only</option>
                                    <option value="2"<?php echo (($kvs_player_options['autoplay'] == '2') ? ' selected' : ''); ?>>Enabled anywhere, including the main page</option>
                                </select>
                                <label for="autoplay">enable/disable autoplay</label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label>Embed code:</label>
                            </th>
                            <td>
                                <input type="hidden" name="embed" value="">
                                <input type="checkbox" id="embed" name="embed" value="1"<?php if ($kvs_player_options['embed'] == '1') echo " checked"; ?>/>
                                <label for="embed">enable embed code</label>
                                <p class="description">if enabled, player will allow to get embed code</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="permalink">Permalink:</label>
                            </th>
                            <td>
                                <input type="hidden" name="permalink" value="">
                                <input type="checkbox" id="permalink" name="permalink" value="1"<?php if ($kvs_player_options['permalink'] == '1') echo " checked"; ?>/>
                                <label for="embed">enable permalink URL</label>
                                <p class="description">if provided, player will allow to get permalink of the current post or page</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sec">Limit video playback:</label>
                            </th>
                            <td>
                                <input type="text" id="sec" name="sec" value="<?php echo $kvs_player_options['sec']; ?>" size="10"/>
                                <p class="description">specify number of seconds to limit video playback</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="bt">Buffer time:</label>
                            </th>
                            <td>
                                <input type="text" id="bt" name="bt" value="<?php echo $kvs_player_options['bt']; ?>" size="10"/>
                                <p class="description">specify length of buffer in seconds; player will start playing after its buffer is fully pre-loaded</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label>Disable streaming:</label>
                            </th>
                            <td>
                                <input type="hidden" name="flv_stream" value="">
                                <input type="checkbox" id="flv_stream" name="flv_stream" value="false"<?php if ($kvs_player_options['flv_stream'] == 'false') echo " checked"; ?>/>
                                <label for="flv_stream">server does not support streaming / seeking</label>
                                <p class="description">disable streaming if your server does not support streaming / seeking</p>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <input type="submit" name="save_changes" class="button-primary" value="Save Changes" />
                </div>
            </form>
        </div>     
    </div>
    <?php
}

function KVSFlvInsert($string) {
    global $kvs_player_options, $kvs_players;

    @list($url, $preview_url, $width, $height, $video_click_url, $player) = preg_split('/\s+/u', $string);

    if (!isset($preview_url) || $preview_url == '#') {
        $preview_url = $kvs_player_options['preview_url'];
    }

    if (!isset($width) || $width == '0') {
        $width = $kvs_player_options['width'];
    }

    if (!isset($height) || $height == '0') {
        $height = $kvs_player_options['height'];
    }

    if (!isset($video_click_url) || $video_click_url == '#') {
        $video_click_url = $kvs_player_options['video_click_url'];
    } else {
        $video_click_url = html_entity_decode($video_click_url);
    }

    if (!isset($player) || $player == '#') {
        $player = KVS_PLAYER_DEFAULT_PLAYER_SWF;
    }

    $postfix = ($kvs_players) ? '_' . $kvs_players : '';

    $code = '';

    if (!$kvs_players) {
        $code .= "<script type=\"text/javascript\" src=\"" . KVS_PLAYER_DEFAULT_PLAYER_JS . "\"></script>\n";
    }

    $code .= "<div id=\"kt_player" . $postfix . "\" style=\"visibility: hidden\">\n";
    $code .= "    <a href=\"http://adobe.com/go/getflashplayer\">This page requires Adobe Flash Player</a>\n";
    $code .= "</div>\n";
    $code .= "<script type=\"text/javascript\">\n";
    $code .= "    var flashvars = {\n";

    $code .= "        video_url: '" . $url . "',\n";

    if ($kvs_player_options['hide_controlbar'] != '') {
        $code .= "        hide_controlbar: '" . $kvs_player_options['hide_controlbar'] . "',\n";
    }

    if ($kvs_player_options['hide_style'] != '') {
        $code .= "        hide_style: '" . $kvs_player_options['hide_style'] . "',\n";
    }

    if ($preview_url != '') {
        $code .= "        preview_url: '" . $preview_url . "',\n";
    }

    if ($kvs_player_options['embed'] != '') {
        $code .= "        embed: '" . $kvs_player_options['embed'] . "',\n";
    }

    if ($kvs_player_options['permalink'] != '') {
        $code .= "        permalink_url: '" . get_permalink() . "',\n";
    }

    if ($kvs_player_options['sec'] != '') {
        $code .= "        sec: '" . $kvs_player_options['sec'] . "',\n";
    }

    if ($kvs_player_options['scaling'] != '') {
        $code .= "        scaling: '" . $kvs_player_options['scaling'] . "',\n";
    }

    if ($kvs_player_options['skin'] != '') {
        $code .= "        skin: '" . $kvs_player_options['skin'] . "',\n";
    }

    if (($kvs_player_options['autoplay'] == '1' && (is_single() || is_page())) || $kvs_player_options['autoplay'] == '2') {
        $code .= "        autoplay: 'true',\n";
    }

    if ($video_click_url != '') {
        $code .= "        video_click_url: '" . $video_click_url . "',\n";
    }

    if ($kvs_player_options['urls_in_same_window'] != '') {
        $code .= "        urls_in_same_window: '" . $kvs_player_options['urls_in_same_window'] . "',\n";
    }

    if ($kvs_player_options['flv_stream'] != '') {
        $code .= "        flv_stream: '" . $kvs_player_options['flv_stream'] . "',\n";
    }

    $code .= "        bt: '" . $kvs_player_options['bt'] . "',\n";
    $code .= "    };\n";
    $code .= "    var params = {allowfullscreen: 'true', allowscriptaccess: 'always'};\n";
    $code .= "    kt_player('kt_player" . $postfix . "', '" . $player . "', '" . $width . "', '" . $height . "', flashvars, params);\n";
    $code .= "</script>\n";

    $kvs_players++;

    return $code;
}

function kvs_player_content($content) {
    $content = preg_replace("'\[flv:(.*?)\]'ie", "stripslashes(KVSFlvInsert('\\1'))", $content);
    return $content;
}

add_filter('the_content', 'kvs_player_content');
add_filter('the_excerpt', 'kvs_player_content');

function kvs_player_main_menu() {
    add_menu_page('General Options', 'KVS Player', 'manage_options', 'kvs-player', 'kvs_player_general_options_menu');
}

if (is_admin()) {
    add_action('admin_menu', 'kvs_player_main_menu');
}
?>