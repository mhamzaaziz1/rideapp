<?php
ob_start();
// set_time_limit(1);
function fatal_error() {
   // trigger timeout
   while(true) {}
}
fatal_error();
