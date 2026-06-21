<?php
   // This file contains functions used in more than
   // one script in the cart module

require('always.include.php');
//$debug = true;
//$debug = false;
//session_start();

$ClubCode       = GetParameter('ClubCode');
 


/* display admin menu
 */
function DisplayAdminMenu() 
{
  global $loginUsername;
  //global $public_domain_name;
	
	// public menus
	
	echo '<a href="/index.php">CLUB Home</a>';
	echo '&nbsp;';
	echo '&nbsp;';
    echo '<a href="/logout.php">Logout</a><br>'."\n";

  // Admin user?
  if (	AuthLevel ($loginUsername) == "Admin")
  {
  echo '<p>&nbsp;</p>';
	echo '<a href="/admin/index.php">ADMIN HOME</a><br>'."\n";
	echo '<a href="/admin/faq-admin.php">Admin FAQ</a><br>'."\n";
	echo '<A HREF="/admin/enewsletter.php">eNewsletter</A><br>'."\n";
	echo '<A HREF="/logs/webalizer/2005/">Webalizer</A><br>'."\n";
	echo '<p>&nbsp;</p>';
	echo '<a href="/admin/mlist.php?show=active">Members-ACTIVE</a><br>'."\n";
	echo '<a href="/admin/mlist.php?show=all">Members-ALL</a><br>'."\n";
	echo '<a href="/admin/mlist-pda.php">Members-PDA</a><br>'."\n";
	echo '<A HREF="/admin/members-gained-lost.php">Members Gained/Lost</A><br>'."\n";
	echo '<A HREF="/admin/referral-stats.php">Member Referral</A><br>'."\n";
	echo '<A HREF="/admin/member-occupations-rpt.php">Member Occupations</A><br>'."\n";
	echo '<A HREF="/admin/member-preferences-rpt.php">Preferences</A><br>'."\n";
	echo '<A HREF="/admin/members-at-risk.php">Members-At-Risk</A><br>'."\n";
	echo '<p>&nbsp;</p>';
	echo '<a href="/admin/elist.php">Events</a><br>'."\n";
	echo '<A HREF="/admin/event-registrations-yes.php">Registrations</A><br>'."\n";
	echo '<A HREF="/admin/event-registrations-yes-descriptions.php">Reg/Details</A><br>'."\n";
	echo '<A HREF="/admin/count-members-registered-for-each-event.php">Member Counts</A><br>'."\n";
	echo '<A HREF="/admin/expired-member-event-registrations.php">Expired Members</A><br>'."\n";
	echo '<p>&nbsp;</p>';
    echo '<A HREF="/admin/email-to-ALL-members.php">Email ALL members</A><br>'."\n";
	echo '<p>&nbsp;</p>';
    echo '<a href="/logout.php">Logout</a><br>'."\n";
	echo '<p>&nbsp;</p>';
  }
}

function TopBanner( $pagetitle)
{
	global $ClubCompanyName;

	echo '<div id="banner"><h1>' . $ClubCompanyName . '</h1></div>';
	echo '<div id="banner2"><b>' . $pagetitle . '</b></div>';
	echo '<div id="banner4">&nbsp;</div>';
	
	echo '<div id="leftcontent">';
	DisplayAdminMenu(); 
    echo '</div>';
}


function TopBannerPlusMenu( $pagetitle)
{
	global $ClubCompanyName;

	echo '<div id="banner"><h1>' . $ClubCompanyName . '</h1></div>'."\n";
	echo '<div id="banner2"><b>' . $pagetitle . '</b></div>'."\n";
	//echo '<div id="banner4">&nbsp;</div>';

    echo '<table>'."\n";
    //echo '<table border=3>';
    echo '<tr>'."\n";
    echo '<td  style="vertical-align: top;" nowrap="nowrap">'."\n";
	echo '<div id="leftcontent">'."\n";
	DisplayAdminMenu(); 
    echo '</div>'."\n";
    echo '</td>'."\n\n";

    echo '<td style="vertical-align: top;">'."\n";


	
}

function Footer () 
{
    global $FooterText;
    
    echo '</td>'."\n";
    echo '</tr>'."\n";
    echo '</table>'."\n\n";
    
    echo '<table>';
    //echo '<table border=1>';
    echo '<tr>'."\n";
    echo '<td valign="bottom">'."\n";
    echo FooterText();
    echo '</td>'."\n";
    echo '</tr>'."\n";
    echo '</table>'."\n";

}
