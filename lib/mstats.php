<?php

function MemberStatistics()
{
     global $ClubCode;

     mysqlconnect($connection);

     $query = "SELECT members.cust_id, members.m_firstname, "
                 ." members.m_lastname, members.m_memberstatus, "
                 ." members.m_email, members.m_phonehome, "
                 ." members.m_sex, members.m_date_birth, "
                 ." members.user_name, " 
                 ." members.u_date_expiration, members.u_auth_level, " 
                 ." members.u_date_last_login "
                 ." FROM members WHERE (m_club='" . $ClubCode . "');";

        if (!($result = @ mysqli_query($connection, $query)))
           trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);

        $count_logins = 0;
        $count_unexpired_logins = 0;
        $count_unexpired_active_logins = 0;
        $count_unexpired_leader_logins = 0;
        $count_unexpired_free_logins = 0;
        $count_unexpired_paid_logins = 0;
        $count_unexpired_male_logins = 0;
        $count_unexpired_female_logins = 0;
        $sum_age_unexpired_logins = 0;
        $age_array = array();

        while ($m_row = mysqli_fetch_array($result)) {

          $cust_id = $m_row["cust_id"];

          //echo '<html><body><pre>';
          $count_logins++;
          // has membership expired?
          if (((float)time()) < ((float)strtotime($m_row["u_date_expiration"])))
          {
            $count_unexpired_logins++;
            $unexpired_members_birthdate = $m_row['m_date_birth'];
            $unexpired_members_birthyear = substr($unexpired_members_birthdate,0,4);
            $this_year = date('Y',time());
            $unexpired_members_age = $this_year - $unexpired_members_birthyear;
            //echo 'unexpired_members_cust_id: ' . $m_row['cust_id'] . '  ';
            //echo 'unexpired_members_age: ' . $unexpired_members_age . '<br>';
            $age_array[] = $unexpired_members_age;
            $sum_age_unexpired_logins += $unexpired_members_age;
            if ($m_row["m_memberstatus"] == 'Paid')
              $count_unexpired_paid_logins++;
            if ($m_row["m_sex"] == 'Male')
              $count_unexpired_male_logins++;
            if ($m_row["m_sex"] == 'Female')
              $count_unexpired_female_logins++;
            if ($m_row["u_auth_level"] == 'Leader')
              $count_unexpired_leader_logins++;
            if ($m_row["m_memberstatus"] == 'Free')
              $count_unexpired_free_logins++;
          }
          if (!empty($m_row["u_date_last_login"]) and
              ((float)time()) < ((float)strtotime($m_row["u_date_expiration"]))
              )
            $count_unexpired_active_logins++;

        }

  $percent_unexpired_male_logins   = 
                    round(($count_unexpired_male_logins / 
                    $count_unexpired_logins) * 100);
  
  $percent_unexpired_female_logins = 
                    round(($count_unexpired_female_logins / 
                    $count_unexpired_logins) * 100);

  $average_unexpired_age = round($sum_age_unexpired_logins / 
                                 $count_unexpired_logins);

  $sum = 0;
  foreach ($age_array as $age_element) {
    $sum += pow($age_element - $average_unexpired_age,2);
  }
  $age_standard_deviation =  round(sqrt($sum / count($age_array)));

  $ret_array = array (
               'count_logins' => $count_logins,
               'count_unexpired_logins' => $count_unexpired_logins,
               'count_unexpired_active_logins' => $count_unexpired_active_logins,
               'count_unexpired_leader_logins' => $count_unexpired_leader_logins,
               'count_unexpired_free_logins' => $count_unexpired_free_logins,
               'count_unexpired_paid_logins' => $count_unexpired_paid_logins,
               'count_unexpired_male_logins' => $count_unexpired_male_logins,
               'count_unexpired_female_logins' => $count_unexpired_female_logins,
               'percent_unexpired_male_logins' => $percent_unexpired_male_logins,
               'percent_unexpired_female_logins' => $percent_unexpired_female_logins,
               'average_unexpired_age' => $average_unexpired_age,
               'age_std_dev' => $age_standard_deviation
               );
 
  return($ret_array);
}

?>
