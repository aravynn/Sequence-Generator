<?php

/**
 *
 * Generic Functions. 
 *
 */
 
function FilterInt($i){
	return filter_var($i, FILTER_SANITIZE_NUMBER_INT);
}	  

function FilterString($i){
	return filter_var($i, FILTER_SANITIZE_STRING);
}