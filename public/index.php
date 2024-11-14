<?php
require_once  __DIR__ . '/../libs/AltoRouter.php';
require_once  __DIR__ . '/../controllers/SubjectController.php';
require_once  __DIR__ . '/../controllers/CourseController.php';
require_once  __DIR__ . '/../config/Database.php';
require_once  __DIR__ . '/../utils/JsonResponse.php';

$router = new AltoRouter();
$router->setBasePath('/api');

$database = new Database();
$db = $database->getConnection();

$subjectController = new SubjectController($db);
$courseController = new CourseController($db);

require_once __DIR__ . '/../routes/subjectsRoute.php';
require_once __DIR__ . '/../routes/coursesRoute.php';

$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    JsonResponse::send(['message' => 'Route not found'], 404);
}
