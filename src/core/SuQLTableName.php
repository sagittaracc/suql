<?php

namespace suql\core;

/**
 * Обработчик названия таблицы
 * Возможные варианты задания названия таблицы:
 * 
 *   1. table - строковое название таблицы
 *   2. [table => alias] - таблица с заданием alias для нее
 *   3. table@alias - строковый вариант задания таблицы с alias
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLTableName extends SuQLName
{
}
