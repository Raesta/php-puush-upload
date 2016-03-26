<?php
  /**
   * php-puush-upload
   *
   * This script will allow you to backup your files on puush.me
   *
   * @author Raesta <contact@raesta.net>
   * @copyright 2016 Raesta
   */
  define("PATH", ".");

  if (file_exists(PATH . "/" . "settings.json")) {
    $json = json_decode(file_get_contents("settings.json"));

    if (!isset($json->dir) || empty($json->dir)) die("Error: You haven't set the folder for images !" . PHP_EOL);
    if (!isset($json->mimes) || empty($json->mimes)) die("Error: You haven't set the mime(s) type of files !" . PHP_EOL);
    if (!isset($json->ignoredFiles) || empty($json->ignoredFiles)) print_r("Warning: You haven't configured the ignore files !" . PHP_EOL);
    if (!isset($json->apiKey) || empty($json->apiKey)) die("Error: You haven't set the api key of puush.me !" . PHP_EOL);
    if (!isset($json->nameLogFile) || empty($json->nameLogFile)) die("Error: You didn't configure the log file name !" . PHP_EOL);

    $files = getPictures(PATH . "/" . $json->dir, $json->mimes, array_merge([".", ".."], $json->ignoredFiles));
    if (count(files) > 0) {
      foreach ($files as $key => $value) {
        $link = upload($json->apiKey, PATH . "/" . $json->dir, $value);
        logFile($json->nameLogFile, $value . "=" . $link);
      }
      moveToBackupDir(PATH . "/" . $json->dir, PATH . "/" . $json->backupDir, $files);
    } else die("Error: No such file in " . $json->dir . "/" . PHP_EOL);
  } else die("Error: settings.json does not exist or is not readable!" . PHP_EOL);

  /**
   * Retrieve the list of files in the folder
   * @param  string $filesDir     the files folder
   * @param  array  $mimes        list of mime type to accept
   * @param  array  $ignoredFiles list of files to ignore
   * @return array                return the list of files in the folder
   */
  function getPictures($filesDir, $mimes, $ignoredFiles) {
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    if (is_dir($filesDir)) {
      $dirs = scandir($filesDir);
      if (count($dirs) <= 2) return "Warning: No images found !" . PHP_EOL;
      foreach ($dirs as $key => $value) {
        if (in_array($value, $ignoredFiles) || is_dir($filesDir . "/" . $value)) unset($dirs[$key]);
        if (!in_array(finfo_file($fileInfo, $filesDir . "/" . $value), $mimes) === true) unset($dirs[$key]);
      }
      return $dirs;
    } else return "folder does not exist !" . PHP_EOL;
  }

  /**
   * Upload the list of file on the puush api
   * @param  string $apiKey   key to use the puush API
   * @param  string $filesDir the files folder
   * @param  array  $files    list of files in the folder
   * @return string           the URL of the hosted file
   */
  function upload($apiKey, $filesDir, $file) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, "php-puush-upload/0.1 (PHP script for uploading screenshots to puush; https://github.com/Raesta/php-puush-upload)");
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array("k" => $apiKey, "z" => "poop", "f" => "@" . $filesDir . "/" . $file));
    curl_setopt($ch, CURLOPT_URL, "https://puush.me/api/up");
    $ret = curl_exec($ch);
    $data = explode(",", $ret);
    curl_close($ch);
    if ($data[0] == "0") return $data[1];
    else die("Error: Failed to upload file to puush (Wrong API Key ?): " . $ret . PHP_EOL);
  }

  /**
   * [logFile description]
   * @param  string $nameLogFile name of log file
   * @param  string $entry       the line in log
   */
  function logFile($nameLogFile, $entry) {
    file_put_contents($nameLogFile, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
  }

  /**
   * [moveToBackupDir description]
   * @param  string $filesDir  the files folder
   * @param  string $backupDir backup files folder
   * @param  array  $files     list of files in the folder
   */
  function moveToBackupDir($filesDir, $backupDir, $files) {
    if (!is_dir(PATH . "/" . $backupDir)) mkdir(PATH . "/" . $backupDir);
    foreach ($files as $key => $value) {
      rename($filesDir . "/" . $value, $backupDir . "/" . $value);
    }
  }

?>
