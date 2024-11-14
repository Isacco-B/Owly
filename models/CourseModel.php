<?php
class CourseModel
{
    private $conn;
    private $table_name = "courses";
    private $course_subjects_table_name = "course_subjects";

    public $id;
    public $name;
    public $available_seats;
    public $subjects = [];

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll($filters)
    {
        $query = "SELECT c.*, GROUP_CONCAT(s.id) as subject_ids, GROUP_CONCAT(s.name) as subject_names
        FROM " . $this->table_name . " c
        LEFT JOIN course_subjects cs ON c.id = cs.course_id
        LEFT JOIN subjects s ON cs.subject_id = s.id";

        $where = [];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "c.name LIKE :name";
            $params[':name'] = "%" . $filters['name'] . "%";
        }

        if (!empty($filters['available_seats'])) {
            $where[] = "c.available_seats = :seats";
            $params[':seats'] = $filters['available_seats'];
        }

        if (!empty($filters['subject_ids'])) {
            $subject_ids = explode(",", $filters['subject_ids']);
            $placeholders = implode(',', array_map(fn($i) => ":subject_id_$i", array_keys($subject_ids)));
            $where[] = "c.id IN (
                        SELECT DISTINCT cs.course_id
                        FROM course_subjects cs
                        WHERE cs.subject_id IN ($placeholders)
                    )";

            foreach ($subject_ids as $index => $subject_id) {
                $params[":subject_id_$index"] = $subject_id;
            }
        }
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " GROUP BY c.id";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt;
    }

    public function getById()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO " . $this->table_name . " (name, available_seats) VALUES (:name, :seats)";
            $stmt = $this->conn->prepare($query);

            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->available_seats = htmlspecialchars(strip_tags($this->available_seats));

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":seats", $this->available_seats);

            $stmt->execute();
            $this->id = $this->conn->lastInsertId();

            foreach ($this->subjects as $subject_id) {
                $query = "INSERT INTO " . $this->course_subjects_table_name . " (course_id, subject_id) VALUES (:course_id, :subject_id)";
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(":course_id", $this->id);
                $stmt->bindParam(":subject_id", $subject_id);

                $stmt->execute();
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update()
    {
        $this->conn->beginTransaction();
        try {
            $query = "UPDATE " . $this->table_name . " SET name = :name, available_seats = :seats WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->available_seats = htmlspecialchars(strip_tags($this->available_seats));

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":seats", $this->available_seats);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            $query = "DELETE FROM " . $this->course_subjects_table_name . " WHERE course_id = :course_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":course_id", $this->id);
            $stmt->execute();

            foreach ($this->subjects as $subject_id) {
                $query = "INSERT INTO " . $this->course_subjects_table_name . " (course_id, subject_id) VALUES (:course_id, :subject_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":course_id", $this->id);
                $stmt->bindParam(":subject_id", $subject_id);
                $stmt->execute();
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete()
    {
        $this->conn->beginTransaction();
        try {
            $query = "DELETE FROM " . $this->course_subjects_table_name . " WHERE course_id = :id";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
