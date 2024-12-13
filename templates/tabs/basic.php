<div class="tab-pane active" id="dantesettings">
  <h4 class="mt-3"><?php echo _("Basic settings"); ?></h4>
  <div class="row">
    <div class="mb-3 col-12 mt-2">
      <div class="row">
        <div class="col-12">
          <?php echo htmlspecialchars($content); ?>
        </div>
      </div>

      <div class="row mt-3">
        <div class="mb-3 col-md-4">
          <label for="txtinternal"><?php echo _("Internal interface") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The listening network interface or IP addresses that can connect. Connections will only be accepted on these addresses."); ?>"></i>
            <input type="text" class="form-control ip_address" name="txtinternal" value="<?php echo $arrConfig['internal_addr']; ?>">
        </div>

        <div class="mb-3 col-md-2">
          <label for="txtport"><?php echo _("Port") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The port that the service is running on."); ?>"></i>
            <input type="text" class="form-control" name="txtport" value="<?php echo $arrConfig['internal_port']; ?>">
        </div>
      </div>
      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="cbxinterface"><?php echo _("External interface") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The network interface used for outbound SOCKS proxy connections."); ?>"></i>
            <?php SelectorOptions('interface', $interfaces, $arrConfig['external'], 'cbxinterface'); ?>
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-3">
          <label for="txtuserprivileged"><?php echo _("Privileged user") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The user which will be used for doing privileged operations."); ?>"></i>
            <input type="text" class="form-control" name="txtuserprivileged" value="<?php echo $arrConfig['user.privileged']; ?>">
        </div>
        <div class="mb-3 col-md-3">
          <label for="txtuserunprivileged"><?php echo _("Unprivileged user") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The user which the server runs as most of the time."); ?>"></i>
            <input type="text" class="form-control" name="txtuserunprivileged" value="<?php echo $arrConfig['user.unprivileged']; ?>">
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="txtsocksmethod"><?php echo _("SOCKS method") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("A list of acceptable authentication methods for socks-rules, listed in order of preference. Specify these in the desired order, with the more secure methods first."); ?>"></i>
            <input type="text" class="form-control" name="txtsocksmethod" value="<?php echo $arrConfig['socksmethod']; ?>">
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="txtclientmethod"><?php echo _("Client method") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("Require that the connection be authenticated using one of the given client methods."); ?>"></i>
            <input type="text" class="form-control" name="txtclientmethod" value="<?php echo $arrConfig['clientmethod']; ?>">
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="txtclientip"><?php echo _("Allowed client IPs") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("For security, restrict access to specific client IP addresses. CIDR notation is supported."); ?>"></i>
            <input type="text" class="form-control" name="txtclientip" value="<?php echo $arrConfig['from']; ?>">
        </div>
      </div>

    </div>
  </div>
</div><!-- /.tab-pane | basic tab -->

