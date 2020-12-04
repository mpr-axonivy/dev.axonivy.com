<?php

namespace app\domain\util;

class ArrayUtil
{
  public static function getLastElementOrNull(array $array)
  {
    if (empty($array)) {
      return null;
    }
    return end($array);
  }
}
