<?php

namespace suql\annotation;

/**
 * Разбор аннотаций параметров прокси
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class ProxyAnnotation extends Annotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*\[Proxy\(url="(?<url>.*?)"\s*,\s*port=(?<port>\d+)\s*(,\s*user="(?<user>\w+)"\s*,\s*pass="(?<pass>\w+)")?\)\]/msi';
    const REGEX_CONFIG = '/#\s*\[ProxyConfig\(file="(?<file>.*?)"\s*,\s*parser="(?<parser>.*?)"\)\]/msi';
    /**
     * @var string
     */
    public $url = null;
    /**
     * @var int
     */
    public $port;
    /**
     * @var string
     */
    public $user = null;
    /**
     * @var string
     */
    public $pass = null;
    /**
     * @inheritdoc
     */
    public function read()
    {
        $matches = parent::readBy(self::REGEX);

        if (!empty($matches)) {
            $this->url = $matches['url'];
            $this->port = $matches['port'];

            if (!empty($matches['user'])) {
                $this->user = $matches['user'];
            }

            if (!empty($matches['pass'])) {
                $this->pass = $matches['pass'];
            }
        }
        else {
            $matches = parent::readBy(self::REGEX_CONFIG);

            if (!empty($matches)) {
                $file = $matches['file'];
                $parserClass = $matches['parser'];
                $parser = new $parserClass($file);
                $config = $parser->getProxy();

                if (!empty($config)) {
                    $this->url = "{$config['protocol']}://{$config['host']}";
                    $this->port = $config['port'];
                    $this->user = $config['user'];
                    $this->pass = $config['pass'];
                }
            }
        }

        return $this;
    }
}