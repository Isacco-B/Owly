<?php
require_once __DIR__ . '/../controllers/SubjectController.php';

$router->map('GET', '/subjects', function () use ($subjectController) {
    $subjectController->getAllSubjects();
});

$router->map('GET', '/subjects/[i:id]', function ($id) use ($subjectController) {
    $subjectController->getSubjectsById($id);
});

$router->map('POST', '/subjects', function () use ($subjectController) {
    $data = json_decode(file_get_contents('php://input'));
    $subjectController->createSubjects($data);
});

$router->map('PUT', '/subjects/[i:id]', function ($id) use ($subjectController) {
    $data = json_decode(file_get_contents('php://input'));
    $subjectController->updateSubjects($id, $data);
});

$router->map('DELETE', '/subjects/[i:id]', function ($id) use ($subjectController) {
    $subjectController->deleteSubjects($id);
});
