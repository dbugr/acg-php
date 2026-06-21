<?php
// displays public web page header
?>
<p>&nbsp;
  <div align="center">
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="90%" bgcolor="#FFFFFF" bordercolor="#000099" style="border-collapse: collapse">
        <tr>
          <td>
            <!-- <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td><img border="0" src="/images/spacer.gif" width="1" height="10"></td></tr></table> -->
            <div align="center">
              <center>
                <table border="0" cellpadding="0" cellspacing="0" width="95%" style="border-collapse: collapse" bordercolor="#111111" bgcolor="#FFFFFF">
                  <tr>
                    <td width="72" style="padding:10px 0;">
                      <img border="0" src="/images/logo.gif" width="72" height="85"></td>
                    <td nowrap>
                      <p align="left"><a href="/index.php"><span style="color: #003366; font-size: 22px">&nbsp;The <?php if (isset($ClubCompanyName)) echo $ClubCompanyName; ?></span></a>&nbsp;&nbsp;&nbsp;<span style="color: #009966; font-size: 18px; font-family: Times New Roman;"><i>Gainesville's Ultimate Outdoor Social Club</i></span>
                    </td>
                    <td style="text-align: right"><a href="https://www.facebook.com/AdventureClubGainesville"><img border="0" src="/images/fb.jpg" width="35" height="36"></a>
                      &nbsp;&nbsp;
                    </td>
                  </tr>
                </table>
              </center>
            </div>
            <div align="center">
              <center>
                <table border="0" cellpadding="4" cellspacing="4" width="95%" background="/images/headerback.jpg" style="border-collapse: collapse" bordercolor="#111111">
                  <tr>
                    <td>
                      <table border="0" cellpadding="2" style="margin-left:8px;">
                        <tr>
                          <form method="POST" action="/login.php" id="loginform" name="loginform">
                            <td align="left" valign="top" nowrap bgcolor="#003366"><br>
                              <font color="#FFFFFF"><span style="font-size: 10px">Username:</span><b><br>
                                </b></font>
                              <input type="text" name="loginUsername" size="20" maxlength=30 style="width: 135px; background-color: #dedede">
                              <p>
                                <font color="#FFFFFF"><span style="font-size: 10px">Password:</span><b><br>
                                  </b>
                                </font><input type="password" name="loginPassword" size="20" maxlength=20 style="width: 135px; background-color: #dedede">
                              </p>
                              <input type="submit" value="Login" name="login">
                              <p>
                                <font size="1" color="#CCFFFF">No Login?</font>
                                <font size="1">
                                </font> <a href="<?php echo GetParameter('vd') . 'join.php'; ?>">
                                  <font color="#33FF66" size="1">Become a member!</font>
                                </a>
                            </td>
                            <td>
                              <div style="text-align: center; margin:0 0 0 80px;">
                                <center>
                                <?php echo PhotoAnchorStr(); ?>
                                </center>
                              </div>
                            </td>
                            <script language='javascript'>
                              document.loginform.loginUsername.focus();
                            </script>
                          </form>
                        </tr>
                      </table>
                  </td>
                </tr>
              </table>
    </center>
  </div>
  <div align="center">
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="95%">
        <tr>
          <td width="100%" colspan="2">&nbsp; </td>
        </tr>
        <tr>
          <td width="25%" nowrap valign="top">