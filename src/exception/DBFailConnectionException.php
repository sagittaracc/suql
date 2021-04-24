<?php

namespace suql\exception;

/**
 * Возникает в случае неудачного подключения к базе данных
 * когда её не существует или она не определена для модели
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class DBFailConnectionException extends \Exception
{
}
