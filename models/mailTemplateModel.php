<?php
class mailTemplateModel
{
    // database connection and table name
    private $conn;

    // object properties
    public $mail_template_code;
    public $mail_subject;
    public $mail_body;
    public $created_date;
    public $updated_date;


    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;

    }

    public function get()
    {
        $query = "select * from db_mail_template where 1=1";

        if (isset($this->mail_template_code)) {
            $query = $query . "and mail_template_code = :mail_template_code";
        }

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":mail_template_code", $this->mail_template_code);
        try {
            $stmt->execute();

            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // set values to object properties
                $this->mail_template_code = $row['mail_template_code'];
                $this->mail_subject = $row['mail_subject'];
                $this->mail_body = $row['mail_body'];
                $this->created_date = $row['created_date'];

            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
