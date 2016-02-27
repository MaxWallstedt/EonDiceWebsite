<?php
function my_rand($min = 0, $max = 0x7FFFFFFF) {
	$diff = $max - $min;

	if ($diff < 0 || $diff > 0x7FFFFFFF) {
		throw new RuntimeException("Bad range");
	}

	$rndfp = fopen("/dev/urandom", "rb");
	$data = fread($rndfp, 4);
	$arr = unpack("Nint", $data);
	$val = $arr["int"] & 0x7FFFFFFF;
	$fp = (float)$val / 2147483647.0;

	return round($fp * $diff) + $min;
}
?>
