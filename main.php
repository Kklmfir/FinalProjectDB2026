<?php
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';
require_once 'helpers/validation.php';
require_once 'helpers/security.php';

// Get database connection
$pdo = require 'config/database.php';

// Sample data for dashboard (replace with actual queries)
$totalBalance = 50000000;
$monthlyIncome = 25000000;
$monthlyExpense = 18000000;
$monthlySpending = 15000000;
$goalProgress = 75;
$budgetUsage = 60;
$activeLoans = 2;
$transferActivity = 5;

include 'components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary"></i> Dashboard Overview
        </h1>
        <p class="text-muted">Welcome to your financial management dashboard</p>
    </div>
</div>

<!-- Dashboard Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Balance</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($totalBalance); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Monthly Income</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($monthlyIncome); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Monthly Expense</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($monthlyExpense); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Monthly Spending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($monthlySpending); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line"></i> Monthly Transactions
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Expense Categories
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-pie">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Cards -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bullseye"></i> Goal Progress
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Emergency Fund</span>
                        <span><?php echo $goalProgress; ?>%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $goalProgress; ?>%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Vacation Fund</span>
                        <span>45%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Car Purchase</span>
                        <span>80%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 80%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Budget Usage
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Food & Dining</span>
                        <span><?php echo $budgetUsage; ?>%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $budgetUsage; ?>%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Transportation</span>
                        <span>35%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 35%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Entertainment</span>
                        <span>70%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 70%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Shopping</span>
                        <span>25%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 25%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-hand-holding-usd"></i> Active Loans & Debts
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="text-primary mr-3">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold">Personal Loan</div>
                        <div class="text-muted">IDR 15,000,000 remaining</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning mr-3">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold">Home Mortgage</div>
                        <div class="text-muted">IDR 250,000,000 remaining</div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="src/debt_loan/index.php" class="btn btn-primary btn-sm">View All Loans</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-exchange-alt"></i> Recent Transfers
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="text-success mr-3">
                        <i class="fas fa-arrow-up fa-lg"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold">Transfer to Savings</div>
                        <div class="text-muted">IDR 2,000,000 - Today</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-danger mr-3">
                        <i class="fas fa-arrow-down fa-lg"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold">Transfer from Emergency</div>
                        <div class="text-muted">IDR 500,000 - Yesterday</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-info mr-3">
                        <i class="fas fa-arrow-right fa-lg"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold">Transfer to Investment</div>
                        <div class="text-muted">IDR 1,500,000 - 2 days ago</div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="src/transfer/index.php" class="btn btn-primary btn-sm">View All Transfers</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>