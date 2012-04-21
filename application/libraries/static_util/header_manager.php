<?php

class header_manager
{
    private static $instance;
    private
      $css_files = array(),
      $js_files = array(),
      $title = "Page Title Not Set";

    private function __construct()
    {
    }

    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function AddCssFile($css_file)
    {
        if (!in_array($css_file, $this->css_files)) {
          $this->css_files[] = $css_file;
        }
    }

    public function GetCssFileList()
    {
        return $this->css_files;
    }

    public function AddJsFile($js_file)
    {
      if (!in_array($js_file, $this->js_files)) {
        $this->js_files[] = $js_file;
      }
    }

    public function GetJsFileList()
    {
      return $this->js_files;
    }

    public function SetPageTitle($title)
    {
      $this->title = $title;
    }

    public function GetPageTitle()
    {
      return $this->title;
    }

    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
}

if ( ! function_exists('require_static'))
{
  function require_static($file)
  {
      header_manager::singleton()->AddCssFile($file);
  }
}

if ( ! function_exists('get_css_file_list'))
{
  function get_css_file_list()
  {
      return header_manager::singleton()->GetCssFileList();
  }
}

if ( ! function_exists('require_js'))
{
  function require_js($file)
  {
    header_manager::singleton()->AddJsFile($file);
  }
}

if ( ! function_exists('get_js_file_list'))
{
  function get_js_file_list()
  {
    return header_manager::singleton()->GetJsFileList();
  }
}

if ( ! function_exists('set_page_title'))
{
  function set_page_title($title)
  {
    header_manager::singleton()->SetPageTitle($title);
  }
}

if ( ! function_exists('get_page_title'))
{
  function get_page_title()
  {
    return header_manager::singleton()->GetPageTitle();
  }
}