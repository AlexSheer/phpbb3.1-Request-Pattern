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
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$request_table
	)
	{
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->request_table = $request_table;
	}

	public function main()
	{
		$question_count = 0;

		$sql = 'SELECT *
			FROM ' . $this->request_table . '
			ORDER BY order_question';
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('patternrow', array(
				'L_KEY'		=> 'q_' . $row['order_question'] . '',
				'L_VALUE'	=> $row['question'],
				'L_EXPLAIN'	=> $row['question_explain'],
				'ID'		=> $row['order_question'] - 1,
				)
			);
			$question_count++;
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'OPTIONS_NUMBER'		=> $question_count,
			)
		);

		page_header($this->user->lang('PATTERN'));
		$this->template->set_filenames(array(
			'body' => 'pattern.html'));

		page_footer();
		return new Response($this->template->return_display('body'), 200);
	}
}
