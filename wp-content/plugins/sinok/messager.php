<?php


//namespace Sinok;

class Messager
{
    
    private $_errors = array();
    
    private $_infos = array();
    
    private $_warnings = array();
    
    private $_debugs = array();

    public function __construct() {
        if ( isset($_SESSION['snk_messager']) && is_array($_SESSION['snk_messager']) ) {
            $this->_errors = ( isset($_SESSION['snk_messager']['_errors']) && is_array($_SESSION['snk_messager']['_errors']) ) ? $_SESSION['snk_messager']['_errors'] : array();
            $this->_infos = ( isset($_SESSION['snk_messager']['_infos']) && is_array($_SESSION['snk_messager']['_infos']) ) ? $_SESSION['snk_messager']['_infos'] : array();
            $this->_warnings = ( isset($_SESSION['snk_messager']['_warnings']) && is_array($_SESSION['snk_messager']['_warnings']) ) ? $_SESSION['snk_messager']['_warnings'] : array();
            $this->_debugs = ( isset($_SESSION['snk_messager']['_debugs']) && is_array($_SESSION['snk_messager']['_debugs']) ) ? $_SESSION['snk_messager']['_debugs'] : array();
        }
    }
    
    function __destruct() {
        $this->delete(TRUE);
    }

    public function set($type = 'error', $msg) {
        
        $type = (string) $type;
        $msg = (string) $msg;
        
        switch($type) {
            case 'debug':
                $_SESSION['snk_messager']['_debugs'] = $this->_debugs[] = $msg;
                break;
            case 'warning':
                $_SESSION['snk_messager']['_warnings'] = $this->_warnings[] = $msg;
                break;
            case 'info':
                $_SESSION['snk_messager']['_infos'] = $this->_infos[] = $msg;
                break;
            case 'error':
            default:
                $_SESSION['snk_messager']['_errors'] = $this->_errors[] = $msg;
                break;
        }
        
        return array();
    }
    
    public function get($type) {
        switch($type) {
            case 'debug':
                return $this->_debugs;
                break;
            case 'warning':
                return $this->_warnings;
                break;
            case 'info':
                return $this->_infos;
                break;
            case 'error':
            default:
                return $this->_errors;
                break;
        }
    }
    
    private function delete($type) {
        
        switch($type) {
            case 'debug':
                $_SESSION['snk_messager']['_debugs'] = array();
                break;
            case 'warning':
                $_SESSION['snk_messager']['_warnings'] = array();
                break;
            case 'info':
                $_SESSION['snk_messager']['_infos'] = array();
                break;
            case 'error':
                $_SESSION['snk_messager']['_errors'] = array();
                break;
            case TRUE: 
            default:
                $_SESSION['snk_messager']['_debugs'] = $_SESSION['snk_messager']['_warnings'] = $_SESSION['snk_messager']['_infos'] = $_SESSION['snk_messager']['_errors'] = array();
                break;
        }
        
    }
}
