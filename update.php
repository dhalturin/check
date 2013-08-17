<?
error_reporting(E_ALL ^ E_NOTICE);
define('check', true);
define('root_dir', dirname(__FILE__));

print root_dir;

require_once(root_dir . '/engine/config.php');
require_once(root_dir . '/engine/mysqli.class.php');

while(true)
{
    #print time() . "\n";

    //select * from `data` where unix_timestamp() - `date_check` > `interval` * 60
    $s = $db->select(array(
        'table' => 'data',
        'field' => '*',
        'where' => '(unix_timestamp() - `date_check`) > (`interval` * 60)',
        'order' => array('date_check', true),
        'limit' => 3,
        #'debug' => true
    ));

/*    $e = array();
    foreach($s as $k)
        $e[] = $k['host'];

    print implode('|', $e) . "\n";
*/
    foreach($s as $k)
    {
        $db->update(array(
            'table' => 'data',
            'field' => array(
                array('state', 2)
            ),
            'where' => array('id', $k['id']),
            #'debug' => true
        ));

        print date('d.m.Y H:i:s') . ' checking ' . $k['host'] . ' ' . $k['proto'] . "\n";

        $o = explode('|', exec(root_dir . '/engine/plugin/' . $k['proto'] . ' ' . $k['host']));

        $db->update(array(
            'table' => 'data',
            'field' => array(
                array('state', $o[0]),
                array('date_check', time()),
                array('status', addslashes($o[1]))
            ),
            'where' => array('id', $k['id']),
            #'debug' => true
	));

        if($o[0] == 0)
	{
            mail('dhalturin@clodo.ru', 'check host ' . $k['host'], 'check host ' . $k['host'] . ', proto ' . $k['proto'] . ', output: ' . $o[1]);
        }
    }

#    print "\n";
    sleep(1);
}
