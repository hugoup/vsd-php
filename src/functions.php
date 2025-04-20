<?php

use HugoUp\Vsd\VsDumper;

if (!function_exists('vsd')) {
	function vsd(...$vars): void
	{
		VsDumper::dump(...$vars);
	}
}
