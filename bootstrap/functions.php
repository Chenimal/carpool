<?php

/**
 * return array's combination
 * (all possible combinations of selecting $num of elements from an $arr )
 * @param  array $arr
 * @param  int $num number of elements to be selected
 * @return array [combination1,combination2....]
 */
function math_combination($arr, $num)
{
    if ($num == 0) {
        return [[]];
    }
    $result = [];
    for ($i = 0; $i < count($arr); $i++) {
        $subs = math_combination(array_slice($arr, $i + 1), $num - 1);
        foreach ($subs as $s) {
            $result[] = array_merge([$arr[$i]], $s);
        }
    }
    return $result;
}

/**
 * return array's sequence
 * (all possible sequences of given array)
 * (each element of the array has start& end point, and each element's start point can't after its end point)
 * @param  array $arr. e.g. [[elt1_start, elt1_end], [elt2_start,elt2_end]...]
 * @return array of sequence. e.g. [elt1_start, elt2_start, elt2_end, elt1_end...]
 */
function math_sequence($arr)
{
    if (count($arr) == 0) {
        return [[]];
    }
    $result = [];
    foreach ($arr as $key => $pair) {
        $select_point = array_shift($pair);

        $rest_arr = array_except($arr, $key);
        $rest_arr = empty($pair) ? $rest_arr : array_merge($rest_arr, [$pair]);

        $sub_sequences = math_sequence($rest_arr);
        foreach ($sub_sequences as $sub) {
            array_unshift($sub, $select_point);
            $result[] = $sub;
        }
    }
    return $result;
}
