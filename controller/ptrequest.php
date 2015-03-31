<?php
/**
*
* @package phpBB Extension - Request Pattern
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\ptrequest\controller;

use Symfony\Component\HttpFoundation\Response;

class ptrequest
{
	protected $db;
	protected $template;
	protected $user;

	public function __construct(
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$cache,
		$request_table
	)
	{
		$this->config = $config;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->cache = $cache;
		$this->request_table = $request_table;
	}

	public function main()
	{
		if (($data = $this->cache->get('_pattern_request')) === false)
		{
			$data = array();

			$sql = 'SELECT *
				FROM ' . $this->request_table . '
				ORDER BY order_question';
			$result = $this->db->sql_query($sql);
			while($row = $this->db->sql_fetchrow($result))
			{
				$id = $row['order_question'] - 1;
				$data[$id]['key'] = $row['order_question'];
				$data[$id]['value'] = $row['question'];
				$data[$id]['explain'] = $row['question_explain'];
			}
			$this->db->sql_freeresult($result);
			$this->cache->put('_pattern_request', $data);
		}

		foreach($data as $key => $value)
		{
			$this->template->assign_block_vars('patternrow', array(
				'L_KEY'		=> 'q_' . $data[$key]['key'] . '',
				'L_VALUE'	=> $data[$key]['value'],
				'L_EXPLAIN'	=> $data[$key]['explain'],
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

		page_header($this->user->lang('PATTERN'));
		$this->template->set_filenames(array(
			'body' => 'pattern.html'));

		page_footer();
		return new Response($this->template->return_display('body'), 200);
	}
}
