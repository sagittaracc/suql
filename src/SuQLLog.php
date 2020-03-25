<?php
class SuQLLog
{
  public static function error($suql, $index)
  {
    return (
      "<p style='font-family: courier;'>".
        substr($suql, 0, $index).
        "<span style='color: white; font-weight: bold; background: red;'>".
          $suql[$index].
        "</span>".
        substr($suql, $index + 1, strlen($suql)).
      "</p>"
    );
  }
}
