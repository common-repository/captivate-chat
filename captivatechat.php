<?php
/**
 * Plugin Name: Captivate Chat
 * Description: A plugin that adds the Captivate Widget to your website.
 * Version: 0.0.4
 * Author: Captivate IO Ltd
 * Author URI: https://www.captivatechat.com
 * License: GPL2
 */

 /*  Copyright 2023 Captivate IO Ltd

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Add the settings page to the WordPress admin menu.
add_action('admin_menu', 'captivate_widget_add_settings_page');

// Add the dashboard widget.
add_action('wp_dashboard_setup', 'captivate_widget_add_dashboard_widget');

// Register the plugin settings.
add_action('admin_init', 'captivate_widget_settings_init');

function captivate_widget_register_settings() {
  add_option('captivate_widget_api_key', '', '', 'yes');
  register_setting('captivate_widget_options_group', 'captivate_widget_api_key');
  $api_key = get_option('captivate_widget_api_key');
}

// Add the plugin settings page.
function captivate_widget_add_settings_page() {
  add_options_page('Captivate Chat Settings', 'Captivate Chat', 'manage_options', 'captivatechat', 'captivate_widget_render_settings_page');
}

// Validate the plugin settings.
function captivate_widget_settings_init() {
  register_setting('captivate_widget_options_group', 'captivate_widget_api_key');
}

// Render the plugin settings page.
function captivate_widget_render_settings_page() {
  if (isset($_POST['update_settings'])) {
    update_option('captivate_widget_api_key', sanitize_text_field($_POST['captivate_widget_api_key']));
  }
  $api_key = get_option('captivate_widget_api_key');
  captivate_widget_add_script();
?>
  <div class="wrap">
  <img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/captivate.png'; ?>" alt="Captivate Chat">
    <h2>Captivate Widget Settings</h2>
    <p><iframe width="560" height="315" src="https://www.youtube.com/embed/PJhJo_VGTp8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></p>
    <p>To obtain your Widget API Key, go to <a href='https://hub.captivat.io'>Captivate Chat</a>, click on your hub, copy the API key, and paste it into the field.<br>
    <strong>Important:</strong> You must have a Captivate Chat account to use this plugin.
    </p>
    <form method="post" action="options.php">
      <?php settings_fields('captivate_widget_options_group'); ?>
      <?php do_settings_sections('captivatechat'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">API Key</th>
          <td><input type="text" name="captivate_widget_api_key" value="<?php echo esc_attr(get_option('captivate_widget_api_key')); ?>" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}

// Add the Captivate Widget script to the website.
function captivate_widget_add_script() {
  $api_key = get_option('captivate_widget_api_key');
  if (!empty($api_key)) {
    wp_enqueue_script('captivatechat', 'https://widget.prod.captivat.io/captivate.min.js', array(), null, true);
    wp_add_inline_script('captivatechat', 'window.addEventListener("load", function() { Captivate.init({ apiKey: "' . $api_key . '" }); Captivate.onConnect(() => { console.log("Connection Status", Captivate.isConnected); }); } );');
  }
}

// Add the dashboard widget
function captivate_widget_add_dashboard_widget() {
  wp_add_dashboard_widget(
    'captivate_widget_dashboard_widget',
    'Captivate Widget',
    'captivate_widget_render_dashboard_widget'
  );
}

// Render the dashboard widget.
function captivate_widget_render_dashboard_widget() {
  $api_key = get_option('captivate_widget_api_key');
  if (!empty($api_key)) {
    echo '<p>Your Captivate Widget API Key is: <code>' . esc_html($api_key) . '</code></p>';
  } else {
    echo '<p>No API Key has been set.</p>';
  }
  echo '<p><a href="options-general.php?page=captivatechat">Click here</a> to set your API Key.</p>';
}

// Register the plugin activation and deactivation hooks.
register_activation_hook(__FILE__, 'captivate_widget_register_settings');
register_deactivation_hook(__FILE__, 'delete_option', 'captivate_widget_api_key');

// Add the plugin settings page link to the WordPress plugins page.
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'captivate_widget_add_settings_link');
function captivate_widget_add_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=captivatechat">' . __('Settings') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
}

// Add the Captivate Widget script to the website.
add_action('wp_enqueue_scripts', 'captivate_widget_add_script');

// Add the dashboard widget.
add_action('wp_dashboard_setup', 'captivate_widget_add_dashboard_widget');