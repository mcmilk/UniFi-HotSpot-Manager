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
 * Form for useradd modal.
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form data-toggle="validator" method="POST" action="">
  <fieldset>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><?php echo __("Please select the permission level."); ?>:</span>
        <select name="options" class="form-control" placeholder="<?php echo __("Usergroup"); ?>" value="" required>
          <option value="a"><?php echo __("Administrator"); ?></option>
          <option value="u"><?php echo __("Default"); ?></option>
          <option value="d"><?php echo __("Disabled"); ?></option>
        </select>
      </div>
      <span style="float: right;width: 5em;" class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
        <input type="text" name="username" required class="form-control" placeholder="<?php echo __("Username"); ?>">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

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

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><?php echo __("Voucher prefix"); ?>:</span>
        <input type="text" name="prefix" class="form-control" placeholder="<?php echo __("Voucher prefix"); ?>">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><?php echo __("Voucher headline"); ?>:</span>
        <input type="text" name="headline" required class="form-control" placeholder="<?php echo __("Voucher headline"); ?>">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

  </fieldset>
</form>
