<?php


/* *******************************************
// LICENSE INFORMATION
// The code, "Detecting Smartphones Using PHP" 
// by Anthony Hand, is licensed under a Creative Commons 
// Attribution 3.0 United States License.
//
// Updated 14 December 2009 by A Hand
//   - Added the method for detecting BlackBerry Touch
//		devices like the Storm 1 and 2.
//
// Updated 5 December 2009 by A Hand
//   - Fixed the DetectPalmOS method. It should filter
//	   out WebOS devices.
//
// Updated 8 November 2009
//   - Added the deviceWebOS variable.
//   - Added Palm's WebOS to the DetectPalm method.
//   - Created a new method to check for Palm's WebOS devices.
//   - Added Palm's WebOS to the DetectTierIphone method.
//
// Updated 4 April 2009
//   - Added Android to the DetectTierIphone method.
//   - Updated comments for Detect Tier methods. 
//
// Updated 23 February 2009
//   - Added a method to detect Opera Mobile and Mini. Opera Mobile is new.
//   - Updated the algorithm for detecting a Sony Mylo.
//
// Updated 13 November 2008
//   - Updated the BlackBerry algorithm to detect vnd.RIM. This change
//        captures the case when BB devices emulate desktop Internet Explorer or Firefox.
//   - Updated the Windows Mobile algorithm to detect certain newer HTC devices running 
//        PocketPC/WinMo professional.
//
// Updated 6 November 2008
//   - Added variables and methods to detect Android-powered devices.
//
// Updated 15 September 2008
//   - Added variables and a method to detect the Danger Hiptop. 
//
// Anthony Hand, ahand@hand-interactive.com
// Web: www.hand-interactive.com
// 
// License info: http://creativecommons.org/licenses/by/3.0/us/
//
// This code is provided ÒAS ISÓ with no expressed or implied warranty.  
// You have the right to use this code or any portion of it 
// so long as you provide credit toward myself as the original author.
//
// *******************************************
*/



//**************************
// The uagent_info class encapsulates information about
//   a browser's connection to your web site. 
//   You can use it to find out whether the browser asking for
//   your site's content is probably running on a mobile device.
//   The methods were written so you can be as granular as you want.
//   For example, enquiring whether it's as specific as an iPod Touch or
//   as general as a smartphone class device.
//   The object's methods return 1 for true, or 0 for false.
class uagent_info
{
   var $useragent = "";
   var $httpaccept = "";

   //standardized values for true and false.
   var $true = 1;
   var $false = 0;

   //Initialize some initial smartphone string variables.
   var $engineWebKit = 'webkit';
   var $deviceAndroid = 'android';
   var $deviceIphone = 'iphone';
   var $deviceIpod = 'ipod';
   var $deviceSymbian = 'symbian';
   var $deviceS60 = 'series60';
   var $deviceS70 = 'series70';
   var $deviceS80 = 'series80';
   var $deviceS90 = 'series90';
   var $deviceWinMob = 'windows ce';
   var $deviceWindows = 'windows'; 
   var $deviceIeMob = 'iemobile';
   var $enginePie = "wm5 pie"; //An old Windows Mobile
   var $deviceBB = 'blackberry';   
   var $vndRIM = 'vnd.rim'; //Detectable when BB devices emulate IE or Firefox
   var $deviceBBStorm = 'blackberry95';  //Storm 1 and 2
   var $devicePalm = 'palm';
   var $deviceWebOS = 'webos'; //For Palm's new WebOS devices

   var $engineBlazer = 'blazer'; //Old Palm
   var $engineXiino = 'xiino'; //Another old Palm
   
   //Initialize variables for mobile-specific content.
   var $vndwap = 'vnd.wap';
   var $wml = 'wml';   
   
   //Initialize variables for other random devices and mobile browsers.
   var $deviceBrew = 'brew';
   var $deviceDanger = 'danger';
   var $deviceHiptop = 'hiptop';
   var $devicePlaystation = 'playstation';
   var $deviceNintendoDs = 'nitro';
   var $deviceNintendo = 'nintendo';
   var $deviceWii = 'wii';
   var $deviceXbox = 'xbox';
   var $deviceArchos = 'archos';
   
   var $engineOpera = "opera"; //Popular browser
   var $engineNetfront = "netfront"; //Common embedded OS browser
   var $engineUpBrowser = 'up.browser'; //common on some phones
   var $engineOpenWeb = 'openweb'; //Transcoding by OpenWave server
   var $deviceMidp = "midp"; //a mobile Java technology
   var $uplink = "up.link";
   
   var $devicePda = "pda"; //some devices report themselves as PDAs
   var $mini = "mini";  //Some mobile browsers put 'mini' in their names.
   var $mobile = 'mobile'; //Some mobile browsers put 'mobile' in their user agent strings.
   var $mobi = 'mobi'; //Some mobile browsers put 'mobi' in their user agent strings.
   
   //Use Maemo, Tablet, and Linux to test for Nokia's Internet Tablets.
   var $maemo = 'maemo';
   var $maemoTablet = 'tablet';
   var $linux = 'linux';
   var $qtembedded = "qt embedded"; //for Sony Mylo
   var $mylocom2 = 'com2'; //for Sony Mylo also
   
   //In some UserAgents, the only clue is the manufacturer.
   var $manuSonyEricsson = "sonyericsson";
   var $manuericsson = "ericsson";
   var $manuSamsung1 = "sec-sgh";
   var $manuSony = "sony";

   //In some UserAgents, the only clue is the operator.
   var $svcDocomo = "docomo";
   var $svcKddi = "kddi";
   var $svcVodafone = "vodafone";



   //**************************
   //The constructor. Initializes several default variables.
   function uagent_info()
   { 
       $this->useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
       $this->httpaccept = strtolower($_SERVER['HTTP_ACCEPT']);
   }

   //**************************
   //Returns the contents of the User Agent value, in lower case.
   function Get_Uagent()
   { 
       return $this->useragent;
   }

   //**************************
   //Returns the contents of the HTTP Accept value, in lower case.
   function Get_HttpAccept()
   { 
       return $this->httpaccept;
   }

   //**************************
   // Detects if the current device is an iPhone.
   function DetectIphone()
   {
      if (stripos($this->useragent, $this->deviceIphone) > -1)
      {
         //The iPod touch says it's an iPhone! So let's disambiguate.
         if ($this->DetectIpod() == $this->true)
         {
            return $this->false;
         }
         else
            return $this->true; 
      }
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an iPod Touch.
   function DetectIpod()
   {
      if (stripos($this->useragent, $this->deviceIpod) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an iPhone or iPod Touch.
   function DetectIphoneOrIpod()
   {
       //We repeat the searches here because some iPods may report themselves as an iPhone, which would be okay.
       if (stripos($this->useragent, $this->deviceIphone) > -1 ||
           stripos($this->useragent, $this->deviceIpod) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an Android OS-based device.
   function DetectAndroid()
   {
      if (stripos($this->useragent, $this->deviceAndroid) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an Android OS-based device and
   //   the browser is based on WebKit.
   function DetectAndroidWebKit()
   {
      if ($this->DetectAndroid() == $this->true)
      {
         if ($this->DetectWebkit() == $this->true)
         {
            return $this->true; 
         }
         else
            return $this->false; 
      }
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is based on WebKit.
   function DetectWebkit()
   {
      if (stripos($this->useragent, $this->engineWebKit) > -1)
         return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // Detects if the current browser is the Nokia S60 Open Source Browser.
   function DetectS60OssBrowser()
   {
      //First, test for WebKit, then make sure it's either Symbian or S60.
      if ($this->DetectWebkit() == $this->true)
      {
        if (stripos($this->useragent, $this->deviceSymbian) > -1 ||
            stripos($this->useragent, $this->deviceS60) > -1)
        {
           return $this->true;
        }
        else
           return $this->false; 
      }
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is any Symbian OS-based device,
   //   including older S60, Series 70, Series 80, Series 90, and UIQ, 
   //   or other browsers running on these devices.
   function DetectSymbianOS()
   {
       if (stripos($this->useragent, $this->deviceSymbian) > -1 || 
           stripos($this->useragent, $this->deviceS60) > -1 ||
           stripos($this->useragent, $this->deviceS70) > -1 || 
           stripos($this->useragent, $this->deviceS80) > -1 ||
           stripos($this->useragent, $this->deviceS90) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is a Windows Mobile device.
   function DetectWindowsMobile()
   {
      //Most devices use 'Windows CE', but some report 'iemobile' 
      //  and some older ones report as 'PIE' for Pocket IE. 
      if (stripos($this->useragent, $this->deviceWinMob) > -1 ||
          stripos($this->useragent, $this->deviceIeMob) > -1 ||
          stripos($this->useragent, $this->enginePie) > -1)
         return $this->true; 
      if ($this->DetectWapWml() == $this->true &&
          stripos($this->useragent, $this->deviceWindows) > -1) 
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is a BlackBerry of some sort.
   function DetectBlackBerry()
   {
       if (stripos($this->useragent, $this->deviceBB) > -1)
         return $this->true; 
       if (stripos($this->httpaccept, $this->vndRIM) > -1)
         return $this->true; 
       else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is a BlackBerry Touch
   //    device, such as the Storm.
   function DetectBlackBerryTouch()
   {
       if (stripos($this->useragent, $this->deviceBBStorm) > -1)
         return $this->true; 
       else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is on a PalmOS device.
   function DetectPalmOS()
   {
      //Most devices nowadays report as 'Palm', but some older ones reported as Blazer or Xiino.
      if (stripos($this->useragent, $this->devicePalm) > -1 ||
          stripos($this->useragent, $this->engineBlazer) > -1 ||
          stripos($this->useragent, $this->engineXiino) > -1)
      {
         //Make sure it's not WebOS first
         if ($this->DetectPalmWebOS() == $this->true)
            return $this->false;
         else
            return $this->true; 
      }
      else
         return $this->false; 
   }


   //**************************
   // Detects if the current browser is on a Palm device
   //   running the new WebOS.
   function DetectPalmWebOS()
   {
      if (stripos($this->useragent, $this->deviceWebOS) > -1)
         return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // Check to see whether the device is any device
   //   in the 'smartphone' category.
   function DetectSmartphone()
   {
      if ($this->DetectIphoneOrIpod() == $this->true) 
         return $this->true; 
      if ($this->DetectS60OssBrowser() == $this->true)
         return $this->true; 
      if ($this->DetectSymbianOS() == $this->true) 
         return $this->true; 
      if ($this->DetectWindowsMobile() == $this->true)
         return $this->true; 
      if ($this->DetectBlackBerry() == $this->true)
         return $this->true; 
      if ($this->DetectPalmOS() == $this->true)
         return $this->true; 
      if ($this->DetectPalmWebOS() == $this->true)
         return $this->true; 
      if ($this->DetectAndroid() == $this->true)
         return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // Detects whether the device is a Brew-powered device.
   function DetectBrewDevice()
   {
       if (stripos($this->useragent, $this->deviceBrew) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects the Danger Hiptop device.
   function DetectDangerHiptop()
   {
      if (stripos($this->useragent, $this->deviceDanger) > -1 ||
          stripos($this->useragent, $this->deviceHiptop) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is Opera Mobile or Mini.
   function DetectOperaMobile()
   {
      if (stripos($this->useragent, $this->engineOpera) > -1)
      {
         if ((stripos($this->useragent, $this->mini) > -1) ||
          (stripos($this->useragent, $this->mobi) > -1))
         {
            return $this->true; 
         }
         else
            return $this->false; 
      }
      else
         return $this->false; 
   }

   //**************************
   // Detects whether the device supports WAP or WML.
   function DetectWapWml()
   {
       if (stripos($this->httpaccept, $this->vndwap) > -1 ||
           stripos($this->httpaccept, $this->wml) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // The quick way to detect for a mobile device.
   //   Will probably detect most recent/current mid-tier Feature Phones
   //   as well as smartphone-class devices.
   function DetectMobileQuick()
   {
      //Ordered roughly by market share, WAP/XML > Brew > Smartphone.
      if ($this->DetectWapWml() == $this->true) 
         return $this->true; 
      if ($this->DetectBrewDevice() == $this->true) 
         return $this->true; 
      if ($this->DetectOperaMobile() == $this->true) 
         return $this->true;    
      if (stripos($this->useragent, $this->engineUpBrowser) > -1)
         return $this->true; 
      if (stripos($this->useragent, $this->engineOpenWeb) > -1)
         return $this->true; 
      if (stripos($this->useragent, $this->deviceMidp) > -1)
         return $this->true; 
      if ($this->DetectSmartphone() == $this->true) 
         return $this->true;    
      if ($this->DetectDangerHiptop() == $this->true) 
         return $this->true;

      if ($this->DetectMidpCapable() == $this->true) 
         return $this->true; 

       if (stripos($this->useragent, $this->devicePda) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->mobile) > -1)
         return $this->true; 

       //Detect older phones from certain manufacturers and operators. 
       if (stripos($this->useragent, $this->uplink) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->manuSonyEricsson) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->manuericsson) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->manuSamsung1) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->svcDocomo) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->svcKddi) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->svcVodafone) > -1)
         return $this->true; 
    
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is a Sony Playstation.
   function DetectSonyPlaystation()
   {
      if (stripos($this->useragent, $this->devicePlaystation) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a Nintendo game device.
   function DetectNintendo()
   {
      if (stripos($this->useragent, $this->deviceNintendo) > -1 || 
           stripos($this->useragent, $this->deviceWii) > -1 ||
           stripos($this->useragent, $this->deviceNintendoDs) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a Microsoft Xbox.
   function DetectXbox()
   {
      if (stripos($this->useragent, $this->deviceXbox) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is an Internet-capable game console.
   function DetectGameConsole()
   {
      if ($this->DetectSonyPlaystation() == $this->true) 
         return $this->true; 
      else if ($this->DetectNintendo() == $this->true) 
         return $this->true; 
      else if ($this->DetectXbox() == $this->true) 
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device supports MIDP, a mobile Java technology.
   function DetectMidpCapable()
   {
       if (stripos($this->useragent, $this->deviceMidp) > -1 || 
           stripos($this->httpaccept, $this->deviceMidp) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is on one of the Maemo-based Nokia Internet Tablets.
   function DetectMaemoTablet()
   {
      if (stripos($this->useragent, $this->maemo) > -1)
         return $this->true; 
      //Must be Linux + Tablet, or else it could be something else. 
      else if (stripos($this->useragent, $this->maemoTablet) > -1 &&
          stripos($this->useragent, $this->linux) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an Archos media player/Internet tablet.
   function DetectArchos()
   {
      if (stripos($this->useragent, $this->deviceArchos) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is a Sony Mylo device.
   function DetectSonyMylo()
   {
      if (stripos($this->useragent, $this->manuSony) > -1)
      {
         if ((stripos($this->useragent, $this->qtembedded) > -1) ||
          (stripos($this->useragent, $this->mylocom2) > -1))
         {
            return $this->true; 
         }
         else
            return $this->false; 
      }
      else
         return $this->false; 
   }

   //**************************
   // The longer and more thorough way to detect for a mobile device.
   //   Will probably detect most feature phones,
   //   smartphone-class devices, Internet Tablets, 
   //   Internet-enabled game consoles, etc.
   //   This ought to catch a lot of the more obscure and older devices, also --
   //   but no promises on thoroughness!
   function DetectMobileLong()
   {
      if ($this->DetectMobileQuick() == $this->true) 
         return $this->true; 
      if ($this->DetectMaemoTablet() == $this->true) 
         return $this->true; 
      if ($this->DetectGameConsole() == $this->true) 
         return $this->true; 

      else
         return $this->false; 
   }



  //*****************************
  // For Mobile Web Site Design
  //*****************************


   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for devices which can 
   //   display iPhone-optimized web content.
   //   Includes iPhone, iPod Touch, Android, WebOS, etc.
   function DetectTierIphone()
   {
      if ($this->DetectIphoneOrIpod() == $this->true) 
         return $this->true; 
      if ($this->DetectAndroid() == $this->true) 
         return $this->true; 
      if ($this->DetectAndroidWebKit() == $this->true) 
         return $this->true; 
      if ($this->DetectPalmWebOS() == $this->true) 
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for all smartphones, but
   //   excludes iPhones and iPod Touches.
   function DetectTierSmartphones()
   {
      if ($this->DetectSmartphone() == $this->true) 
      {
        if ($this->DetectTierIphone() == $this->true)
        {
           return $this->false;
        }
        else
           return $this->true;
      }
      else
         return $this->false; 
   }

   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for all other types of phones,
   //   but excludes the iPhone and Smartphone Tier devices.
   function DetectTierOtherPhones()
   {
      if ($this->DetectMobileQuick() == $this->true) 
      {
        if ($this->DetectTierIphone() == $this->true)
        {
           return $this->false;
        }
        if ($this->DetectTierSmartphones() == $this->true)
        {
           return $this->false;
        }
        else
           return $this->true;
      }
      else
         return $this->false; 
   }
      

}



?>