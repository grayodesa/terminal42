<?php

class DigitalPointBetterAnalytics_Formatting_ExperimentTable extends WP_List_Table
{
	public function __construct($args = array())
	{
		global $status;

		$validStatuses = DigitalPointBetterAnalytics_Model_Experiments::getStatuses();
		$validStatuses = array_keys($validStatuses);
		$validStatuses = array_map('strtolower', $validStatuses);

		$status = 'all';

		if (!empty($args['experiments']['items']) && is_array($args['experiments']['items']))
		{
			foreach ($args['experiments']['items'] as $key => $experiment)
			{
				DigitalPointBetterAnalytics_Model_Experiments::decodeExperimentData($experiment);
				$experiment['sort_type'] = @$experiment['extraData']['type'];
				$args['experiments']['items'][$key] = $experiment;
			}
		}

		if (isset( $_REQUEST['experiment_status'] ) && in_array( $_REQUEST['experiment_status'], $validStatuses))
		{
			$status = $_REQUEST['experiment_status'];

			$experiments = array();

			if (!empty($args['experiments']['items']) && is_array($args['experiments']['items']))
			{
				foreach ($args['experiments']['items'] as $key => $experiment)
				{
					if ($_REQUEST['experiment_status'] == 'draft' && $experiment['status'] == 'DRAFT')
					{
						$experiments[$key] = $experiment;
					}
					elseif ($_REQUEST['experiment_status'] == 'running' && $experiment['status'] == 'RUNNING')
					{
						$experiments[$key] = $experiment;
					}
					elseif ($_REQUEST['experiment_status'] == 'ended' && $experiment['status'] == 'ENDED')
					{
						$experiments[$key] = $experiment;
					}
					elseif ($_REQUEST['experiment_status'] == 'ready' && $experiment['status'] == 'READY_TO_RUN')
					{
						$experiments[$key] = $experiment;
					}
				}

				$args['experiments']['items'] = $experiments;
			}
		}

		// because this isn't hacky, right?  lol
		if (in_array(@$_GET['action'], array('start', 'end', 'delete')) !== false)
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

		$this->items = @$this->_args['experiments']['items'];

		if (!empty($_REQUEST['orderby']))
		{
			$sortOrders = array(
				'name' => array('key' => 'name', 'type' => SORT_STRING),
				'status' => array('key' => 'status', 'type' => SORT_STRING),
				'type' => array('key' => 'sort_type', 'type' => SORT_STRING),
				//	'winner' => array('key' => 'winnerFound', 'type' => SORT_STRING),
				'start' => array('key' => 'startTime', 'type' => SORT_STRING),
				'end' => array('key' => 'endTime', 'type' => SORT_STRING),
			);

			if (!empty($sortOrders[$_REQUEST['orderby']]))
			{
				$sortOrder = array();

				foreach($this->items as $item)
				{
					$sortOrder[] = strtolower(@$item[$sortOrders[$_REQUEST['orderby']]['key']]);
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
		return array('experiments', 'widefat', $this->_args['plural'] );
	}

	public function get_columns()
	{
		return array(
			'cb'		=> '<input type="checkbox" />',
			'name'		=> esc_html__('Name', 'better-analytics'),
			'type'		=> esc_html__('Type', 'better-analytics'),
			'status'	=> esc_html__('Status', 'better-analytics'),
			'winning'	=> esc_html__('Variant Status', 'better-analytics'),
			'start'		=> esc_html__('Start Date', 'better-analytics'),
			'end'		=> esc_html__('End Date', 'better-analytics'),
		);
	}

	protected function get_sortable_columns()
	{
		return array(
			'name'		=> array('name', false),
			'status'	=> array('status', false),
			'type'		=> array('type', false),
			//'winner'	=> array('winner', true),
			'start'		=> array('start', true),
			'end'		=> array('end', true),
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

				case 'draft':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Draft', 'Draft', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'running':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Running', 'Running', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'ended':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Ended', 'Ended', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'ready':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Ready To Run', 'Ready To Run', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;
			}

			if ( 'search' != $type )
			{
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg('experiment_status', $type, menu_page_url('better-analytics_experiments', false)),
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
			$actions['start-selected'] = esc_html__('Start', 'better-analytics');
		}
		if ( $status != 'inactive')
		{
			$actions['end-selected'] = esc_html__('End', 'better-analytics');
		}

		$actions['delete-selected'] = esc_html__('Delete', 'better-analytics');

		return $actions;
	}

	public function single_row($item)
	{
		echo '<tr' . ($item['status'] == 'DRAFT' ? ' class="draft"' : ($item['status'] == 'RUNNING' ? ' class="running"' : ($item['status'] == 'ENDED' ? ' class="ended"' : ''))) . '>';
		$this->single_row_columns($item);
		echo '</tr>';
	}

	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$actions = array();
		$actions['delete'] = '<a href="' . wp_nonce_url(add_query_arg(array('id' => $item['id'], 'action' => 'delete'), $this->_getCurrentUrl()), 'delete-experiment') .  '" class="confirm-prompt" data-confirm="' . esc_attr__('Are you sure you want to permanently delete this experiment?  There is no undo.', 'better-analytics') . '">' . esc_html__('Delete', 'better-analytics') . '</a>';

		return $this->row_actions($actions);
	}

	protected function column_cb($item)
	{
		echo "<label class='screen-reader-text' for='checkbox_" . $item['id'] . "' >" . sprintf(esc_html__('Select %s', 'better-analytics'), $item['name']) . "</label>"
			. "<input type='checkbox' name='checked[]' value='" . esc_attr( $item['id'] ) . "' id='checkbox_" . $item['id'] . "' />";
	}

	protected function column_name($item)
	{
		echo '<strong><a class="row-title" href="' . add_query_arg(array('action' => 'create_edit', 'id' => $item['id']), menu_page_url('better-analytics_experiments', false)) . '">' . sanitize_text_field($item['name']) . '</a></strong>';
	}

	protected function column_type($item)
	{
		$types = DigitalPointBetterAnalytics_Model_Experiments::getTypes();

		if (!$type = @$types[@$item['extraData']['type']])
		{
			$type = esc_html__('Unknown', 'better-analytics');
		}

		echo $type;
	}


	protected function column_status($item)
	{
		echo DigitalPointBetterAnalytics_Model_Experiments::getStatusNameByCode($item['status']) . ($item['status'] == 'DRAFT' ? '<a href="' . wp_nonce_url(add_query_arg(array('id' => $item['id'], 'action' => 'start'), $this->_getCurrentUrl()), 'start-experiment') .  '" class="button button-primary confirm-prompt" data-confirm="' . esc_attr__('Are you sure you want to start this experiment?', 'better-analytics') . '">' . esc_html__('Start', 'better-analytics') . '</a>' : ($item['status'] == 'RUNNING' ? '<a href="' . wp_nonce_url(add_query_arg(array('id' => $item['id'], 'action' => 'end'), $this->_getCurrentUrl()), 'end-experiment') .  '" class="button button-primary confirm-prompt" data-confirm="' . esc_attr__('Are you sure you want to permanently end this experiment?  There is no undo/restart.', 'better-analytics') . '">' . esc_html__('End', 'better-analytics') . '</a>' : ''));
	}

	protected function column_winning($item)
	{
		if (isset($item['variationWinner']))
		{
			if ($item['variationWinner'] > 0)
			{
				printf(esc_html__('Variation %u won', 'better-analytics'), $item['variationWinner']);
			}
			else
			{
				esc_html_e('Original won', 'better-analytics');
			}
		}
		elseif(isset($item['variationWinning']))
		{
			if ($item['variationWinning'] > 0)
			{
				printf(esc_html__('Variation %u is winning', 'better-analytics'), $item['variationWinning']);
			}
			else
			{
				esc_html_e('Original is winning', 'better-analytics');
			}
		}
		else
		{
			esc_html_e('None', 'better-analytics');
		}

		if ($item['status'] == 'RUNNING' || $item['status'] == 'ENDED')
		{
			echo ' <a href="' . esc_url('https://www.google.com/analytics/web/?#siteopt-experiment/siteopt-detail/a' . absint($item['accountId']) . 'w' . absint($item['internalWebPropertyId']) . 'p' . absint($item['profileId']) . '/%3F_r.drilldown%3Danalytics.gwoExperimentId%3A' . urlencode($item['id']) . '/') .  '" target="_blank" ><span class="dashicons dashicons-awards"></span></a>';
		}

	}

	protected function column_start($item)
	{
		if (empty($item['startTime']))
		{
			esc_html_e('None', 'better-analytics');
		}
		else
		{
			/* translators: PHP date format - see: http://php.net/manual/function.date.php */
			echo get_date_from_gmt($item['startTime'], esc_html__('Y/m/d g:i a', 'better-analytics'));
		}
	}

	protected function column_end($item)
	{
		if (empty($item['endTime']))
		{
			esc_html_e('None', 'better-analytics');
		}
		else
		{
			/* translators: PHP date format - see: http://php.net/manual/function.date.php */
			echo get_date_from_gmt($item['endTime'], esc_html__('Y/m/d g:i a', 'better-analytics'));
		}
	}

}