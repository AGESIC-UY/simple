<?php if ($this->session->flashdata('message')): ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('message') ?>
    </div>
<?php endif ?>
<?php if ($this->session->flashdata('message_warning')): ?>
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('message_warning') ?>
    </div>
<?php endif ?>
<?php if ($this->session->flashdata('message_error')): ?>
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('message_error') ?>
    </div>
<?php endif ?>