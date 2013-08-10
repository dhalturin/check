<?
if(!defined('check')) die(' .. fuck!');

class tpl
{
    var $content;

    function set($s, $r)
    {
        $this->content = str_replace('{%' . $s . '%}', $r, $this->content);
    }

    function create()
    {
        $this->content = <<<html
<!DOCTYPE html>
<html>
  <head>
    <title>{%title%}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="http://yandex.st/jquery/1.7.1/jquery.js"></script>
    <script type="text/javascript" src="./data/js/main.js?{%date%}"></script>
    <link type="text/css" rel="stylesheet" href="./data/css/main.css?{%date%}" />
  </head>
  <body>
    <script type="text/javascript">
      $(document).ready(function(){
        autoRun();
      });
    </script>
{%content%}
  </body>
</html>
html;
    }

    function compile()
    {
        print($this->content);
    }
}

$tpl = new tpl;