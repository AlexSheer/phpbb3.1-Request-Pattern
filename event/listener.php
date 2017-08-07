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
		$ex_fid_array = explode(',', $this->config['request_ex_forums']);
		if (!in_array($event['forum_id'], $ex_fid_array))
		{
			if (($data = $this->cache->get('_pattern_request')) === false)
			{
				$data = array();

				$sql = 'SELECT *
					FROM ' . $this->request_table . '
					ORDER BY order_question';
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$id = $row['order_question'] - 1;
					$data[$id]['key'] = $row['order_question'];
					$data[$id]['value'] = $row['question'];
					$data[$id]['explain'] = $row['question_explain'];
				}
				$this->db->sql_freeresult($result);
				$this->cache->put('_pattern_request', $data);
			}

			foreach ($data as $key => $value)
			{
				$this->template->assign_block_vars('patternrow', array(
					'KEY'		=> addslashes('q_' . $data[$key]['key'] . ''),
					'VALUE'		=> addslashes($data[$key]['value']),
					'EXPLAIN'	=> $data[$key]['explain'],
					'ID'		=> $key,
					)
				);
			}

			$this->template->assign_vars(array(
				'OPTIONS_NUMBER'		=> sizeof($data),
				'QUEST_COLOR'			=> $this->config['request_question_color'],
				'ANSWER_COLOR'			=> $this->config['request_answer_color'],
				)
			);
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
