<?php

/**
 * Class responsible for scheduling and un-scheduling events (cron jobs).
 *
 * This class defines all code necessary to schedule and un-schedule cron jobs.
 *
 * @since      1.0.0
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 */
class All_In_One_Analytics_Cron {
	const ALL_IN_ONE_ANALYTICS_EVENT_HOURLY_HOOK = 'all_in_one_analytics_event_hourly';

	/**
	 * Check if already scheduled, and schedule if not.
	 */
	public static function schedule() {
		if ( ! self::next_scheduled_hourly() ) {
			self::hourly_schedule();
		}
	}

	/**
	 * Unschedule.
	 */
	public static function unschedule() {
		wp_clear_scheduled_hook( self::ALL_IN_ONE_ANALYTICS_EVENT_HOURLY_HOOK );
	}

	/**
	 * @return false|int Returns false if not scheduled, or timestamp of next run.
	 */
	private static function next_scheduled_hourly() {
		return wp_next_scheduled( self::ALL_IN_ONE_ANALYTICS_EVENT_HOURLY_HOOK );
	}

	/**
	 * Create new schedule.
	 */
	private static function hourly_schedule() {
		wp_schedule_event( time(), 'hourly', self::ALL_IN_ONE_ANALYTICS_EVENT_HOURLY_HOOK );
	}
}