<?php
if (!isset($session)) {
    $session = session();
}
$branchId = $session->get('branchId');
?>

<?= $this->extend('admin_template'); ?>

<?= $this->section('pageStyles'); ?>
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('css/common.css'); ?>">
<?= $this->endSection(); ?>

<?= $this->section('headerButtons'); ?>
<?php if ($session->get("role") === "Owner") { ?>
    <div class="col-md-5 offset-md-7">
        <select class="form-control" id="branchId" name="branchId" onchange="changeBranch(this.value)">
            <option value="-">-- Select Branch --</option>
            <option value="1" <?php if ($branchId == 1) {
                                    echo 'selected';
                                } ?>>TruTrade Pipeline</option>
            <option value="2" <?php if ($branchId == 2) {
                                    echo 'selected';
                                } ?>>Danny's Sales Pipeline</option>
            <option value="3" <?php if ($branchId == 3) {
                                    echo 'selected';
                                } ?>>Brian's Sales Pipeline</option>
        </select>
    </div>
<?php } ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Clients</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="dtClosedLeads" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Earnings</th>
                            <th>Overall Due</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['leads'] as $lead) { ?>
                            <tr>
                                <td><?= $lead['name']; ?></td>
                                <td><?= $lead['pipeline']['pipelineName']; ?></td>
                                <td><?= $lead['status']; ?></td>
                                <td>$<?= $lead['totalPaidAmount'] ?? 0; ?></td>
                                <td>$<?= $lead['totalDueAmount'] ?? 0; ?></td>
                                <td><?= date("d M, Y h:i A", strtotime($lead['createdAt'])); ?></td>
                                <td>
                                    <div style="display: flex; justify-content: space-around;">
                                        <span onclick="onClickViewLead(<?= $lead['ghlOpportunityId']; ?>)"><i class="fa fa-eye view-icon"></i></span>
                                        <?php if (!empty($lead['contractLink'])) { ?>
                                            <span onclick="onClickDownloadFile('<?= $lead['contractLink']; ?>')"><i class="fa fa-download view-icon"></i></span>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Client Name</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Earnings</th>
                            <th>Overall Due</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('pageScripts'); ?>
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.print.min.js'); ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js'); ?>"></script>
<script src="<?= base_url('js/common.js') . '?t=' . time(); ?>"></script>
<script src="<?= base_url('js/clients.js') . '?t=' . time(); ?>"></script>
<?= $this->endSection(); ?>