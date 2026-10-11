<?php
function validate_positive_int($val) {
    $val = filter_var($val, FILTER_VALIDATE_INT);
    return ($val !== false && $val > 0) ? $val : false;
}

function validate_non_negative_number($val) {
    $val = filter_var($val, FILTER_VALIDATE_FLOAT);
    return ($val !== false && $val >= 0) ? $val : false;
}