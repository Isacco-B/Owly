<?php

require_once __DIR__ . "/../models/CourseModel.php";
require_once __DIR__ . "/../utils/JsonResponse.php";

class CourseController
{
    private $course;

    public function __construct($db)
    {
        $this->course = new CourseModel($db);
    }

    public function getAllCourses()
    {
        $filters = [];

        if (isset($_GET['name'])) $filters['name'] = $_GET['name'];
        if (isset($_GET['available_seats'])) $filters['available_seats'] = $_GET['available_seats'];
        if (isset($_GET['subject_ids'])) $filters['subject_ids'] = $_GET['subject_ids'];

        try {
            $stmt = $this->course->getAll($filters);
            if ($stmt->rowCount() > 0) {
                $courses_arr = [];
                $courses_arr["courses"] = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $subject_ids = explode(',', $row['subject_ids']);
                    $subject_names = explode(',', $row['subject_names']);
                    $subjects = [];
                    if (count($subject_ids) >= 1) {
                        foreach ($subject_ids as $key => $id) {
                            if (!empty($id) || !empty($subject_names[$key])) {
                                $subjects[] = [
                                    "id" => $id,
                                    "name" => $subject_names[$key]
                                ];
                            };
                        }
                    }

                    $course_item = [
                        "id" => $row['id'],
                        "name" => $row['name'],
                        "available_seats" => $row['available_seats'],
                        "subjects" => $subjects,
                        "created_at" => $row['created_at'],
                        "updated_at" => $row['updated_at']
                    ];
                    array_push($courses_arr["courses"], $course_item);
                }

                JsonResponse::send($courses_arr);
            } else {
                JsonResponse::send(["courses" => []], 404);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function getCourseById($id)
    {
        try {
            if (!empty($id)) {
                $this->course->id = $id;

                $course = $this->course->getById();
                if ($course->rowCount() > 0) {
                    JsonResponse::send($course->fetch(PDO::FETCH_ASSOC));
                } else {
                    JsonResponse::send(["message" => "Course not found."], 404);
                }
            } else {
                JsonResponse::send(["message" => "Unable to get course. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function createCourse($data)
    {
        try {
            if (!empty($data->name) && !empty($data->available_seats) && !empty($data->subjects)) {
                $this->course->name = $data->name;
                $this->course->available_seats = $data->available_seats;
                $this->course->subjects = $data->subjects;

                $this->course->create();
                JsonResponse::send(["message" => "Course created successfully."], 201);
            } else {
                JsonResponse::send(["message" => "Unable to create course. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function updateCourse($id, $data)
    {
        try {
            if (!empty($id) && !empty($data->name) && !empty($data->available_seats)) {
                $this->course->id = $id;
                $this->course->name = $data->name;
                $this->course->available_seats = $data->available_seats;
                $this->course->subjects = $data->subjects;

                $this->course->update();
                JsonResponse::send(["message" => "Course updated successfully."]);
            } else {
                JsonResponse::send(["message" => "Unable to update course. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function deleteCourse($id)
    {
        try {
            if (!empty($id)) {
                $this->course->id = $id;

                $this->course->delete();
                JsonResponse::send(["message" => "Course deleted successfully."]);
            } else {
                JsonResponse::send(["message" => "Unable to delete course. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }
}
