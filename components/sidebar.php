<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-wallet text-primary"></i> <?php echo APP_NAME; ?></h3>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo getCurrentPage() === 'main.php' ? 'active' : ''; ?>">
            <a href="main.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'pocket') !== false ? 'active' : ''; ?>">
            <a href="src/pocket/index.php">
                <i class="fas fa-piggy-bank"></i>
                <span>Pocket</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'category') !== false ? 'active' : ''; ?>">
            <a href="src/category/index.php">
                <i class="fas fa-tags"></i>
                <span>Category</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'sub_category') !== false ? 'active' : ''; ?>">
            <a href="src/sub_category/index.php">
                <i class="fas fa-list"></i>
                <span>Sub Category</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'transactions') !== false ? 'active' : ''; ?>">
            <a href="src/transactions/index.php">
                <i class="fas fa-exchange-alt"></i>
                <span>Transactions</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'transfer') !== false ? 'active' : ''; ?>">
            <a href="src/transfer/index.php">
                <i class="fas fa-arrow-right-arrow-left"></i>
                <span>Transfer</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'budget') !== false ? 'active' : ''; ?>">
            <a href="src/budget/index.php">
                <i class="fas fa-chart-line"></i>
                <span>Budget</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'goal') !== false ? 'active' : ''; ?>">
            <a href="src/goal/index.php">
                <i class="fas fa-bullseye"></i>
                <span>Goal</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'contact') !== false ? 'active' : ''; ?>">
            <a href="src/contact/index.php">
                <i class="fas fa-address-book"></i>
                <span>Contact</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos(getCurrentPage(), 'debt_loan') !== false ? 'active' : ''; ?>">
            <a href="src/debt_loan/index.php">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Debt & Loan</span>
            </a>
        </li>
    </ul>
</nav>