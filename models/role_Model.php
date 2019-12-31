<?php
class role_Model
{

    // database connection and table name
    private $conn;

    // object properties
    public $box_code;
    public $menu_code;
    public $box_name;
    public $ord;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;
    public $active_status;

    public $emp_id;
    public $username;
    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn       = $db;
        $this->req_status = 0;
    }

    public function get_role_page()
    {
        $query = "SELECT DISTINCT box.* FROM v_employee_profile
                  JOIN trans_permission_group_employee_mapping as emp_mp ON emp_mp.emp_id = v_employee_profile.emp_id
                  JOIN trans_permission_group_menu_mapping as group_mp ON emp_mp.group_code = group_mp.group_code
                  JOIN db_permission_box as box ON group_mp.box_code = box.box_code
                  JOIN db_permission_group AS group_mas ON group_mas.group_code = group_mp.group_code
                  WHERE group_mas.active_status = '1' AND emp_mp.emp_id = :emp_id AND menu_code = :menu_code AND box.active_status = 1";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":emp_id", $this->emp_id);
        $stmt->bindParam(":menu_code", $this->menu_code);


        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function get_parent_role_page()
    {
        $query = "SELECT * FROM db_permission_menu WHERE menu_code = :menu_code";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":menu_code", $this->menu_code);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function get_role_menu()
    {
        $query = "SELECT
                        pm.menu_code,
                        pm.menu_name,
                        pm.img,
                        pm.icon,
                        pm.path,
                        pm.active_code,
                        COUNT( pgmm.box_code ) AS total_on 
                    FROM
                        trans_permission_group_employee_mapping AS pgem
                        LEFT JOIN trans_permission_group_menu_mapping AS pgmm ON pgmm.group_code = pgem.group_code
                        LEFT JOIN db_permission_box AS pb ON pb.box_code = pgmm.box_code
                        LEFT JOIN db_permission_menu AS pm ON pm.menu_code = pb.menu_code
                    WHERE
                        pm.menu_code NOT IN ('M000', 'M999')
                        AND pgem.emp_id = :emp_id
                        AND EXISTS (SELECT * FROM db_permission_group as pg WHERE pg.group_code = pgmm.group_code AND pg.active_status = '1')
                    GROUP BY
                        pm.menu_code,
                        pm.menu_name,
                        pm.img,
                        pm.icon,
                        pm.path,
                        pm.active_code";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":emp_id", $this->emp_id);


        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function get_role_mypoint()
    {
        $query = "SELECT * FROM tb_User_Backend WHERE LOWER(Username) = :username ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":username", $this->username);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

}
