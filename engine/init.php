<?
if(!defined('check')) die(' .. fuck!');

require_once(root_dir . '/engine/template.class.php');
require_once(root_dir . '/engine/mysqli.class.php');

$_content = '';

/*
    start authorized
*/
if(empty($_COOKIE['_pass']))
{
    if($_POST)
    {
        if(md5($_POST['pass']) == md5($cfg['pass']))
        {
            setcookie('_pass', md5($_POST['pass']), (time() + (60 * 60 * 24 * 365)));
        }
        header('location: ?');
    }
    else
    {
        $_content = <<<html
<div class="login">
  <form method="post" action="">
    <input type="password" name="pass" value="" />
  </form>
</div>
html;
    }
}
else
{
    if($_COOKIE['_pass'] != md5($cfg['pass']))
    {
        setcookie('_pass', null);
        header('location: ?do=logout');
    }

    $_content = <<<html
<div class="menu">
  <a href="?do=add">Добавить хост</a>
  <a href="?do=logout">Выйти</a>
</div>
html;

if($_GET['do'] == 'logout')
{
    setcookie('_pass', null);
    header('location: ?');
}
else
if($_GET['do'] == 'add')
{
    if(!empty($_POST['host']) && !empty($_POST['proto']))
    {
        if(count($db->select(array(
            'table' => 'data',
            'field' => array('id'),
            'where' => array(
                array('host', $_POST['host']),
                array('proto', $_POST['proto'])
            ),
            #'debug' => true
        ))))
        {
            $_content.= 'Запись с таким хостом и портом уже есть';
        }
        else
        {
            if($db->insert(array(
                'table' => 'data',
                'field' => array(
                    array('host', $_POST['host']),
                    array('proto', $_POST['proto']),
                    array('interval', $_POST['interval']),
                    array('date_add', time()),
                    array('date_check', 0),
                    array('state', 0),
                    array('status', 'не проверялся')
                ),
                #'debug' => true
            )))
            {
                header('location: ?sfksjflkjsdflksdjflkj');
            }
            else
            {
                $_content.= 'блять, не добавилось :(';
            }
        }
    }
    else
    {
        $_content.= <<<html
<form method="post" action="">
  <input type="text" name="host" value="" /> - хост<br/>
  <input type="text" name="proto" value="80" onclick="this.select()" /> - порт<br/>
  <select name="interval">
    <option>15</option>
    <option>10</option>
    <option>5</option>
    <option>1</option>
  </select> - интервал проверки ресурса в мин.<br/>
  <input type="submit" value="Добавить" />
</form>
html;
    }
}
else
{

    $s = $db->select(array(
        'table' => 'data',
        'field' => '*',
        'order' => array('date_check', false),
        #'debug' => true
    ));

    $state = array('dont work', 'working', 'updating');

    foreach($s as $k)
    {
        $b = array('ff0000', '52d017', '82caff');

        $l = ($k['date_check'] + ($k['interval'] * 60)) - time();
	$t = array(
            date('d.m.Y H:i:s', $k['date_check']), ($l > 0 ? $l : 0)
        );

        $_content.= <<<html
<table border="1" style="margin-right: 10px; width: 200px; float: left">
  <tr>
    <td>
      <span style="float: right; font-weight: bold">{$k['proto']}</span>
      {$k['host']}
    </td>
  </tr>
  <tr>
    <td style="background: #{$b[$k['state']]}">{$state[$k['state']]}</td>
  </tr>
  <tr>
    <td>{$t[0]}</td>
  </tr>
  <tr>
    <td>ETA {$t[1]} sek</td>
  </tr>
  <tr>
    <td>{$k['status']}</td>
  </tr>
</tables>
html;
    }
    #$_content.= "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"1;URL=?\">";
}
}

$tpl->create();
$tpl->set('title', $cfg['title']);
$tpl->set('date', date('dmYH'));
$tpl->set('content', $_content);
