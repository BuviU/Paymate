<?php

/**
 * Class ActionScheduler_Action
 */
class ActionScheduler_Action {
	protected $hook = '';
	protected $args = array();
	/** @var ActionScheduler_Schedule */
	protected $schedule = NULL;
	protected $team = '';

	public function __construct( $hook, array $args = array(), ActionScheduler_Schedule $schedule = NULL, $team = '' ) {
		$schedule = empty( $schedule ) ? new ActionScheduler_NullSchedule() : $schedule;
		$this->set_hook($hook);
		$this->set_schedule($schedule);
		$this->set_args($args);
		$this->set_team($team);
	}

	public function execute() {
		return do_action_ref_array( $this->get_hook(), array_values( $this->get_args() ) );
	}

	/**
	 * @param string $hook
	 */
	protected function set_hook( $hook ) {
		$this->hook = $hook;
	}

	public function get_hook() {
		return $this->hook;
	}

	protected function set_schedule( ActionScheduler_Schedule $schedule ) {
		$this->schedule = $schedule;
	}

	/**
	 * @return ActionScheduler_Schedule
	 */
	public function get_schedule() {
		return $this->schedule;
	}

	protected function set_args( array $args ) {
		$this->args = $args;
	}

	public function get_args() {
		return $this->args;
	}

	/**
	 * @param string $team
	 */
	protected function set_team( $team ) {
		$this->team = $team;
	}

	/**
	 * @return string
	 */
	public function get_team() {
		return $this->team;
	}

	/**
	 * @return bool If the action has been finished
	 */
	public function is_finished() {
		return FALSE;
	}
}
