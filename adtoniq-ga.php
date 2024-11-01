<?php
/* Plugin Name: Adtoniq for Google Analytics
Plugin URI: https://www.adtoniq.com/
Description: Send ad block analytics into your Google Analytics account. Requires the Adtoniq TruBlock plugin.
Version:  3.0.0-rc3
Author: David Levine for Adtoniq
Author URI: http://www.adtoniq.com/
License: GPLv2 or later
*/

global $adtoniqWebSite;

if (! function_exists('adtoniq_settings') || strlen(get_option('adtoniq-api-key', '')) == 0) {
  function adtoniq_ga_notice_error() {
    $class = 'notice notice-error';
	$message = '';
    if (function_exists('adtoniq_settings') && strlen(get_option('adtoniq-api-key', '')) == 0)
      $message = __( 'Thank you for installing Adtoniq TruBlock. You must now <a id="linkRegisterTruBlock" href="/wp-admin/admin.php?page=adtoniq-settings">register</a> in order to use Adtoniq for Google Analytics.' );
    else if (! function_exists('adtoniq_settings'))
      $message = __( 'Please <a id="linkInstallTruBlock" href="https://wordpress.org/plugins/adtoniq/">install Adtoniq TruBlock</a> and register in order to use Adtoniq for Google Analytics.' );
    if (strlen($message) > 0)
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ),  $message  );
  }
  add_action( 'admin_notices', 'adtoniq_ga_notice_error' );
}

function adtoniq_ga_update() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Changes Saved!</p>
    </div>
    <?php
}

function adtoniq_ga_missing_trublock() {
?>
  <p>
    This plugin lets you capture ad block analytics into Google Analytics but requires that you first install the
    core Adtoniq TruBlock plugin. Click <a id="linkInstallTruBlock" href='https://wordpress.org/plugins/adtoniq/'>here to install Adtoniq TruBlock</a>, complete the registration
    process, and then come back to this page. <a id="linkLearnTruBlock" href='https://www.adtoniq.com/products/trublock/'>Learn more</a> about Adtoniq TruBlock
	and <a href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#a4ga-dependency">why this plugin depends on TruBlock</a>.
  </p>
<?php
}

function adtoniq_ga_settings_page() {
    if (! function_exists('adtoniq_settings') || strlen(get_option('adtoniq-api-key', '')) == 0) {
		adtoniq_ga_missing_trublock();
		return;
	}
	
    $ga_saved = get_option('adtoniq-ga-saved');

	if ($ga_saved == 'saved') {
		adtoniq_ga_update();
		adtoniq_update_option('adtoniq-ga-saved', '', true);
	}
    global $adtoniqWebSite;
    $ga_id = get_option('adtoniq-ga-property-id');
    $ga_split = get_option('adtoniq-ga-traffic-split');
    $apiKey = get_option('adtoniq-api-key', '');
    $quota = 100000;
    adtoniq_update_option('adtoniq-ga-bypass', 'true', true);

    if (strlen($ga_split) == 0) {
      $ga_split = '100';
      adtoniq_update_option('adtoniq-ga-traffic-split', $ga_split, true);
    }

    $data = array(
      'operation'   => 'requestFeature',
      'featureName' => 'GoogleAnalytics',
      'propertyId' => $ga_id,
      'apiKey' => $apiKey
      );
    $response = adtoniq_post(adtoniq_get_server() . 'api/v1', $data);

    $plugin_name = 'Adtoniq TruBlock';
    $plugin_name = 'adtoniq'; // Remove this line to switch to new Adtoniq TruBlock
    $action = 'install-plugin';
    $install_link = 'https://wordpress.org/plugins/adtoniq/';
?>
<link rel="stylesheet" href="<?php echo plugins_url('css/adtoniq.css', __FILE__); ?>">
<script src="<?php echo plugins_url('js/adtoniq-ga.js', __FILE__); ?>"></script>
<div class="adtoniq-ga-plugin wrap">
  <img class="adtoniq-logo" src="<?php echo plugins_url('images/adtoniq-logo-250.png', __FILE__); ?>">
  <h2>Adtoniq For Google Analytics <small class="muted">version 3.0.0-rc2</small></h2>
  <?php
    if (strlen(get_option('adtoniq-api-key', '')) == 0) {
  ?>
  <p>
    This plugin lets you capture ad block analytics into Google Analytics but requires that you first install the
    core Adtoniq TruBlock plugin. To learn Click <a id="linkInstallErrMsg" href='<?php echo $install_link ?>'>here to install Adtoniq TruBlock</a>, complete the registration
    process, and then come back to this page. <a id="linkLearnErrMsg" href='<?php echo $adtoniqWebSite; ?>products/trublock/'>Learn more</a> about Adtoniq TruBlock
	and <a id="linkExplainErrMsg" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#a4ga-dependency">why this plugin depends on TruBlock</a>.
  </p>
  <?php } else { ?>
    <p>This plugin lets you capture ad block analytics into Google Analytics and requires that you first install Adtoniq TruBlock.
You must follow our <a id="linkGaInstructions" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#how-do-i-configure-google-analytics-for-adtoniq">instructions
for setting up your Google Analytics account for Adtoniq</a>. Once you've done that, enter your Google Analytics Tracking ID
in the form below and click Save Changes. You will then start receiving information in your Google Analytics account.</p>
<p>Once you are receiving analytics into your Google Analytics account, you can
	<a id="customDashboardsSegments" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#how-to-get-custom-dashboards-and-segments">add Adtoniq's custom dashboards and segments</a> into your Google Analytics account, read about
	<a id="linkInterpretGaDataSegments" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#how-to-view-custom-segments">using custom segments</a>
	and <a id="linkInterpretGaDataDashboards" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#how-to-view-custom-dashboards">using custom dashboards</a>,
	and <a id="linkTruBlock" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#a4ga-dependency">learn why this plugin depends on TruBlock</a>.</p>
  <p>Google Analytics enforces its own <a id="linkQuotaLimits" href="https://developers.google.com/analytics/devguides/collection/gajs/limits-quotas"> quota limits</a>.
To stay within your quota you can specify a traffic split from 0 to 100%.</p>
	<form method="post" class="adtoniq-ga-form form-horizontal" action="options.php">
		<?php settings_fields( 'adtoniq_ga_settings-group' ); ?>
		<?php do_settings_sections( 'adtoniq_ga_settings-group' ); ?>
		<input type='hidden' name='adtoniq-ga-saved' value='saved'>

    <div class="form-group">
      <label for="adtoniq-ga-property-id" class="col-xs control-label">
        Tracking ID
      </label>
      <div class="col-sm">
        <input
          type="text"
          class="form-control"
          id="gaProperty"
          onblur="AdtoniqGAPlugin.validateAcctNumber()"
          name="adtoniq-ga-property-id"
          placeholder="UA-XXXXXXX-X"
          value="<?php echo esc_attr( $ga_id ); ?>"
          tabindex="1"
          aria-describedby="trackingHelpBlock">
          <span class="form-control-feedback" aria-hidden="true"></span>
      </div>
      <div class="col-half">
        <p>Don't have your own Google Analytics account? <a class="no-link" id="requestAcccount" onClick="AdtoniqGAPlugin.requestAccount('<?php echo adtoniq_get_server(); ?>', '<?php echo $apiKey; ?>');"> Request a dedicated, private, secure property within Adtoniq's Google Analytics account.</a> We'll do all the set up for you and let you know when it's ready, usually within 24 hours.</p>
        <p class="no-margin">Share your Google Analytics property with ga@adtoniq.com (<a id="linkPrivacyPolicy" href='https://www.adtoniq.com/terms-of-service/#section-7'>we promise to keep your information confidential</a>) so we can continue to improve our products, and as a special thank you we will send you advanced reports for Google Analytics to get the most out of Adtoniq for Google Analytics.</p>
      </div>
      <span id="trackingHelpBlock" class="help-block">Message Here</span>
    </div>
    <div class="form-group">
      <label for="adtoniq-ga-traffic-split" class="col-xs control-label">
        Traffic Split
      </label>
      <div class="col-sm">
        <div class="input-group">
          <input type="number" class="form-control" min="0" max="100" step="1" id="traffic" name="adtoniq-ga-traffic-split" value="<?php echo esc_attr( $ga_split ); ?>" tabindex="1">
          <div class="input-group-addon">%</div>
        </div>
      </div>
      <div class="col-half vert-center">
        <p>
          <a id="linkTrafficSplitting" href="https://www.adtoniq.com/my-account/documentation/adtoniq-for-google-analytics/#do-i-need-a-traffic-split">Learn about traffic splitting</a>
        </p>
      </div>
    </div>
    <div class="form-group">
      <label for="adtoniq-ga-quota" class="col-xs control-label">
        Current Quota
      </label>
      <div class="col-sm">
        <input type="text" class="col-sm form-control read-only" readonly min="0" max="100" step="1" id="quota" name="adtoniq-ga-quota" value="<?php echo esc_attr( $quota ); ?>" tabindex="-1" aria-describedby="quotaHelpBlock">
      </div>
      <div class="col-half vert-center">
        <p>
          <a id="linkPurchaseQuota" href="<?php echo $adtoniqWebSite; ?>product/adtoniq/">Purchase Quota Increase</a>
        </p>
      </div>
    </div>
    <div class="form-group">
      <input type="submit" name="submit" id="submit" class="btn btn-primary btn-block" value="Save Changes">
		  <?php /*submit_button();*/ ?>
    </div>
	</form>
<?php } ?>
  <script>AdtoniqGAPlugin.init();</script>
</div>
<?php
}

function adtoniq_ga_menu() {
	add_submenu_page('adtoniq-settings', 'Adtoniq for Google Analytics', 'for Google Analytics', 'administrator', 'adtoniq-settings-ga', 'adtoniq_ga_settings_page');
}

add_action('plugins_loaded', 'adtoniq_ga_plugin_loaded');
function adtoniq_ga_plugin_loaded() {
	if (function_exists('adtoniq_add_menu'))
		adtoniq_add_menu('adtoniq_ga_menu');
}

add_action('admin_menu', 'adtoniq_main_ga_menu');
function adtoniq_main_ga_menu() {
	if (! function_exists('adtoniq_add_menu'))
    add_menu_page('Adtonic for Google Analytics', 'Adtonic for Google Analytics', 'administrator', 'adtoniq_ga_settings', 'adtoniq_ga_settings_page', 'dashicons-admin-generic');
}

add_action( 'admin_init', 'adtoniq_ga_settings' );
function adtoniq_ga_settings() {
  global $adtoniqWebSite;

	register_setting( 'adtoniq_ga_settings-group', 'adtoniq-ga-property-id' );
	register_setting( 'adtoniq_ga_settings-group', 'adtoniq-ga-traffic-split' );
	register_setting( 'adtoniq_ga_settings-group', 'adtoniq-ga-saved' );
}
