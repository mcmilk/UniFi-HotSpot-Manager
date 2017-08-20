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
 * Form for password change modal.
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form data-toggle="validator" method="POST" action="">
  <fieldset>

    <input type="hidden" name="username" value="">
    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
        <input type="password" data-minlength="8" name="password" id="inputPassword" required class="form-control" placeholder="<?php echo __("Password"); ?>">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
        <input type="password" data-minlength="8" name="password2" data-match="#inputPassword" required class="form-control" placeholder="<?php echo __("Confirm Password"); ?>">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

  </fieldset>

</form>
