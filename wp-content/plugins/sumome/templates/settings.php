<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('styles/instructions.css', dirname(__FILE__)) ?>">
<div id="sumome_instructions">
  <!-- Header -->
  <div class="large-12 columns header-banner">
    <img src="<?php echo plugins_url('images/sumome-banner.jpg', dirname(__FILE__)) ?>">
  </div>

  <!-- Register -->
  <div class="row step1">
    <div class="large-6 columns">
      <div class="row">
        <div class="large-12 columns">
          <div class="text">
            <div class="list-bullet">
              <div class="list-number">1</div>
              <h4 class="list-number-title">Register Your Account</h4>
            </div>
            <p>Click on the SumoMe badge in the top right of your screen.  Sign up to register your account and get rolling.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="large-6 columns">
      <img src="<?php echo plugins_url('images/step2-browser.gif', dirname(__FILE__)) ?>">
    </div>
  </div>

  <!-- Install Tools -->
  <div class="row step2">
    <div class="large-6 columns no-mobile">
      <img src="<?php echo plugins_url('images/step3-browser.gif', dirname(__FILE__)) ?>">
    </div>
    <div class="large-6 columns">
      <div class="row">
        <div class="large-12 columns">
          <div class="text">
            <div class="list-bullet">
              <div class="list-number">2</div>
              <h4 class="list-number-title">Install Tools!</h4>
            </div>
            <p>Click on the Sumo Store icon to browse and install the different tools.  Each take seconds and only one-click to install.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="large-6 columns mobile">
      <img src="<?php echo plugins_url('images/step3-browser.gif', dirname(__FILE__)) ?>">
    </div>
  </div>


  <!-- Review -->
  <div class="row row3">
    <div class="large-12 columns">
      <div class="list-bullet">
        <div class="list-number">3</div>
        <h4 class="list-number-title">Leave a Review!</h4>
      </div>
      <div class="sumome-instructions">We will love you forever if you leave an <a href="https://wordpress.org/support/view/plugin-reviews/sumome" target="_blank">honest review here</a> of the SumoMe plugin.</div>
    </div>
  </div>


  <!-- Site ID -->
  <div class="row row3">
    <div class="large-12 columns">
      <div class="list-bullet">
        <h4 class="list-number-title">Your Site Id</h4>
      </div>

      <form method="post" action="options.php">
        <?php settings_fields('sumome'); ?>

        <table>
          <?php do_settings_fields('sumome', 'sumome-settings') ?>
        </table>
        <div class="sumome-instructions">
          NOTE: If you already have a site ID from a previous installation and you wish to retain all your settings then enter the site ID above otherwise you may use a new site ID to perform a new installation.  Changing the site ID will lose all settings, apps, and purchases.
        </div>
        <?php submit_button(); ?>
      </form>
    </div>
  </div>


  <!-- Help -->
  <div class="row">
    <div class="large-12 columns footer">
      <h4 class="list-number-title">Need Help?</h4>
      <div class="sumome-help">
        <span>Take a look at our <a href="https://help.sumome.com/" target="_blank">help page</a> to see our frequently answered</span>
        <span>questions or <a href="https://help.sumome.com/customer/portal/emails/new" target="_blank">send us a message</a> and we will get back to you asap.</span>
      </div>
    </div>
  </div>

</div>









