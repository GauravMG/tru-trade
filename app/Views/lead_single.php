<?= $this->extend('admin_template'); ?>

<?= $this->section('pageStyles'); ?>
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css'); ?>">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="lead-details-tab" data-toggle="pill" href="#lead-details" role="tab" aria-controls="lead-details" aria-selected="true">Lead / Account Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manage-accounts-tab" data-toggle="pill" href="#manage-accounts" role="tab" aria-controls="manage-accounts" aria-selected="false">Manage Accounts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manage-payments-tab" data-toggle="pill" href="#manage-payments" role="tab" aria-controls="manage-payments" aria-selected="false">Manage Payments</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-five-tabContent">
                    <!-- start tab - lead details -->
                    <div class="tab-pane fade show active" id="lead-details" role="tabpanel" aria-labelledby="lead-details-tab">
                        <div class="overlay-wrapper">
                            <div id="lead-details-loader" class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2">Loading...</div>
                            </div>
                            <div id="lead-details-content">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Lead Details</h3>
                                                <span id="leadCreatedAt" style="float: right; font-size: 12px;"></span>
                                            </div>
                                            <form>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" id="name" placeholder="Enter name" readonly>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="status">Status</label>
                                                            <input type="text" class="form-control" id="status" placeholder="Enter status" readonly>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="source">Source</label>
                                                            <input type="text" class="form-control" id="source" placeholder="Enter source" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label>Account Type <span class="text-danger">*</span></label>
                                                            <div class="form-check">
                                                                <label class="form-check-label"><input class="form-check-input" type="radio" name="accountType" id="accountTypeFunded" value="funded"> Funded</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <label class="form-check-label"><input class="form-check-input" type="radio" name="accountType" id="accountTypeBrokered" value="brokered"> Brokered</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6" id="server-div">
                                                            <label>Server <span class="text-danger">*</span></label>
                                                            <br>
                                                            <span style="font-size: 13px;"> (Please select account type first)</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="serverCost">Server Cost <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="serverCost" placeholder="Enter server cost">
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Lead Contact Person Details</h3>
                                            </div>
                                            <form>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="contactNameExternal">Contact Name</label>
                                                        <input type="text" class="form-control" id="contactNameExternal" placeholder="Enter contact name" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contactCompanyNameExternal">Contact Company Name</label>
                                                        <input type="text" class="form-control" id="contactCompanyNameExternal" placeholder="Enter contact person name" readonly>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="contactEmailIdExternal">Contact Email ID</label>
                                                            <input type="email" class="form-control" id="contactEmailIdExternal" placeholder="Enter contact email id" readonly>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="contactPhoneExternal">Contact Phone</label>
                                                            <input type="text" class="form-control" id="contactPhoneExternal" placeholder="Enter contact phone" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contactTagsExternal">Contact Tags</label>
                                                        <!-- <input type="text" placeholder="Enter contact tags" readonly> -->
                                                        <textarea rows="3" class="form-control" id="contactTagsExternal" readonly></textarea>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <form id="formLeadDetailsSubmit">
                                                <div class="card-footer">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end tab - lead details -->

                    <!-- start tab - manage accounts -->
                    <div class="tab-pane fade" id="manage-accounts" role="tabpanel" aria-labelledby="manage-accounts-tab">
                        <div class="overlay-wrapper">
                            <div id="manage-accounts-loader" class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2">Loading...</div>
                            </div>
                            <div id="manage-accounts-content">
                                <div class="card">
                                    <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                                        <h3 class="card-title" style="width: 100%;">All Accounts</h3>
                                        <div style="width: 100%; text-align: end;" id="addAccountButtonSource"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-account">Add Account</button></div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="dtAccountList" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>System Type</th>
                                                    <th>Account Number</th>
                                                    <th>Account Size</th>
                                                    <th>Account Cost</th>
                                                    <th>Multiplier</th>
                                                    <th>Created On</th>
                                                </tr>
                                            </thead>
                                            <tbody id="account-list"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>System Type</th>
                                                    <th>Account Number</th>
                                                    <th>Account Size</th>
                                                    <th>Account Cost</th>
                                                    <th>Multiplier</th>
                                                    <th>Created On</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-add-account">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add New Account</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formNewAccount">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="newAccountSystemType">System Type <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="newAccountSystemType" placeholder="Enter system type">
                                            </div>
                                            <div class="form-group">
                                                <label for="newAccountNumber">Account Number <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="newAccountNumber" placeholder="Enter account number">
                                            </div>
                                            <div class="form-group" id="newServerAccountSizeContainer">
                                                <label for="newAccountSize">Account Size <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="newAccountSize" placeholder="Enter account size between 50000 to 300000" min="50" max="300">
                                            </div>
                                            <div class="form-group">
                                                <label for="newAccountCost">Account Cost <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="newAccountCost" placeholder="Enter account cost">
                                            </div>
                                            <div class="form-group">
                                                <label for="newAccountMultiplier">Multiplier <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="newAccountMultiplier" placeholder="Enter multiplier between 1 to 20" min="1" max="20">
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </form>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="saveAccount()">Save changes</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- end tab - manage accounts -->

                    <!-- start tab - manage payments -->
                    <div class="tab-pane fade" id="manage-payments" role="tabpanel" aria-labelledby="manage-payments-tab">
                        <div class="overlay-wrapper">
                            <div id="manage-payments-loader" class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2">Loading...</div>
                            </div>
                            <div id="manage-payments-content">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">All Payments</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="dtPaymentList" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Payment for Month</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Mark as Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody id="payment-list"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Payment for Month</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Mark as Paid</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-payment-pay">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Enter Payment Details</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formPaymentDetails">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <input type="hidden" class="form-control" id="paymentDetailsPaymentId">
                                            </div>
                                            <div class="form-group">
                                                <label for="paymentDetailsPaymentModeOn">Payment Made On <span class="text-danger">*</span></label>
                                                <input type="date" id="paymentDetailsPaymentModeOn" class="form-control" placeholder="Select Month and Year">
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </form>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="savePaymentDetails()">Save changes</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- end tab - manage payments -->
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    const leadId = <?= $opportunity["ghlOpportunityId"]; ?>;
</script>
<script src="<?= base_url('js/common.js') . '?t=' . time(); ?>"></script>
<script src="<?= base_url('js/lead-single.js') . '?t=' . time(); ?>"></script>
<?= $this->endSection(); ?>