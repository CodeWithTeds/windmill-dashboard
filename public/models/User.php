<?php


class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $email;
    public $username;
    public $password;
    public $confirm_password;
    public $created_at;
    public $updated_at;



    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        if ($this->isValidData()) {
            $query = "INSERT INTO " . $this->table_name . "
                    (email, username, password, confirm_password)
                    VALUES
                    (:email, :username, :password, :confirm_password)";

            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->username = htmlspecialchars(strip_tags($this->username));

            // Hash passwords
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

            // Bind values
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":confirm_password", $hashed_password);

            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    private function isValidData()
    {
        $errors = [];

        if (empty($this->email)) {
            $errors[] = "Email cannot be empty.";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (empty($this->username)) {
            $errors[] = "Username cannot be empty.";
        }

        if (empty($this->password)) {
            $errors[] = "Password cannot be empty.";
        }

        if (empty($this->confirm_password)) {
            $errors[] = "Confirm password cannot be empty.";
        }

        if ($this->password !== $this->confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        if (strlen($this->password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }

        if ($this->emailExists()) {
            $errors[] = "Email already exists.";
        }

        if ($this->usernameExists()) {
            $errors[] = "Username already exists.";
        }

        // Modify the create() method to use these errors
        if (!empty($errors)) {
            // Store errors in a way the registration script can access
            $_SESSION['registration_errors'] = $errors;
            return false;
        }

        return true;
    }


    private function emailExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->email]);
        return $stmt->rowCount() > 0;
    }

    private function usernameExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->username]);
        return $stmt->rowCount() > 0;
    }

    public function login($email, $password)
    {
        // Query to check if email exists
        $query = "SELECT id, username, email, password 
                FROM " . $this->table_name . " 
                WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $email = htmlspecialchars(strip_tags($email));

        $stmt->bindParam(':email', $email);
        // Execute query
        $stmt->execute();

        // Get number of rows
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (password_verify($password, $row['password'])) {

                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                return true;
            }
        }

        return false;
    }

    //data table for my admin side

    public function getUsers()
    {
        try {
            $query = "SELECT id, username, email, created_at FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo "Database error" . $e->getMessage();
            return null;
        }
    }


    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("getUserById result: " . print_r($result, true));

            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getUserById: " . $e->getMessage());
            return null;
        }
    }

    public function updateUser($id, $username, $email)
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, email = :email, updated_at = NOW() 
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $username = htmlspecialchars(strip_tags($username));
            $email = htmlspecialchars(strip_tags($email));

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    }

    public function deleteUser($id)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    }

    public function updateUserProfile($userId, $data)
    {
        try {
            // Debug: Print incoming data
            error_log("Updating profile for user ID: " . $userId);
            error_log("Update data: " . print_r($data, true));

            $query = "UPDATE " . $this->table_name . " 
                  SET username = :username,
                      email = :email,
                      phone_number = :phone_number,
                      address = :address,
                      city = :city,
                      zip_code = :zip_code,
                      updated_at = NOW()
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone_number', $data['phone_number']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':zip_code', $data['zip_code']);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

            $result = $stmt->execute();

            // Debug: Print result
            error_log("Update result: " . ($result ? "success" : "failed"));
            if (!$result) {
                error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    public function createAdmin()
    {
        $hashed_password = password_hash('Admin123!', PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, password, is_admin) 
                  VALUES (:username, :email, :password, 1)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':username', 'admin', PDO::PARAM_STR);
        $stmt->bindValue(':email', 'admin@.com', PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
