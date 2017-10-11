<?php

function require_post()
{
  if(strtoupper($_SERVER['REQUEST_METHOD'])!=='POST')
  {
    show_error('The action you have requested is not allowed.', 403);
    die();
  }
}
