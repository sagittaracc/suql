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
     * @return string шаблон преобразованный в html
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
            $data['id'] = $namespace;
            $html = self::parseTemplate($namespace, $tag, $data, $jsConfig);
            $js = self::generateJs($namespace, $jsConfig);
        }

        return $html . $js;
    }
    /**
     * Генерирует html из массива по схеме tsml
     * @param string $namespace заданная пользователем namespace блока
     * @param string $parent html tag родителя
     * @param array $children вложенные элементы в parent
     * @param array $jsConfig генерируемые по ходу связи между используемыми переменными шаблона и участками их в DOM
     * @return string html
     */
    private static function parseTemplate($namespace, $parent, $children, &$jsConfig)
    {
        $content = self::getContent($namespace, $children, $jsConfig);
        $attributes = self::getAttributes($namespace, $children);
        return Html::tag($parent, $attributes, $content);
    }
    private static function getAttributes($namespace, $children)
    {
        $list = [];
        $sgAttributes = [
            'sg-click' => 'onclick',
        ];

        foreach ($children as $key => $value) {
            if (is_string($value)) {
                $attribute = $key;
                if (isset($sgAttributes[$key])) {
                    $attribute = $sgAttributes[$key];
                    $value = "$namespace.$value";
                }

                $list[$attribute] = $value;
            }
        }

        return $list;
    }
    private static function getContent($namespace, &$children, &$jsConfig)
    {
        $html = '';

        foreach ($children as $key => $value) {
            if (is_array($value)) {
                if (empty($value)) {
                    if (preg_match('/\{\{\w+\}\}/', $key)) {
                        if (!isset($children['id'])) {
                            $id = uniqid();
                            $children['id'] = $id;
                        }
                        $jsConfig[str_replace(['{{', '}}'], '', $key)] = $children['id'];
                    }
                    else {
                        $html .= $key;
                    }
                }
                else {
                    $html .= self::parseTemplate($namespace, $key, $value, $jsConfig);
                }
            }
        }

        return $html;
    }
    private static function generateJs($namespace, $jsConfig)
    {
        $list = [];
        foreach ($jsConfig as $variable => $path) {
            $list[] = "$variable: {path: '$path',value: undefined}";
        }
        return Html::tag('script', ['type' => 'text/javascript'], "window.$namespace = {" . implode(',', $list) . "}");
    }
}