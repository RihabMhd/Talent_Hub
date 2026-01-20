<?php
namespace App\Controllers\Admin;

use App\Services\CategoryService;
use App\Models\Category;  

class CategoryController
{
    private CategoryService $categoryService;
    private $twig;

    public function __construct(CategoryService $categoryService, $twig = null)
    {
        $this->categoryService = $categoryService;
        $this->twig = $twig;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        
        $categoriesArray = array_map(function($category) {
            return $category->toArray();
        }, $categories);
        
        echo $this->twig->render('admin/category.html.twig', [
            'categories' => $categoriesArray,
            'current_user' => $_SESSION['user'] ?? null,
            'session' => $_SESSION ?? [],
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
    }

    public function show(int $id)
    {
        $category = $this->categoryService->getCategoryById($id);
        if (!$category) {
            http_response_code(404);
            return ['error' => 'Category not found'];
        }
        return $category;
    }

    public function store()
    {
        if (empty($_POST['nom'])) {
            $_SESSION['error'] = 'Category name is required';
            header('Location: /admin/categories');
            exit;
        }

        $categoryId = $this->categoryService->createCategory($_POST['nom']);
        
        if ($categoryId) {
            $_SESSION['success'] = 'Category created successfully';
        } else {
            $_SESSION['error'] = 'Failed to create category';
        }
        
        header('Location: /admin/categories');
        exit;
    }

    public function update(int $id)
    {
        if (empty($_POST['nom'])) {
            $_SESSION['error'] = 'Category name is required';
            header('Location: /admin/categories');
            exit;
        }

        $result = $this->categoryService->updateCategory($id, $_POST['nom']);
        
        if ($result) {
            $_SESSION['success'] = 'Category updated successfully';
        } else {
            $_SESSION['error'] = 'Category not found';
        }
        
        header('Location: /admin/categories');
        exit;
    }

    public function destroy(int $id)
    {
        $result = $this->categoryService->deleteCategory($id);
        
        if ($result) {
            $_SESSION['success'] = 'Category deleted successfully';
        } else {
            $_SESSION['error'] = 'Category not found';
        }
        
        header('Location: /admin/categories');
        exit;
    }
}