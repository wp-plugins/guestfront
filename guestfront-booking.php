<?php
/*
Plugin Name: GuestFront
Plugin URI: http://www.guestfront.com
Description: This plugin lets you embed the GuestFront Booking / Reservation engine into any PAGE or POST for hotels, B&Bs, GuestHouses or Self-Catering hire. Remember to create a free GuestFront account to use it. To use it, activate the plugin, then click "Settings" or click the GuestFront tab on the left menu. Inside settings there are instructions on how to add a shortcode that allows you to embed the GuestFront Booking Reservation Process
Version: 1.0.0
Author: GuestFront
Author URI: http://www.guestfront.com
License: GPL2
*/

add_shortcode('gf_booking', array('GuestFrontBooking', 'shortcode'));
register_activation_hook( __FILE__, array( 'GuestFrontBooking', 'install' ) );
register_deactivation_hook( __FILE__, array( 'GuestFrontBooking', 'remove' ) );

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=guestfront-dashboard">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );


class GuestFrontBooking 
{
	function __construct() 
	{
		if ( is_admin() )
		{
			add_action('admin_menu', array(&$this, 'admin_menu'));
		}
	}

 

function admin_menu(){
    add_menu_page( 'GuestFront Reservation Engine Plugin', 'GuestFront', 'administrator', 'guestfront-dashboard', array($this, 'dashboard'), plugins_url( 'guestfront-booking/images/icon.png') ); 
}

function my_custom_menu_page(){
    echo "Admin Page Test";	
}



	//function admin_menu () 
	//{
	
	//	add_options_page('PGuestFront','GuestFront','administrator','guestfront-dashboard',array($this, 'dashboard'));
	//}

	function  dashboard () 
	{
		?>
			<h2>GuestFront Plugin Installation Guide</h2>

      <div style="float:right; width:32%">
      <h3>GuestFront Account Setup</h3>
      <p>To use this plugin you need a GuestFront account</p>
      <p><strong>Step 1:</strong> Get A Free GuestFront account. <br><a class="button" target="_blank" href="http://www.guestfront.com/sign-up">&nbsp;&nbsp;Get a FREE GuestFront account&nbsp;&nbsp;</a> </p>
      <p><strong>Step 2:</strong> Login to setup your GuestFront account. Add your inventory and availability. <a class="button" target="_blank" href="https://live.guestfront.com/system/users/login"> Login to your GuestFront Account ,</a></p>
      <p><strong>Step 3:</strong> Add your GuestFront snippet to your Page or Post</p>
      </div>
			<div style="width:65%">
      <h3>How to Embed you GuestFront Page / Post Embed Shortcode</h3>
			<p>
      The following shortcode will allow you to embed the GuestFront search calendar and results in a Page or Post <br>
      There are three parameters that you can use in the shortcode.The shortcode will generate an iframe.
      <br>
      <br>
      <strong>bid</strong> = the Business ID. This tag attribute is mandatory. Example: [gf_booking bid="htl684297"]<br>
      To find your Business ID, <a href="https://live.guestfront.com/system/users/login" target="_blank">log into your GuestFront account</a>. Your Business ID is in the top left corner of your GuestFront backend dashboard.
      <br>
      <br>
      <strong>width</strong> - controls the iframe width. The default width is already set to "100%"
      <br>
      <br>
      <strong>height</strong> - controls iframe height. The default height is already set to "1600"
      <br>
      <br>
      <strong>Shortcode Example:</strong> [gf_booking bid="htl684297" width="800" height="1000"]
      <p> Next add a new page or post and insert your code snippet and save the page or post</p>
     <p>The code above will embed a GuestFront search form on your page or post with an iframe width of 800px and iframe height of 1000px</p>
      <br>
      Please note: The shortcode's iframe is restricted by the container in which it is embedded.
      </p>

			<p><h3>External / Header search form</h3>
      You can create a search form anywhere on your website, for example in the header area of your website, 
      as long as you submit the necessary parameters to the page where you have the GuestFront plugin snippet code.
      </p> 
      <h4>How to setup </h4>
          <strong>Step 1:</strong> Setup your form  <br>
          Example form: <br><textarea rows="5" cols="50"> <form name="search" action="http://hotelwebsitenme.com/book-accommodation-online" method="post"></textarea>
                 <br>
               <strong>Step 2:</strong> Pass the arriving on data  <br>
           Pass the arrival date to the results page.                                                                        <br>
           Example arriving on date: <br><textarea rows="5" cols="50"><input type="text" id="datefrom" name="data[arriving_on]" value="01/01/2016"></textarea>
           <br>
           You might have be using a javascript calendar of the above, ensure that it passes a futur dated and in US date format: data[arriving_on] = MM/DD/YYYY
           <br>
           <strong>Step 3:</strong> Pass the number of nights<br>
           Example: You might set this up as a dropdown list<br>
           <textarea rows="5" cols="50"><select name="data[nights]"  id="nights">
  <option value="1">1 night</option>
  <option value="2" selected>2 nights</option>
  <option value="3">3 nights</option>
  <option value="4">4 nights</option>
  </select>
</textarea>
      
           
      <p><strong>Step 4:</strong> In Wordpress or on your website Add a new page to hold your search results. Example: Give your page the title "Book Online" 
      <br>
      <br>
      <strong>Step 5:</strong> For Wordpress, place your GuestFront plugin snippet on the page: Example: [gf_booking bid="PLACE-YOUR-GUESTFRONT-ID-HERE"]    
      <br>
      For other website types, put an iframe on the page where you want your results to appear. Something like this 
      <br>
      <textarea rows="5" cols="50"> <iframe width="100%" height="1600" class="iframe-wrapper" style="border:0;overflow-y:scoll;" src="http://live.guestfront.com/PLACE-YOUR-GUESTFRONT-ID-HERE"></iframe></textarea>
      <br>
      </p>

       </div>     
		<?php
	}

    function shortcode($atts) 
    {
        $id = $atts['bid'];
        $width = isset($atts['width']) ? $atts['width'] : "100%";
        $height = isset($atts['height']) ? $atts['height'] : 1600;

        $url = "http://live.guestfront.com/$id/bookings/new";
        $start = '';

        if(isset($_REQUEST['data']['arriving_on']) && isset($_REQUEST['data']['nights']))
        {
            $url = "http://live.guestfront.com/$id/bookings/fwd?arriving_on=".$_REQUEST['data']['arriving_on']."&nights=".$_REQUEST['data']['nights'];
        }

        if (empty($id)) {
            return '<!-- hotel value not passed -->';
        }
        
        return $start.' <iframe width="'.$width.'" height="'.$height.'" class="iframe-wrapper" style="border:0;overflow-y:scroll;" src="'.$url.'"></iframe>';
    }   

	static function install() {}

	static function remove() {}

}

new GuestFrontBooking;




