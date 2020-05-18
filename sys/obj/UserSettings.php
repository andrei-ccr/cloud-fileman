<?php
class UserSettings extends Connection{

    private int $user_id;

    /* Define settings list below */
    private bool $show_context_menu;

    
    public function __construct(int $user_id, string $password) {
        Connection::__construct();

        try {
            $stmt = $this->conn->prepare("SELECT * FROM settings WHERE user_id=:uid");
            $stmt->bindValue(":uid", $user_id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row !== false) {
                $this->user_id = (int)$user_id;

                /* Assign settings values below this line */
                $this->show_context_menu = (bool)$row['show_context_menu'];

            }
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /* Add settings getters and setters below */
    
    public function SetShowContextMenu(bool $value) {
        try {
            $stmt = $this->conn->prepare("UPDATE settings SET show_context_menu=:val WHERE user_id=:uid");
            $stmt->bindParam(":uid", $this->user_id);
            $stmt->bindParam(":val", $value, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->show_context_menu = (bool)$value;

    }
    public function GetShowContextMenu() : bool {
        return $this->show_context_menu;
    }
}
?>