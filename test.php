<?php

function red($message) {
	echo "\e[31m".$message."\033[0m\n";
}
function yellow($message) {
	echo "\e[33m".$message."\033[0m\n";
}
function blue($message) {
	echo "\e[36m".$message."\033[0m\n";
}
function green($message) {
	echo "\e[32m".$message."\033[0m\n";
}

function upVersion () {
	$lastTag = shell_exec('git describe --abbrev=0');
echo $lastTag;
/*
	local major=`cut -d'.' -f1 <<<$lastTag | awk '{print ($0+0)}'`
	local minor=`cut -d'.' -f2 <<<$lastTag | awk '{print ($0+0)}'`
	local patch=`cut -d'.' -f3 <<<$lastTag | awk '{print ($0+0)}'`

	local nextPatch=$(($patch+1))
	local nextTag="${major}.${minor}.${nextPatch}"

	echo $nextTag
*/
}


upVersion();

echo red("test");
echo green("test");
echo yellow("test");
