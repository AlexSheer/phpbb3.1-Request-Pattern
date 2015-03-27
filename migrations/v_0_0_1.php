<?php
/**
*
* @package phpBB Extension - My test
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\ptrequest\migrations;

class v_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'request_pattern'	=> array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'question'		=> array('VCHAR:255', ''),
						'order_question'=> array('USINT', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'request_pattern',
			),
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('request_version', '0.0.1')),
			// ACP
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_REQUEST_PATTERN')),
			array('module.add', array('acp', 'ACP_REQUEST_PATTERN', array(
				'module_basename'	=> '\sheer\ptrequest\acp\main_module',
				'module_langname'	=> 'ACP_REQUEST_PATTERN_MANAGE',
				'module_mode'		=> 'manage',
				'module_auth'		=> 'ext_sheer/ptrequest && acl_a_board',
			))),
		);
	}
}
