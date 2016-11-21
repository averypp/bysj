<?php
function bar($items){
	for ($i=0; $i < count($items); $i++) { 
		if(isInt($items[$i])){
			echo 10*20*$items[$i];
		}
	}
}
function isInt($value){
	if(is_int($value)){
		return true;
	} else {
		return false;
	}
}
$ints = array(1,2,'E',4,5,6,'T',8,9,'O');
$ints2 = array(0,1,2,3,4,5,6,7,8,9);
bar($ints);
bar($ints2);
echo "DONE";
?>
