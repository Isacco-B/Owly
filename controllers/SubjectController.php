<?php

require_once __DIR__ . "/../models/SubjectModel.php";
require_once __DIR__ . "/../utils/JsonResponse.php";

class SubjectController
{
    private $subject;

    public function __construct($db)
    {
        $this->subject = new SubjectModel($db);
    }


    public function getAllSubjects()
    {
        $filters = [];

        if (isset($_GET["name"])) $filters["name"] = $_GET["name"];

        try {
            $stmt = $this->subject->getAll($filters);
            if ($stmt->rowCount() > 0) {
                $subjects_arr = [];
                $subjects_arr["subjects"] = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $subject_item = [
                        "id" => $id,
                        "name" => $name,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                    array_push($subjects_arr["subjects"], $subject_item);
                }

                JsonResponse::send($subjects_arr);
            } else {
                JsonResponse::send(["subjects" => []], 404);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function getSubjectsById($id)
    {
        try {
            if (!empty($id)) {
                $this->subject->id = $id;

                $subject = $this->subject->getById();
                if ($subject->rowCount() > 0) {
                    JsonResponse::send($subject->fetch(PDO::FETCH_ASSOC));
                } else {
                    JsonResponse::send(["message" => "Subject not found."], 404);
                }
            } else {
                JsonResponse::send(["message" => "Unable to get subject. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function createSubjects($data)
    {
        try {
            if (!empty($data->name)) {
                $this->subject->name = $data->name;

                $this->subject->create();
                JsonResponse::send(["message" => "Subject created successfully."], 201);
            } else {
                JsonResponse::send(["message" => "Unable to create subject. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function updateSubjects($id, $data)
    {
        try {
            if (!empty($id) && !empty($data)) {
                $this->subject->id = $id;
                $this->subject->name = $data->name;

                $this->subject->update();
                JsonResponse::send(["message" => "Subject updated successfully."]);
            } else {
                JsonResponse::send(["message" => "Unable to update subject. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }

    public function deleteSubjects($id)
    {
        try {
            if (!empty($id)) {
                $this->subject->id = $id;

                if ($this->subject->delete()) {
                    JsonResponse::send(["message" => "Subject deleted successfully."]);
                } else {
                    JsonResponse::send(["message" => "Subject not found."], 404);
                }
            } else {
                JsonResponse::send(["message" => "Unable to delete subject. Data is incomplete."], 400);
            }
        } catch (Exception $e) {
            JsonResponse::send(["error" => $e->getMessage()], 500);
        }
    }
}
