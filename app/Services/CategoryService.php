<?php
namespace App\Services;

use App\Models\Category;  
use App\Repository\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): array
    {
        $categoriesData = $this->categoryRepository->findAll();
        
        return array_map(function($data) {
            return Category::fromArray($data);
        }, $categoriesData);
    }

    public function getCategoryById(int $id): ?Category
    {
        $data = $this->categoryRepository->findById($id);
        
        if (!$data) {
            return null;
        }
        
        return Category::fromArray($data);
    }

    public function createCategory(string $nom): ?int
    {
        return $this->categoryRepository->create(['nom' => $nom]);
    }

    public function updateCategory(int $id, string $nom): bool
    {
        return $this->categoryRepository->update($id, ['nom' => $nom]);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}