<?php
namespace App\Services;

use App\Models\Tag;  
use App\Repository\TagRepository;

class TagService
{
    private TagRepository $TagRepository;

    public function __construct(TagRepository $TagRepository)
    {
        $this->TagRepository = $TagRepository;
    }

    public function getAllTags(): array
    {
        $tagsData = $this->TagRepository->findAll();
        
        return array_map(function($data) {
            return Tag::fromArray($data);
        }, $tagsData);
    }

    public function getTagsById(int $id): ?Tag
    {
        $data = $this->TagRepository->findById($id);
        
        if (!$data) {
            return null;
        }
        
        return Tag::fromArray($data);
    }

    public function createTags(string $nom): ?int
    {
        return $this->TagRepository->create(['nom' => $nom]);
    }

    public function updateTags(int $id, string $nom): bool
    {
        return $this->TagRepository->update($id, ['nom' => $nom]);
    }

    public function deleteTags(int $id): bool
    {
        return $this->TagRepository->delete($id);
    }
}