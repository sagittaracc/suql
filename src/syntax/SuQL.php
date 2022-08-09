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

        $template = $parser->parseFile($file);

        foreach ($template as $root => $view) {
            list($rootElement, $namespace) = explode('@', $root);
            $view['id'] = $namespace;
            $html = self::parseTemplate($namespace, $rootElement, $view, $jsConfig);
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
        $attributes = self::getAttributes($namespace, $children, $jsConfig);
        return
            is_null($parent)
                ? $content
                : Html::tag($parent, $attributes, $content);
    }
    /**
     * Получает класс html элемента в dom
     * @param array $dom
     * @return string
     */
    private static function attachClassToElement(&$dom)
    {
        $class = isset($dom['class']) ? $dom['class'] : uniqid();
        $dom['class'] = $class;
        return $class;
    }
    /**
     * Получает все атрибуты
     * @param string $namespace
     * @param array $children вложенные элементы (атрибуты и контент вместе)
     * @param array $jsConfig генерируемые по ходу связи между используемыми переменными шаблона и участками их в DOM
     * @return array массив атрибутов (сырых и конвертированных из спец атрибутов sg)
     */
    private static function getAttributes($namespace, $children, &$jsConfig)
    {
        $list = [];
        $sgAttributes = [
            'sg-click' => function ($namespace, $value) {
                return ['onclick' => "$namespace.$value"];
            },
            'sg-model' => function ($namespace, $value) use ($children, &$jsConfig) {
                $class = self::attachClassToElement($children);
                self::addJsVariable($jsConfig, $value, "$namespace>$class");
                return [
                    'onkeyup' => "assign($namespace.$value, this.value)",
                    'class' => $class,
                ];
            },
        ];

        foreach ($children as $key => $value) {
            if (is_string($value)) {
                if (isset($sgAttributes[$key])) {
                    $list = array_merge($list, $sgAttributes[$key]($namespace, $value));
                }
                else {
                    $list[$key] = $value;
                }
            }
        }

        return $list;
    }
    /**
     * Генерирует html контент
     * @param string $namespace основной namespace
     * @param array $children все дочерние элементы из которых генерируем
     * @param array $jsConfig будущая конфигурация для генерации js
     * @return string html
     */
    private static function getContent($namespace, &$children, &$jsConfig)
    {
        $html = '';

        foreach ($children as $key => $value) {
            // Template variable
            if (preg_match('/\{\{(\w+)\}\}/', $key, $matches)) {
                $class = self::attachClassToElement($children);
                $template = !empty($value) ? self::parseTemplate($namespace, null, $value, $jsConfig) : null;
                $variable = $matches[1];
                self::addJsVariable($jsConfig, $variable, "$namespace>$class", $template);
                $html = '';
            }
            // Template function
            else if (preg_match('/\{\{\w+\(\)\}\}/', $key)) {
                $class = self::attachClassToElement($children);
                $template = !empty($value) ? self::parseTemplate($namespace, null, $value, $jsConfig) : null;
            }
            else {
                if (is_array($value)) {
                    if (empty($value)) {
                        $html .= $key;
                    }
                    else {
                        $html .= self::parseTemplate($namespace, $key, $value, $jsConfig);
                    }
                }
            }
        }

        return $html;
    }
    /**
     * Добавляет js template variable
     * @param array $jsConfig будущая конфигурация для генерации js
     * @param string $variable
     * @param string $path
     * @param string $template
     */
    private static function addJsVariable(&$jsConfig, $variable, $path = null, $template = null)
    {
        if (!isset($jsConfig[$variable])) {
            $jsConfig[$variable] = [
                'value' => null,
                'paths' => [],
            ];
        }

        if (!is_null($path)) {
            $jsConfig[$variable]['paths'][$path] = [
                'format' => 'raw',
            ];
        }

        if (!is_null($template)) {
            $jsConfig[$variable]['paths'][$path] = [
                'format' => 'html',
                'template' => $template,
            ];
        }
    }
    /**
     * Генерация дополнительного js
     * @param string $namespace основной namespace
     * @param array $jsConfig конфигурация для js
     * @return string script tag
     */
    private static function generateJs($namespace, $jsConfig)
    {
        $list = [];
        return Html::tag('script', ['type' => 'text/javascript'], "window.$namespace = " . json_encode($jsConfig));
    }
}