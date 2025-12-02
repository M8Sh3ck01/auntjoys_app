<?php
/**
 * Manager Controller
 * Handles reports generation and exports
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../includes/auth.php';

class ManagerController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    public function showDashboard() {
        requireRole([4]);

        $stats = $this->orderModel->getStatistics();
        $bestSellers = $this->orderModel->getBestSellers(5);

        require __DIR__ . '/../views/manager/dashboard.php';
    }

    public function showReports() {
        requireRole([4]);

        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        $stats = $this->orderModel->getStatistics($month, $year);
        $bestSellers = $this->orderModel->getBestSellers(10, $month, $year);

        require __DIR__ . '/../views/manager/reports.php';
    }

    /**
     * Export report to CSV (Excel-compatible)
     */
    public function exportExcel() {
        requireRole([4]); // Manager only

        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        // Get data
        $stats = $this->orderModel->getStatistics($month, $year);
        $bestSellers = $this->orderModel->getBestSellers(10, $month, $year);
        $orders = $month && $year ? $this->orderModel->getAll() : $this->orderModel->getAll();

        // Filter orders by date if needed
        if ($month && $year) {
            $orders = array_filter($orders, function($order) use ($month, $year) {
                $orderMonth = date('n', strtotime($order['order_date']));
                $orderYear = date('Y', strtotime($order['order_date']));
                return $orderMonth == $month && $orderYear == $year;
            });
        }

        // Set headers for Excel download
        $filename = "sales_report_" . ($month && $year ? "{$year}_{$month}" : "all") . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Write header
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Write summary
        fputcsv($output, ['SALES REPORT']);
        fputcsv($output, ['Period:', $month && $year ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : 'All Time']);
        fputcsv($output, ['Generated:', date('Y-m-d H:i:s')]);
        fputcsv($output, []);

        // Write statistics
        fputcsv($output, ['SUMMARY']);
        fputcsv($output, ['Total Orders', $stats['total_orders'] ?? 0]);
        fputcsv($output, ['Total Revenue (MWK)', number_format($stats['total_revenue'] ?? 0, 2)]);
        fputcsv($output, ['Average Order Value (MWK)', number_format($stats['avg_order_value'] ?? 0, 2)]);
        fputcsv($output, []);

        // Write best sellers
        fputcsv($output, ['BEST SELLING ITEMS']);
        fputcsv($output, ['Meal Name', 'Quantity Sold', 'Revenue (MWK)']);
        foreach ($bestSellers as $item) {
            fputcsv($output, [
                $item['name'],
                $item['total_sold'],
                number_format($item['revenue'], 2)
            ]);
        }
        fputcsv($output, []);

        // Write orders details
        fputcsv($output, ['ALL ORDERS']);
        fputcsv($output, ['Order ID', 'Date', 'Customer', 'Amount (MWK)', 'Status']);
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_id'],
                date('Y-m-d H:i', strtotime($order['order_date'])),
                $order['username'],
                number_format($order['total_amount'], 2),
                $order['status']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export report to PDF
     */
    public function exportPDF() {
        requireRole([4]); // Manager only

        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        // Get data
        $stats = $this->orderModel->getStatistics($month, $year);
        $bestSellers = $this->orderModel->getBestSellers(10, $month, $year);

        // Simple HTML to PDF conversion using browser print
        $period = $month && $year ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : 'All Time';
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Sales Report - <?php echo $period; ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
                h2 { color: #555; margin-top: 30px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #007bff; color: white; }
                .stats { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .stat-item { margin: 10px 0; font-size: 18px; }
                .stat-label { font-weight: bold; color: #555; }
                .stat-value { color: #007bff; font-weight: bold; }
                @media print {
                    body { margin: 20px; }
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <h1>Aunt Joy's Restaurant - Sales Report</h1>
            <p><strong>Period:</strong> <?php echo $period; ?></p>
            <p><strong>Generated:</strong> <?php echo date('F d, Y H:i:s'); ?></p>

            <div class="stats">
                <h2>Summary Statistics</h2>
                <div class="stat-item">
                    <span class="stat-label">Total Orders:</span>
                    <span class="stat-value"><?php echo $stats['total_orders'] ?? 0; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Revenue:</span>
                    <span class="stat-value">MWK <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Average Order Value:</span>
                    <span class="stat-value">MWK <?php echo number_format($stats['avg_order_value'] ?? 0, 2); ?></span>
                </div>
            </div>

            <h2>Best Selling Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Meal Name</th>
                        <th>Quantity Sold</th>
                        <th>Revenue (MWK)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($bestSellers as $item): 
                    ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['total_sold']; ?></td>
                            <td>MWK <?php echo number_format($item['revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 40px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 30px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    Print / Save as PDF
                </button>
                <button onclick="window.close()" style="padding: 10px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                    Close
                </button>
            </div>

            <script>
                // Auto-open print dialog
                setTimeout(function() {
                    if (confirm('Ready to print/save as PDF?')) {
                        window.print();
                    }
                }, 500);
            </script>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
