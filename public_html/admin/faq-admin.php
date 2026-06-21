<?php

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = __FILE__;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}
$FileName = __FILE__;
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin FAQ List ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent">
  <table width="80%">
    <tr>
      <td>
        <hr>
        <a name="Questions">&nbsp;</a><br>
        <a href="#q1">How do I get admin Moderator status for the newsletter mailing list?</a><br>
        <a href="#q2">How do I get a list of EZMLM mailing list manager commands?</a><br>
        <a href="#q3">How do I get a list of current email addresses on the newsletter mailing list?</a><br>
        <a href="#q4">How do I add (subscribe) an email address to the newsletter mailing list?</a><br>
        <a href="#q5">How do I remove (UNsubscribe) an email address to the newsletter mailing list?</a><br>
        <a href="#q6">The EZMLM mailing list manager sent me an Approve/Reject email. It is clear that a spammer tried to send an email to the mailing list. Should I mark the email as SPAM?</a><br>

        <hr>
        <table>

          <tr>
            <td>
              <a name="q1">&nbsp;</a>
              <b>How do I get admin Moderator status for the newsletter mailing list?</b><br>
              Have the club owner email a request to the System Administrator, he can make the necessary arrangements. Note that the club owner is the only person who can moderate the event leaders mailing list (leaders@domain.name).
              <br><a href="#Questions">top</a>
              <hr>
            </td>
          </tr>

          <tr>
            <td>
              <a name="q2">&nbsp;</a>
              <b>How do I get a list of EZMLM mailing list manager commands?</b><br>
              Send an email to [my-list-name]-help@domain.name. For example:
              news-help@adventureclub.info
              <br><a href="#Questions">top</a>
              <hr>
            </td>
          </tr>

          <tr>
            <td>
              <a name="q3">&nbsp;</a>
              <b>How do I get a list of current email addresses on the newsletter mailing list?</b><br>
              Send an email to [my-list-name]-list@domain.name. For example:
              news-list@adventureclub.info
              <br><br>
              The system will respond by sending you an email containing all email addresses on the mailing list.
              <br><a href="#Questions">top</a>
              <hr>
            </td>
          </tr>

          <tr>
            <td>
              <a name="q4">&nbsp;</a>
              <b>How do I add (subscribe) an email address to the newsletter mailing list?</b><br>
              Copy/paste the email address. Change the "at sign" "@" to an "equals sign". For example: chuck@here.now becomes chuck=here.now. Then send an email to:
              <br><br>
              [my-list-name]-subscribe-name=here.now@domain.name
              <br><br>
              Example: news-subscribe-chucky.acg=gmail.com@adventureclub.info
              <br><br>
              The system will send you a reply. Follow the instructions in the reply - To accept/approve the submission, you can just reply to the reply. To reject the submission, simply delete the reply.
              <br><a href="#Questions">top</a>
              <hr>
            </td>
          </tr>


          <tr>
            <td>
              <a name="q5">&nbsp;</a>
              <b>How do I remove (UNsubscribe) an email address to the newsletter mailing list?</b><br>
              Copy/paste the email address. Change the "at sign" "@" to an "equals sign". For example: chuck@here.now becomes chuck=here.now. Then send an email to:
              <br><br>
              [my-list-name]-unsubscribe-name=here.now@domain.name
              <br><br>
              Example: news-unsubscribe-chucky.acg=gmail.com@adventureclub.info
              <br><br>
              The system will send you a reply. Follow the instructions in the reply - To accept/approve the submission, you can just reply to the reply. To reject the submission, simply delete the reply.
              <br><a href="#Questions">top</a>
              <hr>
            </td>
          </tr>

          <tr>
            <td>
              <a name="q6">&nbsp;</a>
              <b>The EZMLM mailing list manager sent me an Approve/Reject email. It is clear that a spammer tried to send an email to the mailing list. Should I mark the email as SPAM?</b><br>
              NO!!! If you mark the email as SPAM, all emails from the EZMLM mailing list manager TO YOU will end up in your SPAM folder! The best choice here is to simply delete the email.

              <br><a href="#Questions">top</a>
              <hr>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</div>

<?php

require('footer.php');

?>