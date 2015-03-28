<?php
/**
*
* @package Request Pattern
* @copyright (c) 2014 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\ptrequest\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var sheer\ptrequest\core\helper */
	protected $controller_helper;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string phpbb_root_path */
	protected $phpbb_root_path;

/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'load_language_on_setup',
			'core.posting_modify_template_vars'	=> 'add_popup_url',
			'core.viewtopic_modify_page_title'	=> 'add_popup_url',
		);
	}

	/**
	* Constructor
	*/
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template,
		\phpbb\controller\helper $controller_helper,
		$cache,
		$phpbb_root_path,
		$request_table
	)
	{
		$this->config = $config;
		$this->db = $db;
		$this->template = $template;
		$this->controller_helper = $controller_helper;
		$this->cache = $cache;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->request_table = $request_table;
	}

	public function add_popup_url($event)
	{
		$page_data = $event['page_data'];
		$ex_fid_array = explode(',', $this->config['request_ex_forums']);
		if (!in_array($event['forum_id'], $ex_fid_array))
		{
			if (($data = $this->cache->get('_pattern_request')) === false)
			{
				$data = array();

				$sql = 'SELECT id
					FROM ' . $this->request_table;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
			}

			if(isset($row) && !empty($row) || !empty($data))
			{
				$page_data['POPUP_URL'] = $this->controller_helper->route('sheer_ptrequest_controller');
				$event['page_data'] = $page_data;
			}
		}
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'sheer/ptrequest',
			'lang_set' => 'ptrequest',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
