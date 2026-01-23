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

        // njm3ou stats mn database bach naffichewhom f graphs
        $stats = [
            'users_count' => $this->countUsersByRole(),           
            'jobs_by_category' => $this->countJobsByCategory(),  
            'applications_status' => $this->countApplicationsByStatus(), 
            'top_companies' => $this->getTopCompanies()           
        ];

        // nrenderiw view dial statistics
        echo Twig::render('admin/statistics/index.html.twig', [
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    // exporter stats l csv file - s7al bach admin ysauvi data
    public function export() {
        $this->checkAdmin();
        
        $filename = "talenthub_stats_" . date('Y-m-d') . ".csv";
        
        // n configurew headers dial download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // n ktbou header dial csv
        fputcsv($output, ['Statistic', 'Category/Name', 'Count']);

        // nzidou data dial jobs by category
        $jobs = $this->countJobsByCategory();
        foreach($jobs as $job) {
            fputcsv($output, ['Job Category', $job['name'], $job['count']]);
        }

        // nzidou data dial users
        $users = $this->countUsersByRole();
        foreach($users as $user) {
            fputcsv($output, ['User Role', $user['name'], $user['count']]);
        }

        fclose($output);
        exit();
    }

    // n7sbou users 7sb role dyalhom (admin, recruiter, candidate)
    private function countUsersByRole() {
        $sql = "SELECT r.name, COUNT(u.id) as count 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                GROUP BY r.name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // n7sbou job offers 7sb category
    private function countJobsByCategory() {
        // kan checkew deleted_at bach ma n7sboch archived jobs
        $sql = "SELECT c.nom as name, COUNT(o.id) as count 
                FROM offres o 
                JOIN categories c ON o.category_id = c.id 
                WHERE o.deleted_at IS NULL 
                GROUP BY c.nom";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // status dial applications (pending, accepted, rejected)
    private function countApplicationsByStatus() {
        // hadi temporary data - ila 3ndk table applications badel b query
        $sql = "SELECT status, COUNT(*) as count FROM candidatures GROUP BY status";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // top 5 companies li 3ndhom akthar offres
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