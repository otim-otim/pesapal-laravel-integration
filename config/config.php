<?php

return [
  "CONSUMER_KEY" => "TDpigBOOhs+zAl8cwH2Fl82jJGyD8xev", // enter your consumer key here

  "CONSUMER_SECRET" => "1KpqkfsMaihIcOlhnBo/gBZ5smw=", //enter your consumer secret here

  'SAND_BOX_URL' => 'https://cybqa.pesapal.com/pesapalv3/api', //enter sandbox url here
  'LIVE_URL'  => 'https://pay.pesapal.com/v3/api', //enter live url here


  /*
  get the notification id from pesapal
  and add here.
  check pesapal documentation for more details
  */
  'NOTIFICATION_ID' => '', //notification id from pesapal
  /*
  url pesapal will hit this route whenever action is done to a payment request
  NB:be sure to create this route
  for our case, this will be a post route
  */
  'NOTIFICATION_URL' => '', 

  /*
  url pesapal will redirect your user to after processing payment 
  NB:be sure to create this route
  */
  'CALLBACK_URL' => '', 
  /*
  url pesapal will redirect your user to after canceling payment 
  NB:be sure to create this route, if you want to enable payemnt request cancelation.
  check pesapal documentation for more details
  */
  'CANCELLATION_URL' => '', 


];
