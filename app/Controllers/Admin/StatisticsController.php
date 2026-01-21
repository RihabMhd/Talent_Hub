<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Config\Twig;
use PDO;

class StatisticsController {

    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function index() {
        $this->checkAdmin();

        // 1. Fetch Stats for Graphs
        $stats = [
            'users_count' => $this->countUsersByRole(),
            'jobs_by_category' => $this->countJobsByCategory(),
            'applications_status' => $this->countApplicationsByStatus(),
            'top_companies' => $this->getTopCompanies()
        ];

        // 2. Render View
        echo Twig::render('admin/statistics/index.html.twig', [
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    // --- Export to CSV ---
    public function export() {
        $this->checkAdmin();
        
        $filename = "talenthub_stats_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add Header
        fputcsv($output, ['Statistic', 'Category/Name', 'Count']);

        // Add Data: Jobs by Category
        $jobs = $this->countJobsByCategory();
        foreach($jobs as $job) {
            fputcsv($output, ['Job Category', $job['name'], $job['count']]);
        }

        // Add Data: Users
        $users = $this->countUsersByRole();
        foreach($users as $user) {
            fputcsv($output, ['User Role', $user['name'], $user['count']]);
        }

        fclose($output);
        exit();
    }

    // --- Helper Queries ---

    private function countUsersByRole() {
        $sql = "SELECT r.name, COUNT(u.id) as count 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                GROUP BY r.name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function countJobsByCategory() {
        $sql = "SELECT c.nom as name, COUNT(o.id) as count 
                FROM offres o 
                JOIN categories c ON o.category_id = c.id 
                WHERE o.deleted_at IS NULL 
                GROUP BY c.nom";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function countApplicationsByStatus() {
        // Assuming you have an 'applications' table
        // If not, returns dummy data or create table first
        // $sql = "SELECT status, COUNT(*) as count FROM applications GROUP BY status";
        // return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            ['status' => 'Pending', 'count' => 12],
            ['status' => 'Accepted', 'count' => 5],
            ['status' => 'Rejected', 'count' => 3]
        ];
    }

    private function getTopCompanies() {
        $sql = "SELECT c.nom_entreprise, COUNT(o.id) as count 
                FROM companies c 
                JOIN offres o ON c.id = o.company_id 
                GROUP BY c.nom_entreprise 
                ORDER BY count DESC LIMIT 5";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 1) {
            header('Location: /login');
            exit();
        }
    }
}