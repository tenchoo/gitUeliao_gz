<?php
/**
 * 与图片匹配伺服器进行数据交互
 */
class ispy {

    private $_socket;
    private $_error = false;

    public function __construct( $host='192.168.1.230', $port=12306 ) {
        set_time_limit( 0 );

        $this->_socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
        $result        = socket_connect( $this->_socket, $host, $port );

        if($result === false) {
            $this->_error = "socket_connect() failed.\nReason: ($result) " . socket_strerror( socket_last_error( $socket ) ) . "\n";
        }
    }

    /**
     * 向伺服器发送请求，请求对图片进行匹配搜索
     * @param string $uuid 上传的图片id
     * @return array
     */
    public function fetch($uuid) {
        $recv = "";
        $cmd  = json_encode( array( "uuid"=>$uuid ) );

        socket_write( $this->_socket, $cmd, strlen($cmd) );
        
        while ( $out = socket_read( $this->_socket, 32 ) ) {
            $recv .= $out;
        }
        return json_decode( $recv );
    }

    /**
     * 关闭与伺服器连接
     */
    public function __destruct() {
        socket_close( $this->_socket );
    }

    /**
     * 获取错误信息
     */
    public function getErrors() {
        return $this->_error;
    }
}