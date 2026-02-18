<?php
class Task extends Model {
    public function all() {
        return $this->db->query("SELECT * FROM tasks ORDER BY id DESC");
    }

    public function find($id) {
        return $this->db->query("SELECT * FROM tasks WHERE id=$id")->fetch_assoc();
    }

    public function create($l,$d,$s,$due) {
        return $this->db->query(
            "INSERT INTO tasks VALUES (NULL,'$l','$d','$s','$due',NOW())"
        );
    }

    public function update($id,$l,$d,$s,$due) {
        return $this->db->query(
            "UPDATE tasks SET location='$l', task_description='$d', status='$s', due_date='$due' WHERE id=$id"
        );
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM tasks WHERE id=$id");
    }
}
