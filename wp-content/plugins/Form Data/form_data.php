<?php
/*
Plugin Name: Form Data
Plugin URI: http://localhost/wordpress/
Description: A simple wordpress plugin
Author: Shraddha Kulkarni
Author URI: http://localhost/wordpress/
Version: 1.0

*/

register_activation_hook(__FILE__,'form_data_activate');
register_deactivation_hook(__FILE__,'form_data_deactivate');

function form_data_activate()
{
	global $wpdb;
	global $table_prefix;
	$table=$table_prefix.'form_data';

	$sql="CREATE TABLE `$table` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `$table`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `$table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

";

$wpdb->query($sql);

}

function form_data_deactivate()
{
	global $wpdb;
	global $table_prefix;
	$table=$table_prefix.'form_data';


	$sql="DROP TABLE `wordpress`.`$table`";
	$wpdb->query($sql);
}

add_action('admin_menu','form_data_menu');

function form_data_menu()
{
	add_menu_page('Form Data','Form Data',8,__FILE__,'form_data_list');
}

function form_data_list()
{
	include('form_data_list.php');
}

?>