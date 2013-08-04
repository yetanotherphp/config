<?php

namespace YetAnother\Config;

class Config implements \ArrayAccess
{
    protected $configPath;
    protected $isDir = false;
    protected $config = array();

    public function __construct($configPathOrArray = array())
    {
        if (is_array($configPathOrArray)) {
            $this->config = $configPathOrArray;
        } else {
            $this->configPath = $configPathOrArray;
            $this->isDir = is_dir($this->configPath);
            if (!$this->isDir) $this->load();
        }
    }

    public function get($key)
    {
        if ($this->isDir && !isset($this->config[$key])) {
            $this->load($key);
        }
        return $this->config[$key];
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function has($key)
    {
        if ($this->isDir && !isset($this->config[$key])) {
            return file_exists($this->getConfigPath($key));
        }
        return isset($this->config[$key]);
    }

    public function remove($key)
    {
        unset($this->config[$key]);
    }

    public function all()
    {
        if ($this->isDir) {
            if ($dir = opendir($this->configPath)) {
                while (false !== ($filename = readdir($dir))) {
                    if ($filename != '.' && $filename != '..') {
                        $filepath = $this->configPath . DIRECTORY_SEPARATOR . $filename;
                        if (!is_dir($filepath) && $this->isPhpPath($filename)) {
                            $key = basename($filename, '.php');
                            if (!isset($this->config[$key])) {
                                $this->load($key);
                            }
                        }
                    }
                }
                closedir($dir);
            }
        }
        return $this->config;
    }

    protected function load($key = '')
    {
        $fileConfig = $this->import($key);

        if ($key) {
            $this->config[$key] = $fileConfig;
        } else {
            $this->config = $fileConfig;
        }
    }

    protected function import($key)
    {
        $path = $this->isPhpPath($key) ? $key : $this->getConfigPath($key);

        if (!file_exists($path)) {
            throw new ConfigException("File $path not found");
        }
        if (1 === $fileConfig = include($path)) {
            unset($key, $path, $fileConfig);
            $fileConfig = get_defined_vars();
        }
        return $fileConfig;
    }

    protected function isPhpPath($key)
    {
        return strcasecmp(substr($key, -4), '.php') === 0;
    }

    protected function getConfigPath($key = '')
    {
        if ($key) {
            if ($this->isDir) {
                return $this->configPath . DIRECTORY_SEPARATOR . $key . '.php';
            } else {
                return dirname($this->configPath) . DIRECTORY_SEPARATOR . $key . '.php';
            }
        } else {
            return $this->configPath;
        }
    }

    /********** ArrayAccess functions **********/

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    public function offsetExists($key)
    {
        return $this->has($key);
    }

    public function offsetUnset($key)
    {
        $this->remove($key);
    }
}