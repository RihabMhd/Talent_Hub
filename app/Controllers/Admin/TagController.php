<?php

namespace App\Controllers\Admin;

use App\Services\TagService;
use App\Models\Tag;

class TagController
{
    private TagService $TagService;
    private $twig;

    public function __construct(TagService $TagService, $twig = null)
    {
        $this->TagService = $TagService;
        $this->twig = $twig;
    }

    public function index()
    {
        $tags = $this->TagService->getAllTags();

        $tagsArray = array_map(function ($tags) {
            return $tags->toArray();
        }, $tags);

        echo $this->twig->render('admin/tags.html.twig', [
            'tags' => $tagsArray,
            'current_user' => $_SESSION['user'] ?? null,
            'session' => $_SESSION ?? [],
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    public function show(int $id)
    {
        $tag = $this->TagService->getTagsById($id);
        if (!$tag) {
            http_response_code(404);
            return ['error' => 'Tag not found'];
        }
        return $tag;
    }

    public function store()
    {
        if (empty($_POST['nom'])) {
            $_SESSION['error'] = 'Tag name is required';
            header('Location: /admin/tags.html.twig');
            exit;
        }

        $TagId = $this->TagService->createTags($_POST['nom']);

        if ($TagId) {
            $_SESSION['success'] = 'Tag created successfully';
        } else {
            $_SESSION['error'] = 'Failed to create Tag';
        }

        header('Location: /admin/tags.html.twig');
        exit;
    }

    public function update(int $id)
    {
        if (empty($_POST['nom'])) {
            $_SESSION['error'] = 'Tag name is required';
            header('Location: /admin/tags.html.twig');
            exit;
        }

        $result = $this->TagService->updateTags($id, $_POST['nom']);

        if ($result) {
            $_SESSION['success'] = 'Tag updated successfully';
        } else {
            $_SESSION['error'] = 'Tag not found';
        }

        header('Location: /admin/tags.html.twig');
        exit;
    }

    public function destroy(int $id)
    {
        $result = $this->TagService->deleteTags($id);

        if ($result) {
            $_SESSION['success'] = 'Tag deleted successfully';
        } else {
            $_SESSION['error'] = 'Tag not found';
        }

        header('Location: /admin/tags.html.twig');
        exit;
    }
}
