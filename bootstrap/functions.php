<?php

/**
 * return arrays combination
 * (return all possible combinations of selecting $num of element from an $arr )
 * @param  array $arr
 * @param  int $num number of elements to be selected
 * @return [combination1,combination2....]
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
