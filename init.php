<?php
/*******************************************************************************
 * Obligatory WordPress plugin information
 ******************************************************************************/
/*
Plugin Name: WP News Manager
Plugin URI: http://www.objectunoriented.com/projects/wp-news-manager
Description: Plugin that allows management and display of news articles
Version: 1.0
Author: Charles Hriczko
Author URI: http://objectunoriented.com
License: GPLv2
*/
/*******************************************************************************
 * Require necessary files
 ******************************************************************************/
require_once('lib/constants.php');
require_once('lib/news_manager.class.php');

/*******************************************************************************
 * Instantiate our class
 ******************************************************************************/
$news_manager = new News_Manager(); //Initialize the News Manager class
?>