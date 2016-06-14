<?php

class DigitalPointBetterAnalytics_Model_Goals
{
	public static function getTypes()
	{
		$types = array(
			'URL_DESTINATION' => esc_html__('Destination URL', 'better-analytics'),
			'VISIT_TIME_ON_SITE' => esc_html__('Session duration', 'better-analytics'),
			'VISIT_NUM_PAGES' => esc_html__('Pages per session', 'better-analytics'),
			'EVENT' => esc_html__('Event', 'better-analytics'),
		);

		return $types;
	}

	public static function getTypeNameByCode($typeCode)
	{
		$types = self::getTypes();
		return @$types[$typeCode];
	}

	public static function getMatchTypes($goals = false)
	{
		if ($goals)
		{
			$types = array(
				'EXACT' => esc_html__('Equal to', 'better-analytics'),
				'BEGINS_WITH' => esc_html__('Begins with', 'better-analytics'),
				'REGEXP' => esc_html__('Regular expression', 'better-analytics'),
			);
		}
		else
		{
			$types = array(
				'EXACT' => esc_html__('Equal to', 'better-analytics'),
				'HEAD' => esc_html__('Begins with', 'better-analytics'),
				'REGEX' => esc_html__('Regular expression', 'better-analytics'),
			);
		}

		return $types;
	}

	public static function getComparisonTypes()
	{
		$types = array(
			'LESS_THAN' => esc_html__('Less than', 'better-analytics'),
			'GREATER_THAN' => esc_html__('Greater than', 'better-analytics'),
			'EQUAL' => esc_html__('Equal to', 'better-analytics'),
		);

		return $types;
	}

	public static function getEventConditionTypes()
	{
		$types = array(
			'CATEGORY' => esc_html__('Category', 'better-analytics'),
			'ACTION' => esc_html__('Action', 'better-analytics'),
			'LABEL' => esc_html__('Label', 'better-analytics'),
			'VALUE' => esc_html__('Value', 'better-analytics'),
		);

		return $types;
	}

	public static function getUrlMatchTypeNameByCode($typeCode)
	{
		$types = self::getMatchTypes();
		return @$types[$typeCode];
	}

	public static function getGoalSetByGoal($goalId)
	{
		return ((($goalId - 1) - (($goalId - 1) % 5)) / 5) + 1;
	}
}