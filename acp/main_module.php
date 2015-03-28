<?php
/**
*
* @package phpBB Extension - Request Pattern
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\ptrequest\acp;

class main_module
{
	var $u_action;
	var $request_table;

	function main($id, $mode)
	{
		global $db, $user, $template, $cache, $request, $phpbb_container;

		$this->request_table = $phpbb_container->getParameter('tables.ptrequest');

		$action				= $request->variable('action', '');
		$order_question_id	= $request->variable('order_question_id', 0);

		$ids				= $request->variable('ids', array(0));
		$order_questions	= $request->variable('patternrow', array(''), true);
		$explains			= $request->variable('explain', array(''), true);
		$deletemark			= $request->variable('delmarked', false, false, \phpbb\request\request_interface::POST);
		$deleteall			= $request->variable('delall', false, false, \phpbb\request\request_interface::POST);

		$question			= $request->variable('question', '', true);
		$explain			= $request->variable('expl', '', true);

		$this->tpl_name = 'acp_order_questions_body';
		$this->page_title = $user->lang('ACP_REQUEST_PATTERN');
		$error = $_error = array();

		add_form_key('sheer/order_questions');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('sheer/order_questions'))
			{
				trigger_error('FORM_INVALID');
			}

			if(sizeof($ids))
			{
				foreach($ids as $key => $id)
				{
					if(!$order_questions[$id])
					{
						$error[] = $user->lang['EMPTY_QUESTION'];
						break;
					}

					if (!sizeof($error))
					{
						$sql_data = array(
							'question'			=> $order_questions[$id],
							'question_explain'	=> $explains[$id],
						);

						$sql = 'UPDATE ' . $this->request_table . '
							SET ' . $db->sql_build_array('UPDATE', $sql_data) . '
							WHERE id = ' . $id;
						$db->sql_query($sql);
					}
				}
				$cache->destroy('sql', $this->request_table);
				meta_refresh(3, append_sid($this->u_action));
				trigger_error($user->lang['UPDATE_SUCCESS'] . adm_back_link($this->u_action));
			}
			else
			{
				meta_refresh(3, append_sid($this->u_action));
				trigger_error($user->lang['UPDATE_FAIL'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		if (($deletemark || $deleteall))
		{
			if (confirm_box(true))
			{
				if ($deletemark && sizeof($ids))
				{
					$msg = $user->lang['DELETE_MARKED_SUCESS'];
					foreach($ids as $id)
					{
						$sql = 'SELECT order_question
							FROM ' . $this->request_table. '
							WHERE id = '. $id;
						$result = $db->sql_query($sql);
						$order_question = (int) $db->sql_fetchfield('order_question');
						$db->sql_freeresult($result);

						$sql = 'DELETE FROM ' . $this->request_table. ' WHERE id = '. $id;
						$db->sql_query($sql);

						$sql = 'SELECT id, order_question
							FROM ' . $this->request_table . '
							WHERE order_question > '. $order_question;
						$result = $db->sql_query($sql);

						while ($row = $db->sql_fetchrow($result))
						{
							$sql = 'UPDATE ' . $this->request_table . ' SET order_question = order_question - 1 WHERE id = '. $row['id'] . '';
							$db->sql_query($sql);
						}
						$db->sql_freeresult($result);
					}
				}
				if($deleteall)
				{
					$sql = 'TRUNCATE ' . $this->request_table;
					$msg = $user->lang['DELETE_SUCESS'];
					$db->sql_query($sql);
				}
				$cache->destroy('sql', $this->request_table);
				meta_refresh(3, append_sid($this->u_action));
				trigger_error($msg . adm_back_link($this->u_action));
			}
			else
			{
				confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
					'delmarked'	=> $deletemark,
					'delall'	=> $deleteall,
					'ids'		=> $ids,
					'action'	=> $this->u_action))
				);
			}
		}

		$sql = 'SELECT *
			FROM ' . $this->request_table . '
			ORDER BY order_question';
		$result = $db->sql_query($sql, 86400);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('patternrow', array(
				'ID'				=> $row['id'],
				'QUESTION'			=> $row['question'],
				'QUESTION_EXPLAIN'	=> $row['question_explain'],
				'U_MOVE_UP'			=> $this->u_action . '&amp;action=move_up&amp;order_question_id=' . $row['id'] . '',
				'U_MOVE_DOWN'		=> $this->u_action . '&amp;action=move_down&amp;order_question_id=' . $row['id'] . '',
				)
			);
		}
		$db->sql_freeresult($result);

		if ($request->is_set_post('add'))
		{
			$sql = 'SELECT MAX(order_question) AS max
				FROM ' . $this->request_table;
			$result = $db->sql_query($sql);
			$max = (int) $db->sql_fetchfield('max');
			$db->sql_freeresult($result);
			++$max;

			if(!$question)
			{
				$_error[] = $user->lang['EMPTY_QUESTION'];
			}

			if(!sizeof($_error))
			{
				$sql_ary = array(
					'question'			=> $question,
					'question_explain'	=> $explain,
					'order_question'	=> $max,
				);

				$db->sql_query('INSERT INTO ' . $this->request_table . ' ' . $db->sql_build_array('INSERT', $sql_ary));
				$cache->destroy('sql', $this->request_table);
				meta_refresh(3, append_sid($this->u_action));
				trigger_error($user->lang['ADD_SUCCESS'] . adm_back_link($this->u_action));
			}
		}

		switch ($action)
		{
			case 'move_up':
			case 'move_down':
			$move_name = $this->move($order_question_id, $action);
			$cache->destroy('sql', $this->request_table);
			if ($request->is_ajax())
			{
				$json_response = new \phpbb\json_response;
				$json_response->send(array(
					'success'	=> ($move_name !== false),
				));
			}
			break;
		}

		$template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,
			'QUESTION'			=> $question,
			'QUESTION_EXPLAIN'	=> $explain,
			'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',
			'S_ERROR'			=> (sizeof($_error)) ? implode('<br />', $_error) : '',
		));
	}

	function move($id, $action = 'move_up')
	{
		global $db, $phpbb_container;
		$sql = 'SELECT order_question
			FROM ' . $this->request_table . '
			WHERE id = ' . $id;
		$result = $db->sql_query_limit($sql, 1);
		$order = $db->sql_fetchfield('order_question');
		$db->sql_freeresult($result);

		$sql = 'SELECT id, order_question
			FROM ' . $this->request_table . "
			WHERE " . (($action == 'move_up') ? "order_question < {$order} ORDER BY order_question DESC" : "order_question > {$order} ORDER BY order_question ASC");
		$result = $db->sql_query_limit($sql, 1);
		$target = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$db->sql_freeresult($result);

		if (!sizeof($target))
		{
			return false;
		}

		if ($action == 'move_up')
		{
			$sql = 'UPDATE ' . $this->request_table . ' SET order_question = order_question + 1 WHERE id = '. $target['id'] . '';
			$db->sql_query($sql);
			$sql = 'UPDATE ' . $this->request_table . ' SET order_question = order_question - 1 WHERE id = '. $id . '';
			$db->sql_query($sql);
		}
		else
		{
			$sql = 'UPDATE ' . $this->request_table . ' SET order_question = order_question - 1 WHERE id = '. $target['id'] . '';
			$db->sql_query($sql);
			$sql = 'UPDATE ' . $this->request_table . ' SET order_question = order_question + 1 WHERE id = '. $id . '';
			$db->sql_query($sql);
		}
		return $order;
	}
}
