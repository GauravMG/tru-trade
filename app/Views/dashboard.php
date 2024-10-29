<?php
if (!isset($session)) {
    $session = session();
}
$branchId = $session->get('branchId');
?>

<?= $this->extend('admin_template'); ?>

<?= $this->section('pageStyles'); ?>
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
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Client Accounts</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-info">
                            <div class="card-body">
                                <canvas id="donutChartClientAccounts"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= $data['stats']['totalPaidAccounts']; ?></h3>

                                    <p>Total Paid Accounts</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-md-12">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= $data['stats']['totalPaidAccountsWithDueAmount']; ?></h3>

                                    <p>Accounts with Due Payment</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-md-12">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $data['stats']['totalUnpaidAccounts']; ?></h3>

                                    <p>Unpaid Accounts</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Earnings</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-info">
                            <div class="card-body">
                                <canvas id="donutChartEarnings"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>$<?= $data['stats']['totalEarnings']; ?></h3>

                                    <p>Total Earnings</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-md-12">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>$<?= $data['stats']['totalEarningsThisMonth']; ?></h3>

                                    <p>This Month's Earnings</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-md-12">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>$<?= $data['stats']['totalAmountDue']; ?></h3>

                                    <p>Due Amount</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Last 12 Month Earnings</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="barChartEarnings"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Quickfund Accounts</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $data['stats']['totalQuickfundAccounts']; ?></h3>

                                <p>Total Quickfund Accounts</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-md-3">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>$<?= $data['stats']['totalQuickfundCost']; ?></h3>

                                <p>Total Quickfund Cost</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pageScripts'); ?>
<script src="<?= base_url('assets/adminlte/plugins/chart.js/Chart.min.js'); ?>"></script>
<script src="<?= base_url('js/common.js') . '?t=' . time(); ?>"></script>
<script src="<?= base_url('js/dashboard.js') . '?t=' . time(); ?>"></script>
<script>
    var donutDataClientAccounts = {
        labels: [
            'Paid Accounts without Dues',
            'Paid Accounts with Dues',
            'Unpaid Accounts',
        ],
        datasets: [
            {
                data: [<?= $data['stats']['totalPaidAccounts'] - $data['stats']['totalPaidAccountsWithDueAmount']; ?>, <?= $data['stats']['totalPaidAccountsWithDueAmount']; ?>, <?= $data['stats']['totalUnpaidAccounts']; ?>],
                backgroundColor: ['#00a65a', '#f39c12', '#f56954'],
            }
        ]
    }
    loadClientAccountsDonutChart(donutDataClientAccounts)

    var donutDataEarnings = {
        labels: [
            'Total Earnings',
            'Overall Dues',
        ],
        datasets: [
            {
                data: [<?= $data['stats']['totalEarnings']; ?>, <?= $data['stats']['totalAmountDue']; ?>],
                backgroundColor: ['#00a65a', '#f39c12'],
            }
        ]
    }
    loadEarningsDonutChart(donutDataEarnings)

    const barChartDataEarningsInput = JSON.parse('<?= json_encode($data['graph']['barChartEarnings']); ?>')
    const barChartDataEarnings = {
        labels: barChartDataEarningsInput.map((el) => el.label),
        datasets: [
            {
                label: 'Monthly Earnings',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: barChartDataEarningsInput.map((el) => Number(el.value))
            }
        ]
    }
    loadEarningsBarChart(barChartDataEarnings)
</script>
<?= $this->endSection(); ?>