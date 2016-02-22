function debug_log( $msg, $file = '/var/log/httpd/debug.txt' ) {
   $msg = gmdate( 'Y-m-d H:i:s' ) . ' ' . print_r( $msg, TRUE ) . "n";
   error_log( $msg, 3, $file );
}
