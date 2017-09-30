<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>minTime</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
  <script src="vendor/locutus/strtotime.js"></script>
  <script src="vendor/mustache/mustache.min.js"></script>
  <script src="js/mintime.js"></script>
  <link href="css/mintime.css" rel="stylesheet" type="text/css"/>
</head>
<body>

  <script id="tasks-template" type="text/template"><?php $this->load->view('templates/tasks.php'); ?></script>
  <script id="task_details-template" type="text/template"><?php $this->load->view('templates/task_details.php'); ?></script>
  <script id="log-template" type="text/template"><?php $this->load->view('templates/log.php'); ?></script>
  <script id="log_details-template" type="text/template"><?php $this->load->view('templates/log_details.php'); ?></script>
  <script id="stats-template" type="text/template"><?php $this->load->view('templates/stats.php'); ?></script>
  
  <div id="container">  

    <div id="top">
      <div id="q1c"><div class="q" id="q1"></div></div>
      <div id="q2c"><div class="q" id="q2"></div></div>
    </div>

    <div id="bottom">
      <div id="q3c"><div class="q" id="q3"></div></div>
      <div id="q4c"><div class="q" id="q4"></div></div>
    </div>

  </div>

</body>
</html>
