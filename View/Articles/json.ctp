<?php
if ($pay) {
	echo json_encode(array('free' => false, 'status' => 'ok', 'data' => $return));
} else {
	echo json_encode(array('free' => true, 'status' => 'ok', 'data' => $return));
}
