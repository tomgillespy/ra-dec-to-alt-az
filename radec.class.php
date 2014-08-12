<?php
 
/**
 * @author Tom Gillespy
 * @copyright 2014
 * 
 * This script provides a class to convert between Right Ascension and Declination to Altitude and Azimuth for your local position.
 * It also provides some helper functions to get LST, HA and days from J2000.
 */
 
class radec {
  protected $ra = 0;
  protected $dec = 0;
  
  protected $latitude = 0;
  protected $longditude = 0;
  
  protected $day = 0;
  protected $month = 0;
  protected $year = 0;
 
  
  private $yearj2000 = array(   '1998' => -731.5,
                                '1999' => -366.5,
                                '2000' => -1.5,
                                '2001' => 364.5,
                                '2002' => 729.5,
                                '2003' => 1094.5,
                                '2004' => 1459.5,
                                '2005' => 1825.5,
                                '2006' => 2190.5,
                                '2007' => 2555.5,
                                '2008' => 2920.5,
                                '2009' => 3286.5,
                                '2010' => 3651.5,
                                '2011' => 4016.5,
                                '2012' => 4381.5,
                                '2013' => 4747.5,
                                '2014' => 5112.5,
                                '2015' => 5477.5,
                                '2016' => 5842.5,
                                '2017' => 6208.5,
                                '2018' => 6573.5,
                                '2019' => 6938.5,
                                '2020' => 7303.5,
                                '2021' => 7669.5,
                                //This needs exending to 2030 or via a method if possible. But I can't work out how Julian dates work just yet.                           
  ); 
  
  //The month is 1 indexed, so 1 is January and 12 is December. This provides the year offset for the start of the month.
  private $monthj2000 = array(  1 => 0,
                                2 => 31,
                                3 => 59,
                                4 => 90,
                                5 => 120,
                                6 => 151,
                                7 => 181,
                                8 => 212,
                                9 => 243,
                                10 => 273,
                                11 => 304,
                                12 => 334,
  );
  
  private $monthj2000leap = array(  1 => 0,
                                    2 => 31,
                                    3 => 60,
                                    4 => 91,
                                    5 => 121,
                                    6 => 152,
                                    7 => 182,
                                    8 => 213,
                                    9 => 244,
                                    10 => 274,
                                    11 => 305,
                                    12 => 335,
  );
  
  
  function __construct($lat, $long)
    {
        $this->latitude = $lat;
        $this->longditude = $long;
    }
    
  function settimefromtimestamp($timestamp)
    {
        $this->day = gmdate('j', $timestamp);
        $this->month = gmdate('n', $timestamp);
        $this->year = gmdate('Y', $timestamp);
    }
    
  function monthoffset($month, $year)
    {
        if (gmdate('L', strtotime($year.'-'.$month.'-01')) == 1)
          {
            return $this->monthj2000leap[$month];
          }
          else
          {
            
            return $this->monthj2000[$month];
          }
    }
  
  function yearoffset($year)
    {
        return $this->yearj2000[$year];
    }
  
  function daysbeforeJ2000($timestamp)
    {
        $this->settimefromtimestamp($timestamp);
        $offset = $this->yearoffset($this->year);
        $offset += $this->monthoffset($this->month, $this->year);
        $offset += $this->day;
        $offset += $this->fractionalday($timestamp);
        return $offset;
    }
    
  function getdecimalUT($timestamp)
    {
        $ut = gmdate('G', $timestamp);
        $ut += (gmdate('i', $timestamp)/60);
        $ut += gmdate('s', $timestamp)/3600;
        return $ut;
    }
    
  function decimaldegrees($degrees, $minutes, $seconds)
    {
        return $degrees + ($minutes / 60) + ($seconds / 3600);
    }
  
  function fractionalday($timestamp)
    {
        //$hours = date('G', $timestamp) + (date('i', $timestamp) / 60);
        $hours = $this->getdecimalUT($timestamp);
        return $hours/24;
    }
  
  function getLST($timestamp)
    {
        //LST = 100.46 + 0.985647 * d + long + 15*UT
        $unrefinedlst = 100.46 + (0.985647 * $this->daysbeforeJ2000($timestamp)) + $this->longditude + (15*$this->getdecimalUT($timestamp));
        //Get into range of 0<LST<360
        While (($unrefinedlst < 0) || ($unrefinedlst > 360))
          {
            If ($unrefinedlst < 0)
              {
                $unrefinedlst = $unrefinedlst + 360;
              }
            If ($unrefinedlst > 360)
              {
                $unrefinedlst = $unrefinedlst - 360;
              }
          }
        $lst = $unrefinedlst;
        return $lst;
    }
 
   function getHA($timestamp)
     {
        return $this->getLST($timestamp) - $this->ra;
     }
 
   function getALT($timestamp)
     {
        $dec = $this->dec;
        $lat = $this->latitude;
        $ha = $this->getHA($timestamp);
        
        $sinALT = (sin(deg2rad($dec))*sin(deg2rad($lat))) + (cos(deg2rad($dec))*cos(deg2rad($lat))*cos(deg2rad($ha)));
        return rad2deg(asin($sinALT));
     }
     
   function getAZ($timestamp)
     {
        $dec = $this->dec;
        $lat = $this->latitude;
        $ha = $this->getHA($timestamp);
        $alt = $this->getALT($timestamp);
        $cosAZ = (sin(deg2rad($dec)) - (sin(deg2rad($alt))*sin(deg2rad($lat))))/(cos(deg2rad($alt)) * cos(deg2rad($lat)));
        $azdeg = rad2deg(acos($cosAZ));
        if (rad2deg(sin(deg2rad($ha))) > 0)
          {
            return 360 - $azdeg;
          }
          else
          {
            return $azdeg;
          }
     }
     
   function setradec($ra, $dec)
     {
        $this->ra = $ra * 15;
        $this->dec = $dec;
     }   
    
}
 
 
?>