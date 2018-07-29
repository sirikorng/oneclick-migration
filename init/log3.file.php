<?php

require_once('dupx/class.dupx.u.php');
require_once('dupx/class.dupx.log.php');

//FINAL RESULTS
$profile_end	= DUPX_U::getMicrotime();
$ajax2_end		= DUPX_U::getMicrotime();
$ajax1_sum		= DUPX_U::elapsedTime($ajax2_end, $ajax2_start);
DUPX_Log::info("\nCREATE/INSTALL RUNTIME: " . DUPX_U::elapsedTime($profile_end, $profile_start));
DUPX_Log::info('STEP-2 COMPLETE @ ' . @date('h:i:s') . " - RUNTIME: {$ajax1_sum}");

$ajax2_start = DUPX_U::getMicrotime();