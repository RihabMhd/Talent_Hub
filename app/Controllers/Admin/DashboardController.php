<?php

namespace App\Controllers\Admin;

class DashboardController
{
    private $twig;
    private $db;

    public function __construct($twig, $db)
    {
        $this->twig = $twig;
        $this->db = $db;
    }

    public function index()
    {
        $totalUsers = $this->getTotalUsers();
        $totalRoles = $this->getTotalRoles();
        
        echo $this->twig->render('admin/dashboard.html.twig', [
            'current_user' => $_SESSION['user'] ?? null,
            'total_users' => $totalUsers,
            'total_roles' => $totalRoles,
            'session' => $_SESSION
        ]);
        
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    private function getTotalUsers(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalRoles(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM roles");
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }
}