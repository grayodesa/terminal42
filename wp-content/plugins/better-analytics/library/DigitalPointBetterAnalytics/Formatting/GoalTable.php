<?php

class DigitalPointBetterAnalytics_Formatting_GoalTable extends WP_List_Table
{
	public function __construct( $args = array())
	{
		global $status;

		$status = 'all';
		if ( isset( $_REQUEST['goal_status'] ) && in_array( $_REQUEST['goal_status'], array( 'active', 'inactive') ) )
		{
			$status = $_REQUEST['goal_status'];

			$goals = array();

			if (!empty($args['goals']) && is_array($args['goals']))
			{
				foreach ($args['goals'] as $key => $goal)
				{
					if ($_REQUEST['goal_status'] == 'active' && $goal['active'])
					{
						$goals[$key] = $goal;
					}
					elseif ($_REQUEST['goal_status'] == 'inactive' && !$goal['active'])
					{
						$goals[$key] = $goal;
					}
				}

				$args['goals'] = $goals;
			}
		}

		// because this isn't hacky, right?  lol
		if (@$_GET['action'] == 'activate' || @$_GET['action'] == 'deactivate')
		{
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('id', 'action', '_wpnonce'), $_SERVER['REQUEST_URI']);
		}

		parent::__construct($args);
	}

	protected function _getCurrentUrl()
	{
		return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}

	public function ajax_user_can()
	{
		return current_user_can('manage_options');
	}

	public function prepare_items()
	{
		global $totals, $status;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->items = $this->_args['goals'];

		if (!empty($_REQUEST['orderby']))
		{
			$sortOrders = array(
				'name' => array('key' => 'name', 'type' => SORT_STRING),
				'slot' => array('key' => 'id', 'type' => SORT_NUMERIC),
				'value' => array('key' => 'value', 'type' => SORT_NUMERIC),
				'created' => array('key' => 'created', 'type' => SORT_STRING),
				'updated' => array('key' => 'updated', 'type' => SORT_STRING),
			);

			if (!empty($sortOrders[$_REQUEST['orderby']]))
			{
				$sortOrder = array();

				foreach($this->items as $item)
				{
					$sortOrder[] = strtolower($item[$sortOrders[$_REQUEST['orderby']]['key']]);
				}

				array_multisort($sortOrder, (@$_REQUEST['order'] == 'desc' ? SORT_DESC : SORT_ASC), $sortOrders[$_REQUEST['orderby']]['type'], $this->items);
			}
		}

		$this->set_pagination_args( array(
			'total_items' => (@$totals[$status] + 0),
			'per_page' => 1000,
		));
	}

	protected function get_table_classes()
	{
		return array('goals', 'widefat', $this->_args['plural'] );
	}

	public function get_columns()
	{
		return array(
			'cb'		=> '<input type="checkbox" />',
			'name'		=> esc_html__('Name', 'better-analytics'),
			'slot'		=> esc_html__('Slot', 'better-analytics'),
			'value'		=> esc_html__('Value', 'better-analytics'),
			'created'	=> esc_html__('Created', 'better-analytics'),
			'updated'	=> esc_html__('Updated', 'better-analytics'),
		);
	}

	protected function get_sortable_columns()
	{
		return array(
			'name'		=> array('name', false),
			'slot'		=> array('slot', false),
			'value'		=> array('value', true),
			'created'	=> array('created', true),
			'updated'	=> array('updated', true),
		);
	}

	protected function get_views()
	{
		global $totals, $status;

		$status_links = array();
		foreach ($totals as $type => $count)
		{
			if (!$count)
			{
				continue;
			}

			switch ( $type ) {
				case 'all':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('All', 'All', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'active':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Active', 'Active', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'inactive':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Inactive', 'Inactive', $count),
						'<span class="count">',
						'</span>',
						$count
					);
			}

			if ( 'search' != $type )
			{
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg('goal_status', $type, menu_page_url('better-analytics_goals', false)),
					( $type == $status ) ? ' class="current"' : '',
					sprintf( $text, number_format_i18n( $count ) )
				);
			}
		}

		return $status_links;
	}

	protected function get_bulk_actions()
	{
		global $status;

		$actions = array();

		if ($status != 'active')
		{
			$actions['activate-selected'] = esc_html__('Activate', 'better-analytics');
		}
		if ( $status != 'inactive')
		{
			$actions['deactivate-selected'] = esc_html__('Deactivate', 'better-analytics');
		}

		return $actions;
	}

	public function single_row($item)
	{
		echo '<tr' . ($item['active'] ? ' class="active"' : '') . '>';
		$this->single_row_columns($item);
		echo '</tr>';

		echo '<tr class="extra_info no-items' . ($item['active'] ? ' active' : '') . '"><td></td><td colspan="' . (count($this->get_columns()) - 1) . '">' . '<div>' . DigitalPointBetterAnalytics_Model_Goals::getTypeNameByCode($item['type']) . ': ' . '<strong style="padding-left: 5px;">';

		switch ($item['type'])
		{
			case 'URL_DESTINATION':
				echo $item['urlDestinationDetails']['url'];
				break;
			case 'VISIT_TIME_ON_SITE':
				$durations = explode(":", gmdate('j:H:i:s', @$item['visitTimeOnSiteDetails']['comparisonValue']));

				/* translators: %1$s = hours, %2$s = minutes, %3$s = seconds */
				printf(esc_html__('%1$u hours, %2$u minutes, %3$u seconds', 'better-analytics'),
					(absint($durations[0] - 1) * 24) + absint($durations[1]),
					$durations[2],
					$durations[3]
				);
				break;
			case 'VISIT_NUM_PAGES':
				echo number_format_i18n(@$item['visitNumPagesDetails']['comparisonValue']);
				break;
			case 'EVENT':
				echo @$item['eventDetails']['eventConditions'][0]['expression'];
				break;
		}

		echo '</strong>';
		echo '</div>';

		$url = $this->_getCurrentUrl();
		if ($item['active'])
		{
			echo '<div class="row-actions visible"><a href="' . wp_nonce_url(add_query_arg(array('id' => $item['id'], 'action' => 'deactivate'), $url), 'deactivate-goal') . '" title="' . esc_html__('Deactivate', 'better-analytics') . '">' . esc_html__('Deactivate', 'better-analytics') . '</a></div>';
		}
		else
		{
			echo '<div class="row-actions visible"><a href="' . wp_nonce_url(add_query_arg(array('id' => $item['id'], 'action' => 'activate'), $url), 'activate-goal') . '" title="' . esc_html__('Activate', 'better-analytics') . '">' . esc_html__('Activate', 'better-analytics') . '</a></div>';
		}


		echo '</td></tr>';

	}

	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}
		$actions = array();

		return $this->row_actions( $actions );
	}

	protected function row_actions($actions)
	{
		return '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';
	}

	protected function column_cb($item)
	{
		echo "<label class='screen-reader-text' for='checkbox_" . $item['id'] . "' >" . sprintf(esc_html__('Select %s', 'better-analytics'), $item['name']) . "</label>"
			. "<input type='checkbox' name='checked[]' value='" . esc_attr( $item['id'] ) . "' id='checkbox_" . $item['id'] . "' />";
	}


	protected function column_name($item)
	{
		echo '<strong><a class="row-title" href="' . add_query_arg(array('action' => 'create_edit', 'id' => $item['id']), menu_page_url('better-analytics_goals', false)) . '">' . sanitize_text_field($item['name']) . '</a></strong>';
	}

	protected function column_slot($item)
	{
		$goalSet = DigitalPointBetterAnalytics_Model_Goals::getGoalSetByGoal($item['id']);

		printf(esc_html__('Goal ID %1$u / Goal Set %2$u', 'better-analytics'), $item['id'], $goalSet);
	}

	protected function column_value($item)
	{
		echo number_format_i18n($item['value'], 2);
	}

	protected function column_created($item)
	{
		/* translators: PHP date format - see: http://php.net/manual/function.date.php */
		echo get_date_from_gmt($item['created'], esc_html__('Y/m/d g:i a', 'better-analytics'));
	}

	protected function column_updated($item)
	{
		/* translators: PHP date format - see: http://php.net/manual/function.date.php */
		echo get_date_from_gmt($item['updated'], esc_html__('Y/m/d g:i a', 'better-analytics'));
	}

}