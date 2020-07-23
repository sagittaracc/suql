<?php
interface IDb {
  public function setQuery($query);
  public function bindParams($params);
  public function fetchAll();
  public function fetchOne();
}
