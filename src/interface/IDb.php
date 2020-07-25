<?php
interface IDb {
  public function setQuery($query);
  public function bindParams($params);
  public function exec();
}
