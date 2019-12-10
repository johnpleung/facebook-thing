<?php

function get_metrics_schema(){

	/* IMPORTANT: Make sure that col_name are no more than 64 characters long */

	return [
		/*
			This group is for the "lifetime" metrics.
			Convention: "Lifetime" metrics get "lifetime" suffix in names and col_names
		*/
		new metrics_group ([
			'name'		=> 'Lifetime Metrics',
			'endpoint'	=> 'https://graph.facebook.com/v2.9/{{USER_NAME}}/insights/page_fans_country,page_fans_gender_age,page_fans/lifetime?&since={{DATE_FROM}}&until={{DATE_TO}}&access_token={{AUTH_TOKEN}}', //
			'metrics'	=> [
				new shallow_metric ([
					'name'					=> 'page_fans_lifetime',
					'definition'			=> new data_definition ([
						'col_name'				=> 'page_fans_lifetime',
						'col_declaration'		=> 'INT(11) unsigned',
						'path'					=> '$.data[?(@.name=="page_fans")].values[{{INDEX}}].value',
						'sprintf_type'			=> 'i'
					])
				]),
				new enum_metric ([
					'name'					=> 'page_fans_country_lifetime',
					'path'					=> '$.data[?(@.name=="page_fans_country")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'INT(11) unsigned',
						'sprintf_type'			=> 'i'
					])
				]),
				new enum_metric ([
					'name'					=> 'page_fans_gender_age_lifetime',
					'path'					=> '$.data[?(@.name=="page_fans_gender_age")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8) unsigned',
						'sprintf_type'			=> 'i'
					])
				])
			]
		]),

		/*
			This group is for the "day" metrics
		*/
			
		new metrics_group ([
			'name'		=> 'Day Metrics',
			'endpoint'	=> 'https://graph.facebook.com/v2.9/{{USER_NAME}}/insights/page_fans_online,page_fans_by_unlike_source_unique,page_actions_post_reactions_total,page_impressions_by_story_type_unique,page_impressions_by_story_type,page_negative_feedback_by_type,page_positive_feedback_by_type,page_positive_feedback_by_type_unique,page_positive_feedback_by_type_unique,page_fan_adds_by_paid_non_paid_unique,page_impressions,page_impressions_paid,page_impressions_organic,page_impressions_viral,page_impressions_unique,page_impressions_paid_unique,page_impressions_organic_unique,page_impressions_viral_unique,page_posts_impressions,page_posts_impressions_paid,page_posts_impressions_organic,page_posts_impressions_viral,page_posts_impressions_unique,page_posts_impressions_paid_unique,page_posts_impressions_organic_unique,page_posts_impressions_viral_unique,page_consumptions,page_consumptions_unique,page_negative_feedback,page_negative_feedback_unique,page_fans_online_per_day,page_video_views,page_video_views_paid,page_video_views_organic,page_video_views_autoplayed,page_video_views_click_to_play,page_video_views_unique,page_video_repeat_views,page_story_adds/day?&since={{DATE_FROM}}&until={{DATE_TO}}&access_token={{AUTH_TOKEN}}',
			'metrics'	=> [
				new shallow_metric ([
					"name" => "page_video_views_organic",
					"definition" => new data_definition ([
						"col_name" => "page_video_views_organic",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_views_organic\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_video_views_click_to_play",
					"definition" => new data_definition ([
						"col_name" => "page_video_views_click_to_play",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_views_click_to_play\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_video_repeat_views",
					"definition" => new data_definition ([
						"col_name" => "page_video_repeat_views",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_repeat_views\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_fans_online_per_day",
					"definition" => new data_definition ([
						"col_name" => "page_fans_online_per_day",
						"col_declaration" => "MEDIUMINT(8) unsigned",
						"path" => "$.data[?(@.name==\"page_fans_online_per_day\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions",
					"definition" => new data_definition ([
						"col_name" => "page_impressions",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_paid",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_paid",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_paid\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_organic",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_organic",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_organic\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_viral",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_viral",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_viral\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_unique",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_paid_unique",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_paid_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_paid_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_organic_unique",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_organic_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_organic_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_impressions_viral_unique",
					"definition" => new data_definition ([
						"col_name" => "page_impressions_viral_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_impressions_viral_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_paid",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_paid",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_paid\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_organic",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_organic",
						"col_declaration" => "MEDIUMINT(8) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_organic\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_viral",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_viral",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_viral\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_unique",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_paid_unique",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_paid_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_paid_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_organic_unique",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_organic_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_organic_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_posts_impressions_viral_unique",
					"definition" => new data_definition ([
						"col_name" => "page_posts_impressions_viral_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_posts_impressions_viral_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_consumptions",
					"definition" => new data_definition ([
						"col_name" => "page_consumptions",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_consumptions\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_consumptions_unique",
					"definition" => new data_definition ([
						"col_name" => "page_consumptions_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_consumptions_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_negative_feedback",
					"definition" => new data_definition ([
						"col_name" => "page_negative_feedback",
						"col_declaration" => "MEDIUMINT(8) unsigned",
						"path" => "$.data[?(@.name==\"page_negative_feedback\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_negative_feedback_unique",
					"definition" => new data_definition ([
						"col_name" => "page_negative_feedback_unique",
						"col_declaration" => "MEDIUMINT(8) unsigned",
						"path" => "$.data[?(@.name==\"page_negative_feedback_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_video_views",
					"definition" => new data_definition ([
						"col_name" => "page_video_views",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_views\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_video_views_paid",
					"definition" => new data_definition ([
						"col_name" => "page_video_views_paid",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_views_paid\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_video_views_autoplayed",
					"definition" => new data_definition ([
						"col_name" => "page_video_views_autoplayed",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_views_autoplayed\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_video_views_unique",
					"definition" => new data_definition ([
						"col_name" => "page_video_views_unique",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_video_views_unique\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new shallow_metric ([
					"name" => "page_story_adds",
					"definition" => new data_definition ([
						"col_name" => "page_story_adds",
						"col_declaration" => "INT(11) unsigned",
						"path" => "$.data[?(@.name==\"page_story_adds\")].values[{{INDEX}}].value",
						"sprintf_type" => "i"
					])
				]), new enum_metric ([
					'name'					=> 'page_fans_online',
					'path'					=> '$.data[?(@.name=="page_fans_online")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						"col_declaration" => "INT(11) unsigned",
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_fans_by_unlike_source_unique',
					'path'					=> '$.data[?(@.name=="page_fans_by_unlike_source_unique")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8) unsigned',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_negative_feedback_by_type',
					'path'					=> '$.data[?(@.name=="page_negative_feedback_by_type")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8) unsigned',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_fan_adds_by_paid_non_paid_unique',
					'path'					=> '$.data[?(@.name=="page_fan_adds_by_paid_non_paid_unique")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8)',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_positive_feedback_by_type',
					'path'					=> '$.data[?(@.name=="page_positive_feedback_by_type")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8) unsigned',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_positive_feedback_by_type_unique',
					'path'					=> '$.data[?(@.name=="page_positive_feedback_by_type_unique")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8) unsigned',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_impressions_by_story_type_unique',
					'path'					=> '$.data[?(@.name=="page_impressions_by_story_type_unique")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'INT(11) unsigned',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_impressions_by_story_type',
					'path'					=> '$.data[?(@.name=="page_impressions_by_story_type")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'INT(11) unsigned',
						'sprintf_type'			=> 'i'
					])
				]), new enum_metric ([
					'name'					=> 'page_actions_post_reactions_total',
					'path'					=> '$.data[?(@.name=="page_actions_post_reactions_total")].values[{{INDEX}}].value',
					'enums_definition'		=> new data_definition ([ 
						'col_declaration'		=> 'MEDIUMINT(8) unsigned',
						'sprintf_type'			=> 'i'
					])
				])
			]
		])
	];
}