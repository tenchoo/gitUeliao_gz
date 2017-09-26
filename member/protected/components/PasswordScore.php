<?php
/**
 * Description of PasswordScore
 *
 * @author Administrator
 */
class PasswordScore {

	/**
	 * 计算密码强度得分,共10分，1-3为弱，4-6为中，7-10为强
	 * @param string $password 密码字符串
	 * @return score 密码强度得分
	 */
	public function getScore( $password ){
			$score = 0;
			if(preg_match("/[0-9]+/",$password))
			{
				$score ++;
			}
           if(preg_match("/[0-9]{3,}/",$password))
           {
              $score ++;
           }
           if(preg_match("/[a-z]+/",$password))
           {
              $score ++;
           }
           if(preg_match("/[a-z]{3,}/",$password))
           {
              $score ++;
           }
           if(preg_match("/[A-Z]+/",$password))
           {
              $score ++;
           }
           if(preg_match("/[A-Z]{3,}/",$password))
           {
              $score ++;
           }
           if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/",$password))
           {
              $score += 2;
           }
           if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/",$password))
           {
              $score ++ ;
           }
           if(strlen($password) >= 10)
           {
              $score ++;
           }
		   return $score;
	}

}

?>
