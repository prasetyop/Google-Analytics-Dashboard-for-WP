<?php
/**
 * Author: Alin Marcu
 * Author URI: http://deconf.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if (! class_exists('GADASH_Backend_Ajax')) {

  final class GADASH_Backend_Ajax
  {

    function __construct()
    {
      // Backend Widget Realtime action
      add_action('wp_ajax_gadash_get_realtime', array(
        $this,
        'ajax_widget_realtime'
      ));
      // Admin Widget get Reports action
      add_action('wp_ajax_gadash_get_widgetreports', array(
        $this,
        'ajax_widget_reports'
      ));
      // Admin Post/Page List get Reports action
      add_action('wp_ajax_gadash_get_itemreports', array(
        $this,
        'ajax_item_reports'
      ));
    }

    /**
     * Ajax handler for getting reports for Admin Post/Page List
     *
     * @return json|int
     */
    function ajax_item_reports()
    {
      global $GADASH_Config;
      if (! isset($_REQUEST['gadash_security_item_reports']) or ! wp_verify_nonce($_REQUEST['gadash_security_item_reports'], 'gadash_get_itemreports')) {
        wp_die(- 30);
      }
      $id = $_REQUEST['id'];
      /*
       * Include Tools
       */
      include_once ($GADASH_Config->plugin_path . '/tools/tools.php');
      $tools = new GADASH_Tools();
      if (! $tools->check_roles($GADASH_Config->options['ga_dash_access_back'])) {
        wp_die(- 31);
      }
      if ($GADASH_Config->options['ga_dash_token'] and $GADASH_Config->options['ga_dash_tableid_jail']) {
        include_once ($GADASH_Config->plugin_path . '/tools/gapi.php');
        global $GADASH_GAPI;
      } else {
        wp_die(- 24);
      }
      $profile_info = $tools->get_selected_profile($GADASH_Config->options['ga_dash_profile_list'], $projectId);
      if (isset($profile_info[4])) {
        $GADASH_GAPI->timeshift = $profile_info[4];
      } else {
        $GADASH_GAPI->timeshift = (int) current_time('timestamp') - time();
      }
      wp_send_json('Nice');
    }

    /**
     * Ajax handler for getting reports for Admin Widget
     *
     * @return json|int
     */
    function ajax_widget_reports()
    {
      global $GADASH_Config;
      if (! isset($_REQUEST['gadash_security_widget_reports']) or ! wp_verify_nonce($_REQUEST['gadash_security_widget_reports'], 'gadash_get_widgetreports')) {
        wp_die(- 30);
      }
      $projectId = $_REQUEST['projectId'];
      $from = $_REQUEST['from'];
      $to = $_REQUEST['to'];
      $query = $_REQUEST['query'];
      if (ob_get_length()) {
        ob_clean();
      }
      /*
       * Include Tools
       */
      include_once ($GADASH_Config->plugin_path . '/tools/tools.php');
      $tools = new GADASH_Tools();
      if (! $tools->check_roles($GADASH_Config->options['ga_dash_access_back'])) {
        wp_die(- 31);
      }
      if ($GADASH_Config->options['ga_dash_token'] and $projectId and $from and $to) {
        include_once ($GADASH_Config->plugin_path . '/tools/gapi.php');
        global $GADASH_GAPI;
      } else {
        wp_die(- 24);
      }
      $profile_info = $tools->get_selected_profile($GADASH_Config->options['ga_dash_profile_list'], $projectId);
      if (isset($profile_info[4])) {
        $GADASH_GAPI->timeshift = $profile_info[4];
      } else {
        $GADASH_GAPI->timeshift = (int) current_time('timestamp') - time();
      }
      switch ($query) {
        case 'referrers':
          wp_send_json($GADASH_GAPI->get_referrers($projectId, $from, $to));
          break;
        case 'contentpages':
          wp_send_json($GADASH_GAPI->get_contentpages($projectId, $from, $to));
          break;
        case 'locations':
          wp_send_json($GADASH_GAPI->get_locations($projectId, $from, $to));
          break;
        case 'bottomstats':
          wp_send_json($GADASH_GAPI->get_bottomstats($projectId, $from, $to));
          break;
        case 'trafficchannels':
          wp_send_json($GADASH_GAPI->get_trafficchannels($projectId, $from, $to));
          break;
        case 'medium':
          wp_send_json($GADASH_GAPI->get_trafficdetails($projectId, $from, $to, 'medium'));
          break;
        case 'visitorType':
          wp_send_json($GADASH_GAPI->get_trafficdetails($projectId, $from, $to, 'visitorType'));
          break;
        case 'socialNetwork':
          wp_send_json($GADASH_GAPI->get_trafficdetails($projectId, $from, $to, 'socialNetwork'));
          break;
        case 'source':
          wp_send_json($GADASH_GAPI->get_trafficdetails($projectId, $from, $to, 'source'));
          break;
        case 'searches':
          wp_send_json($GADASH_GAPI->get_searches($projectId, $from, $to));
          break;
        default:
          wp_send_json($GADASH_GAPI->get_mainreport($projectId, $from, $to, $query));
          break;
      }
    }
    // Real-Time Request
    /**
     * Ajax handler for getting realtime analytics data for Admin widget
     *
     * @return json|int
     */
    function ajax_widget_realtime()
    {
      global $GADASH_Config;
      if (! isset($_REQUEST['gadash_security_widgetrealtime']) or ! wp_verify_nonce($_REQUEST['gadash_security_widgetrealtime'], 'gadash_get_realtime')) {
        wp_die(- 30);
      }
      $projectId = $_REQUEST['projectId'];
      if (ob_get_length()) {
        ob_clean();
      }
      /*
       * Include Tools
       */
      include_once ($GADASH_Config->plugin_path . '/tools/tools.php');
      $tools = new GADASH_Tools();
      if (! $tools->check_roles($GADASH_Config->options['ga_dash_access_back'])) {
        wp_die(- 31);
      }
      if ($GADASH_Config->options['ga_dash_token'] and $projectId) {
        include_once ($GADASH_Config->plugin_path . '/tools/gapi.php');
        global $GADASH_GAPI;
      } else {
        wp_die(- 24);
      }
      wp_send_json($GADASH_GAPI->get_realtime_data($projectId));
    }
  }
}
$GADASH_Backend_Ajax = new GADASH_Backend_Ajax();
