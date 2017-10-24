<?php

class Currencies_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function get()
  {
    $currencies = [];
  
    $currencies['AUD'] = ['Australian Dollar'];
    $currencies['BGN'] = ['Bulgarian Lev'];
    $currencies['BRL'] = ['Brazilian Real'];
    $currencies['CAD'] = ['Canadian Dollar'];
    $currencies['CHF'] = ['Swiss Franc'];
    $currencies['CNY'] = ['Chinese Yuan Renminbi'];
    $currencies['CZK'] = ['Czech Koruna'];
    $currencies['DKK'] = ['Danish Krone'];
    $currencies['EUR'] = ['Euro'];
    $currencies['GBP'] = ['British Pound'];
    $currencies['HKD'] = ['Hong Kong Dollar'];
    $currencies['HRK'] = ['Croatian Kuna'];
    $currencies['HUF'] = ['Hungarian Forint'];
    $currencies['IDR'] = ['Indonesian Rupiah'];
    $currencies['ILS'] = ['Israeli Shekel'];
    $currencies['INR'] = ['Indian Rupee'];
    $currencies['JPY'] = ['Japanese Yen'];
    $currencies['KRW'] = ['South Korean Won'];
    $currencies['MXN'] = ['Mexican Peso'];
    $currencies['MYR'] = ['Malaysian Ringgit'];
    $currencies['NOK'] = ['Norwegian Krone'];
    $currencies['NZD'] = ['New Zealand Dollar'];
    $currencies['PHP'] = ['Philippine Peso'];
    $currencies['PLN'] = ['Polish Zloty'];
    $currencies['RON'] = ['Romanian Leu'];
    $currencies['RUB'] = ['Russian Ruble'];
    $currencies['SEK'] = ['Swedish Krona'];
    $currencies['SGD'] = ['Singapore Dollar'];
    $currencies['THB'] = ['Thai Baht'];
    $currencies['TRY'] = ['Turkish Lira'];
    $currencies['USD'] = ['US Dollar'];
    $currencies['ZAR'] = ['South African Rand'];
  
    $base = $this->get_functional();

    // see when we last got exchange data
    $this->db->where('name','currencies_updated');
    $query = $this->db->get('settings');
    $setting = $query->row();

    // if exchange data not available or too old, get new data
    if(!$setting || $setting->value < time()-3600)
    {
      $exchange = file_get_contents('http://api.fixer.io/latest?base='.$base);
      if($exchange)
      {
        $exchange_decoded = json_decode($exchange,true);

        // if the exchange data looks right, update the database
        if($exchange_decoded && !empty($exchange_decoded['rates']) && is_array($exchange_decoded['rates']))
        {
          $this->db->replace('settings',['name'=>'currencies_updated', 'value'=>time()]);
          $this->db->replace('settings',['name'=>'currencies_exchange', 'value'=>$exchange]);
        }
      }
    }

    // get exchange data and add to our currencies array
    $this->db->where('name','currencies_exchange');
    $query = $this->db->get('settings');
    $setting = $query->row();
    
    if($setting)
    {
      $exchange = json_decode($setting->value,true);
      foreach($exchange['rates'] as $symbol=>$rate)
      {
        if(isset($currencies[$symbol])) $currencies[$symbol][1] = $rate;
      }
    }

    // set functional currency rate to 1. it's not defined above.
    $currencies[$base][1] = 1.0;

    return $currencies;
  }

  // same as get but different format
  public function list($available_only = false)
  {
    if($available_only) $available = $this->get_available();

    $return = [];
    $currencies = $this->get();
    foreach($currencies as $symbol=>$data)
    {
      if(!$available_only || array_search($symbol, $available)!==false)
      {
        $return[] = ['symbol'=>$symbol, 'name'=>$data[0], 'rate'=>(isset($data[1]) ? $data[1] : false)];
      }
    }

    return $return;
  }

  public function exchange($amount, $currency)
  {
    $currencies = $this->get();
    if(empty($currencies[$currency][1])) return false;
    
    $rate = $currencies[$currency][1];
    $fee = $this->get_fee()/100;

    $exchange = $amount / $rate;
    $exchange = $exchange - ($exchange * $fee);
    return $exchange;
  }

  public function set_functional($currency)
  {
    // make sure currency is valid
    $valid_currencies = array_keys($this->get());
    if(array_search($currency, $valid_currencies)===false) return false;

    // delete+insert (replace)
    $this->db->replace('settings',['name'=>'functional_currency', 'value'=>$currency]);
     
    // if changing functional currency, need to refresh rates
    $this->db->replace('settings',['name'=>'currencies_updated','value'=>0]);

    // return whether something happened
    return (bool) $this->db->affected_rows();
  }

  public function get_functional()
  {
    // get value from settings table
    $this->db->where('name','functional_currency');
    $query = $this->db->get('settings');
    $setting = $query->row();

    // return setting value, or USD as default
    if($setting) return $setting->value;
    else return 'USD';
  }

  public function set_available($currencies)
  {
    if(!is_array($currencies)) $currencies = [$currencies];

    // make sure all currencies are valid and we have at least one
    if(!is_array($currencies) || empty($currencies)) return false;
    $valid_currencies = array_keys($this->get());
    foreach($currencies as $currency)
    {
      if(array_search($currency, $valid_currencies)===false) return false;
    }

    // delete+insert (replace)
    $this->db->replace('settings',['name'=>'available_currencies', 'value'=>json_encode($currencies)]);

    // return whether something happened
    return (bool) $this->db->affected_rows();
  }

  public function get_available()
  {
    // get value from settings table
    $this->db->where('name','available_currencies');
    $query = $this->db->get('settings');
    $setting = $query->row();

    // return setting value, or USD as default
    if($setting) return json_decode($setting->value);
    else return ['USD'];
  }

  public function set_fee($fee)
  {
    // force value to be valid
    $fee = (float) $fee;

    // delete+insert (replace)
    $this->db->replace('settings',['name'=>'exchange_fee', 'value'=>$fee]);

    // return whether something happened
    return (bool) $this->db->affected_rows();
  }

  public function get_fee()
  {
    // get value from settings table
    $this->db->where('name','exchange_fee');
    $query = $this->db->get('settings');
    $setting = $query->row();

    // return setting value or zero as default
    if($setting) return (float) $setting->value;
    else return 0.0;
  }

}
