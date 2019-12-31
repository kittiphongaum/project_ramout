<?php
class http_response {
    public $http_respond_code = array(
        "200"=>array(
            "code"=>"200",
            "name"=>"Success"
        ),
        "201"=>array(
            "code"=>"201",
            "name"=>"Created"
        ),
        "202"=>array(
            "code"=>"202",
            "name"=>"Accepted"
        ),
        "204"=>array(
            "code"=>"204",
            "name"=>"No Content"
        ),
        "400"=>array(
            "code"=>"400",
            "name"=>"Bad Request"
        ),
        "401"=>array(
            "code"=>"401",
            "name"=>"Unauthorized"
        ),
        "403"=>array(
            "code"=>"403",
            "name"=>"Forbidden"
        ),
        "404"=>array(
            "code"=>"404",
            "name"=>"NotFound"
        ),
        "500"=>array(
            "code"=>"500",
            "name"=>"Internal Server Error"
        ),
        "503"=>array(
            "code"=>"503",
            "name"=>"Service Unavailable"
        ),
        "504"=>array(
            "code"=>"504",
            "name"=>"Timeout"
        ),
    );

    public function print_error($code) {
        $response_data = array();
        $response_data["error_code"] = $this->http_respond_code[$code]["code"];
        $response_data["error_message"] = $this->http_respond_code[$code]["name"];
        echo json_encode($response_data, JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>