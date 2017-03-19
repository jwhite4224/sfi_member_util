<?php

class SFI_Member_Util
{
    public function verify_remote_addr($remote_addr) 
    {
        $authorized = false;

        $whitelisted_ips = array('127.0.0.1');

        $message = date('Y-m-d H:i:s', time()).' Membership Verification Request originated from '.$remote_addr;
        
        $this->write_log($message);

        if ( in_array( $remote_addr, $whitelisted_ips ) ) {
            $authorized = true;
        }

        return $authorized;
    }

    public function verify_membership( $scc, $email, $ext_conn = null )
    {
        if ( isset( $ext_conn ) ) {
            $conn = $ext_conn;
        } else {
            $conn = $this->get_connection();
        }
        
        $results = false;

        if ( $conn ) {
            if ( $sql = $conn->prepare("SELECT expiration_date FROM member_list WHERE scc = ? AND email = ? ORDER BY id DESC LIMIT 1") ) {
            
                $sql->bind_param("ds", $scc, $email);
                $sql->execute();

                $sql->bind_result($results);

                $sql->fetch();
                $sql->close();
            }
        }
        $conn->close();

        return $results;
    }

    private function get_connection()
    {
        $host = "localhost";
        $username = "";
        $password = "";
        $dbname = "";

        $conn = new mysqli($host, $username, $password, $dbname);

        if ($conn->connect_error) {
            $conn = false;
        }
        
        return $conn;
    }

    private function write_log( $message, $log_file = 'sfi_util.log' )
    {
        if ( is_array($message) ) {
            error_log(print_r($message,true)."\n", 3, dirname(__FILE__)."/".$log_file);        
        } else {
            error_log($message."\n", 3, dirname(__FILE__)."/".$log_file);        
        }

        return;
    }

}

$member_util = new SFI_Member_Util();

$method = filter_input( INPUT_SERVER, 'REQUEST_METHOD');
$remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR');

if ( $method == 'POST' && $member_util->verify_remote_addr($remote_addr) ) {

    $scc = filter_input( INPUT_POST, 'scc', FILTER_VALIDATE_INT);
    $email = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ( !empty($scc) && !empty($email) ) {
    
        $results = $member_util->verify_membership( $scc, $email );
        echo json_encode($results);    
    }
}
