<?php

namespace suql\syntax;

use sagittaracc\Html;
use suql\syntax\field\Field;
use suql\syntax\parser\Tsml;

/**
 * SuQL синтакс
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class SuQL
{
    /**
     * Разбор запроса
     * @param string файл с запросом
     * @param \syql\syntax\SuQLParser парсер
     * @return \suql\syntax\ActiveRecord
     */
    public static function query($file, SuQLParser $parser = null)
    {
        if (is_null($parser)) {
            $parser = new Tsml;
        }

        $json = $parser->parseFile($file);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            self::parseQuery($instance, $data, $parser);
        }

        $instance->as(pathinfo($file, PATHINFO_FILENAME));

        return $instance;
    }
    /**
     * Разбор значения данных в yaml запросе
     * @param \suql\syntax\ActiveRecord $instance текущий запрос
     * @param array $data данные по ключу
     * @param \suql\syntax\SuQLParser парсер
     */
    private static function parseQuery(&$instance, $data, $parser)
    {
        foreach ($data as $key => $value) {
            if ($key === '!buff') {
                $instance = $instance->buff();
            }
            else if (class_exists($key)) {
                $instance = $instance->join($key);
                self::parseQuery($instance, $value, $parser);
            }
            else if (file_exists($key)) {
                $instance->join(self::query($key, $parser));
            }
            else if (is_array($value)) {
                $instance->select([
                    new Field($key, $value)
                ]);
            }
            else {
                $instance->select([$key => $value]);
            }
        }
    }
    /**
     * Разбор шаблона
     * @param string файл с шаблоном
     * @param \syql\syntax\SuQLParser парсер
     * @return \suql\syntax\ActiveRecord
     */
    public static function template($file, SuQLParser $parser = null)
    {
        $html = '';
        $js = '';
        $jsConfig = [];

        if (is_null($parser)) {
            $parser = new Tsml;
        }

        $json = $parser->parseFile($file);

        foreach ($json as $root => $data) {
            $parts = explode('@', $root);
            $tag = $parts[0];
            $namespace = isset($parts[1]) ? $parts[1] : 'main';

            $html = Html::tag($tag, ['id' => $namespace], self::parseTemplate($namespace, $root, $data, $jsConfig));
            $js = Html::tag('script', ['type' => 'text/javascript'], self::generateJs($namespace, $jsConfig));
        }

        return $html . $js;
    }
    /**
     * Разбор данных в шаблоне
     * @param string $namespace
     * @param string $parent
     * @param array $children
     * @param array $jsConfig
     * @return string
     */
    private static function parseTemplate($namespace, $parent, $children, &$jsConfig)
    {
        $html = '';

        foreach ($children as $key => $value) {
            if (preg_match('/\{\{\$\w+\}\}/', $key)) {
                // $key - переменная
            }
            else if (is_array($value)) {
                // $key - тэг
                $html .= Html::tag($key, [], self::parseTemplate($namespace, $key, $value, $jsConfig));
            }
            else if (is_string($value)) {
                // $key - атрибут
            }
        }

        return $html;
    }
    /**
     * Генерация js
     * @param string $namespace
     * @param array $jsConfig
     * @return string
     */
    private static function generateJs($namespace, $jsConfig)
    {
        return '';
    }
}