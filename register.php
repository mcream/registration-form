<?php

  $login = @filter_var($_POST['login'], FILTER_SANITIZE_STRING);
  //Zapisywanie haseł do zmiennych
  $password = @filter_var($_POST['password'], FILTER_SANITIZE_STRING);
  $repassword = @$_POST['repassword'];
  $rules = @$_POST['rules'];

  // Weryfikacja
  $verify = false;

  //sprawdzanie Czy login jest ustawiony
  if(isset($login)){
    $checkLogin = true;
  }

  //Sprawdzanie poprawności hasła, a następnie zaszyfrowuje się je md5
  if($password == $repassword){
      $password_md5 = md5($password);
      $checkPwd = true;
  }

  //Sprawdzanie czy checkboxa. Następnie dodaje wartość 'true' dla rulesAccept
  if(isset($rules)){
    $rulesAccept = true;
    $checkRules = true;
  }
  //Jeżeli wszystkie 'if'-y się wykonają, to zmienna verify otrzyma wartosc 'true'
  if($checkLogin && $checkPwd && @$checkRules){
      $verify = true;
  }else{
    echo "Przykro mi, ale najwyraźniej coś poszło nie tak <br /><a href='login.php'>Zaloguj</a><br /> <a href='index.php'>Rejestracja</a>";
  }
  //ref. do połączenia z bazą danych
  require_once "db.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  //Utworzenie klasy Time
  class Time
  {
      const DEFAULT_TIME_ZONE = "Europe/Warsaw";

      public $currentTime;
      function __construct($timeZone=self::DEFAULT_TIME_ZONE)
      {
         $this->timeZone = $timeZone;
         date_default_timezone_set($this->timeZone);
         $this->currentTime = $this->getCurrentTime();
      }
      function __toString()
      {
          return $this->currentTime;
      }
      function getCurrentTime($pattern="Y-m-d")
      {
          $this->setTimeZone();
          return date($pattern,time());
      }
      function setTimeZone()
      {
         if ($this->timeZone != date_default_timezone_get())
           date_default_timezone_set($this->timeZone);
      }
  }

  // Zapisanie w zmiennej klasy Time
  $myTime = new Time();


  //Wykonywanie połączenia
  try
  {
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    //Reakcja na pojawienie się błędu
    if ($connect->connect_errno!=0)
    {
      throw new Exception(mysqli_connect_errno());
    }
    else
    {
      if ($verify)
      {
        //Wprowadzanie zmian do bazy danych
        if ($connect->query("INSERT INTO reglog_tab VALUES (NULL, '$login', '$password_md5', '$rulesAccept', '$myTime', '')"))
        {
          //Po wprowadzeniu zmian do db, zostaniemy przekierowani do strony logining.php
          header('Location: logining.php');
        }
        else
        {
          throw new Exception($connect->error);
        }

      }
      //zamknięcie połączenia
      $connect->close();
    }

  }
  catch(Exception $e)
  {
    //Zwrac informację o błędzie
    echo '<br />Informacja: '.$e;
  }








 ?>
