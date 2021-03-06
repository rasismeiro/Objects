<?php
/**
 * @package API
 * @author Ricardo Sismeiro
 * @version 1.0.0
 * @copyright Copyright (c) 2011, Ricardo Sismeiro.
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE
 */

class Api
{

  private static $_instance;

  public static function plugin($obj='')
  {
    $class = __CLASS__;
    if (!isset(self::$_instance)) {
      self::$_instance = new $class;
    }
    if (!is_array($obj)) {
      $obj = array($obj);
    }
    foreach ($obj as $key => $value) {
      if (is_numeric($key)) {
        if (is_string($value)) {
          if (!isset(self::$_instance->$value)) {
            self::$_instance->$value = $value;
          }
        } elseif (is_object($value)) {
          $class = get_class($value);
          if (!isset(self::$_instance->$class)) {
            self::$_instance->$class = $value;
          }
        }
      } else {
        if (!isset(self::$_instance->$key)) {
          self::$_instance->$key = $value;
        }
      }
    }
    return self::$_instance;
  }

  public function __set($name, $value)
  {
    if (is_string($value) && class_exists($value)) {
      $this->$name = new $value();
    } else {
      $this->$name = $value;
    }
    return $this->$name;
  }

  public function __get($name)
  {
    if (!isset($this->$name)) {
      if (class_exists($name)) {
        $this->$name = new $name();
        return $this->$name;
      } else {
        return false;
      }
    } else {
      return $this->$name;
    }
  }

  public function __call($name, $arguments)
  {

    if (!method_exists($this, $name) && !isset($this->$name)) {
      if (class_exists($name)) {
        if (!empty($arguments)) {
          $arg = '';
          foreach ($arguments as $k => $var) {
            $k = 'v' . $k;
            $$k = $var;
            $arg.='$' . $k . ',';
          }
          $arg = substr($arg, 0, -1);
          eval('$this->$name = new $name(' . $arg . ');');
        } else {
          $this->$name = new $name();
        }
        return $this->$name;
      }
    } elseif (is_object($this->$name)) {
      return $this->$name;
    }
  }

}
 
