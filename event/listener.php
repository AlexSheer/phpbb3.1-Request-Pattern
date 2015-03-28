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
	protected $controller_helper;

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
		);
	}

	/**
	* Constructor
	*/
	public function __construct(
		\phpbb\template\template $template,
		\phpbb\controller\helper $controller_helper,
		$phpbb_root_path
	)
	{
		$this->template = $template;
		$this->controller_helper = $controller_helper;
		$this->phpbb_root_path = $phpbb_root_path;
	}

	public function add_popup_url($event)
	{
		$page_data = $event['page_data'];
		$page_data['POPUP_URL'] = $this->controller_helper->route('sheer_ptrequest_controller');
		$event['page_data'] = $page_data;
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
