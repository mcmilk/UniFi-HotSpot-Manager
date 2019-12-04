<?php

/**
 * UniFi HotSpot Manager
 *
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Show Login page.
 */

if (!defined('HOTSPOT')) { exit; }

?>

<div class="row">
    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
        <form data-toggle="validator" method="POST" action="main.php">
            <fieldset>
                <h2>HotSpot Manager - <?php echo __("Sign In"); ?></h2>
                <hr class="colorgraph">

                <?php if (empty($use_saml) || ($use_saml === false)) { ?>
                <div class="form-group has-feedback">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                    <input type="text" name="username" required class="form-control input-lg" placeholder="<?php echo __('Username'); ?>">
                    <input type="hidden" name="loginform" value="login">
                  </div>
                  <span class="glyphicon form-control-feedback"></span>
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                    <input type="password" name="password" required class="form-control input-lg" placeholder="<?php echo __('Password'); ?>">
                  </div>
                  <span class="glyphicon form-control-feedback"></span>
                  <div class="help-block with-errors"></div>
                </div>
                <?php } ?>

                <div class="form-group">
                    <input type="submit" class="btn btn-lg btn-success btn-block" value="<?php echo __("Sign In"); ?>">
                </div>
            </fieldset>
        </form>
    </div>
</div>
