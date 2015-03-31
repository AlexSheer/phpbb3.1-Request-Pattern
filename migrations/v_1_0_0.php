<?php
/**
*
* @package phpBB Extension - Request Pattern
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\ptrequest\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['request_version']) && version_compare($this->config['request_version'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
		return array('\sheer\ptrequest\migrations\v_0_0_2');
	}

	public function update_schema()
	{
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Update configs
			array('config.update', array('request_version', '1.0.0')),
		);
	}
}
