<?php

namespace App\Controllers\Recruiter;

use App\Services\JobOfferService;
use App\Services\CategoryService;
use App\Services\TagService;

class JobOfferController
{
    private $jobOfferService;
    private $categoryService;
    private $tagService;
    private $twig;
    private $db;

    public function __construct($jobOfferService, $categoryService, $tagService, $twig)
    {
        $this->jobOfferService = $jobOfferService;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->twig = $twig;
    }

    public function index()
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        $filter = $_GET['filter'] ?? 'all';

        try {
            // njibou job offers dial recruiter 7sb filter (all, active, archived)
            $offers = $this->getRecruiterOffers($userId, $filter);
            
            // companies dyalo 
            $companies = $this->getRecruiterCompanies($userId);
            
            // categories bach y9der ychof w ya3ti category l kol offer
            $categories = $this->categoryService->getAllCategories();
            
            // stats - ch7al offer 3nd kol company
            $stats = $this->getCompanyStats($userId);

            echo $this->twig->render('recruiter/jobs/index.html.twig', [
                'offers' => $offers,
                'companies' => $companies,
                'categories' => $categories,
                'stats' => $stats,
                'currentFilter' => $filter,
                'session' => $_SESSION,
                'current_user' => $_SESSION['user'] ?? null,
                'app' => [
                    'request' => [
                        'uri' => $_SERVER['REQUEST_URI'] ?? ''
                    ]
                ]
            ]);

            unset($_SESSION['success']);
            unset($_SESSION['error']);

        } catch (\Exception $e) {
            error_log("Error loading recruiter jobs: " . $e->getMessage());
            $_SESSION['error'] = "Failed to load jobs";
            header('Location: /recruiter/dashboard');
            exit;
        }
    }

    // njibou offers dial recruiter - b join m3a companies w categories
    private function getRecruiterOffers($userId, $filter)
    {
        try {
            $db = $this->getDbConnection();
            
            // query complexe - kayjib offers m3a company name w category
            $sql = "
                SELECT 
                    o.id,
                    o.titre,
                    o.description,
                    o.salaire,
                    o.lieu,
                    o.status,
                    o.created_at,
                    o.deleted_at,
                    o.company_id,
                    o.category_id,
                    c.nom_entreprise,
                    cat.nom as category_name,
                    CONCAT(u.prenom, ' ', u.nom) as recruteur_nom
                FROM offres o
                INNER JOIN companies c ON o.company_id = c.id
                INNER JOIN categories cat ON o.category_id = cat.id
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.user_id = :user_id
            ";

            // n appliqiw filter 7sb choice dial user
            if ($filter === 'active') {
                $sql .= " AND o.deleted_at IS NULL AND o.status = 'active'";
            } elseif ($filter === 'archived') {
                $sql .= " AND o.deleted_at IS NOT NULL";
            }

            $sql .= " ORDER BY o.created_at DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            error_log("Error getting recruiter offers: " . $e->getMessage());
            return [];
        }
    }

    // njibou companies dial recruiter
    private function getRecruiterCompanies($userId)
    {
        try {
            $db = $this->getDbConnection();
            
            $stmt = $db->prepare("
                SELECT id, nom_entreprise 
                FROM companies 
                WHERE user_id = :user_id
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            error_log("Error getting companies: " . $e->getMessage());
            return [];
        }
    }

    // stats - ch7al offer 3nd kol company
    private function getCompanyStats($userId)
    {
        try {
            $db = $this->getDbConnection();
            
            $stmt = $db->prepare("
                SELECT 
                    c.nom_entreprise,
                    COUNT(o.id) as total_offres
                FROM companies c
                LEFT JOIN offres o ON c.id = o.company_id AND o.deleted_at IS NULL
                WHERE c.user_id = :user_id
                GROUP BY c.id, c.nom_entreprise
                HAVING total_offres > 0
                ORDER BY total_offres DESC
                LIMIT 4
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            error_log("Error getting company stats: " . $e->getMessage());
            return [];
        }
    }

    // archiver offer - soft delete (ma kanmsho7ohch permanently)
    public function archive($id)
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        
        try {
            $db = $this->getDbConnection();
            
            // n checkew ownership - ma y9derch yarchiver offer dial recruiter akhor
            if (!$this->verifyOfferOwnership($id, $userId)) {
                $_SESSION['error'] = 'Unauthorized action';
                header('Location: /recruiter/jobs');
                exit;
            }

            // n sauvgardiw date f deleted_at - hada soft delete
            $stmt = $db->prepare("
                UPDATE offres 
                SET deleted_at = NOW() 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
            
            $_SESSION['success'] = 'Job offer archived successfully';
        } catch (\Exception $e) {
            error_log("Error archiving offer: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to archive job offer';
        }

        header('Location: /recruiter/jobs');
        exit;
    }

    // restaurer offer mn archive
    public function restore($id)
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        
        try {
            $db = $this->getDbConnection();
            
            if (!$this->verifyOfferOwnership($id, $userId)) {
                $_SESSION['error'] = 'Unauthorized action';
                header('Location: /recruiter/jobs');
                exit;
            }

            // nrja3ou deleted_at l NULL
            $stmt = $db->prepare("
                UPDATE offres 
                SET deleted_at = NULL 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
            
            $_SESSION['success'] = 'Job offer restored successfully';
        } catch (\Exception $e) {
            error_log("Error restoring offer: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to restore job offer';
        }

        header('Location: /recruiter/jobs');
        exit;
    }

    // créer offer jdid
    public function store()
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        
        try {
            $db = $this->getDbConnection();
            
            // n checkew ila company t3lq b recruiter had
            $companyId = $_POST['company_id'] ?? null;
            if (!$this->verifyCompanyOwnership($companyId, $userId)) {
                $_SESSION['error'] = 'Unauthorized action';
                header('Location: /recruiter/jobs');
                exit;
            }

            $stmt = $db->prepare("
                INSERT INTO offres (titre, description, company_id, category_id, salaire, lieu, status, created_at)
                VALUES (:titre, :description, :company_id, :category_id, :salaire, :lieu, :status, NOW())
            ");
            
            $stmt->execute([
                'titre' => $_POST['titre'] ?? '',
                'description' => $_POST['description'] ?? '',
                'company_id' => $companyId,
                'category_id' => $_POST['category_id'] ?? null,
                'salaire' => $_POST['salaire'] ?? null,
                'lieu' => $_POST['lieu'] ?? '',
                'status' => $_POST['status'] ?? 'active'
            ]);
            
            $_SESSION['success'] = 'Job offer created successfully';
        } catch (\Exception $e) {
            error_log("Error creating offer: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create job offer';
        }

        header('Location: /recruiter/jobs');
        exit;
    }

    // modifier offer
    public function update($id)
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        
        try {
            $db = $this->getDbConnection();
            
            if (!$this->verifyOfferOwnership($id, $userId)) {
                $_SESSION['error'] = 'Unauthorized action';
                header('Location: /recruiter/jobs');
                exit;
            }

            $stmt = $db->prepare("
                UPDATE offres 
                SET titre = :titre,
                    description = :description,
                    category_id = :category_id,
                    salaire = :salaire,
                    lieu = :lieu,
                    status = :status
                WHERE id = :id
            ");
            
            $stmt->execute([
                'titre' => $_POST['titre'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category_id' => $_POST['category_id'] ?? null,
                'salaire' => $_POST['salaire'] ?? null,
                'lieu' => $_POST['lieu'] ?? '',
                'status' => $_POST['status'] ?? 'active',
                'id' => $id
            ]);
            
            $_SESSION['success'] = 'Job offer updated successfully';
        } catch (\Exception $e) {
            error_log("Error updating offer: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update job offer';
        }

        header('Location: /recruiter/jobs');
        exit;
    }

    // supprimer permanently - hard delete, ma y9derch yrj3ha
    public function delete($id)
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        
        try {
            $db = $this->getDbConnection();
            
            if (!$this->verifyOfferOwnership($id, $userId)) {
                $_SESSION['error'] = 'Unauthorized action';
                header('Location: /recruiter/jobs');
                exit;
            }

            $stmt = $db->prepare("DELETE FROM offres WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            $_SESSION['success'] = 'Job offer deleted successfully';
        } catch (\Exception $e) {
            error_log("Error deleting offer: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete job offer';
        }

        header('Location: /recruiter/jobs');
        exit;
    }

    // verification - ila offer t3lq b recruiter had
    private function verifyOfferOwnership($offerId, $userId)
    {
        try {
            $db = $this->getDbConnection();
            
            // n joiniw m3a companies bach nchoufou ownership
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM offres o
                INNER JOIN companies c ON o.company_id = c.id
                WHERE o.id = :offer_id AND c.user_id = :user_id
            ");
            $stmt->execute([
                'offer_id' => $offerId,
                'user_id' => $userId
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (\Exception $e) {
            error_log("Error verifying offer ownership: " . $e->getMessage());
            return false;
        }
    }

    // verification company ownership
    private function verifyCompanyOwnership($companyId, $userId)
    {
        try {
            $db = $this->getDbConnection();
            
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM companies
                WHERE id = :company_id AND user_id = :user_id
            ");
            $stmt->execute([
                'company_id' => $companyId,
                'user_id' => $userId
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (\Exception $e) {
            error_log("Error verifying company ownership: " . $e->getMessage());
            return false;
        }
    }

    // njibou db connection - kan checkew f service wla n créewha
    private function getDbConnection()
    {
        if (method_exists($this->jobOfferService, 'getDb')) {
            return $this->jobOfferService->getDb();
        }
        
        $database = new \App\Config\Database();
        return $database->getConnection();
    }
}