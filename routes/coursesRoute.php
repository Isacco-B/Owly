<?php
require_once __DIR__ . '/../controllers/CourseController.php';

$router->map('GET', '/courses', function () use ($courseController) {
    $courseController->getAllCourses();
});

$router->map('GET', '/courses/[i:id]', function ($id) use ($courseController) {
    $courseController->getCourseById($id);
});

$router->map('POST', '/courses', function () use ($courseController) {
    $data = json_decode(file_get_contents('php://input'));
    $courseController->createCourse($data);
});

$router->map('PUT', '/courses/[i:id]', function ($id) use ($courseController) {
    $data = json_decode(file_get_contents('php://input'));
    $courseController->updateCourse($id, $data);
});

$router->map('DELETE', '/courses/[i:id]', function ($id) use ($courseController) {
    $courseController->deleteCourse($id);
});
