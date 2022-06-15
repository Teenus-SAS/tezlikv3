<?php

  namespace tezlikv2\Constants;
  /**
   * Class Constants
   * @package tezlikv2\Constants
   * @author Teenus <Teenus-SAS>
   */
  class Constants
  {
    const API_PATH = __DIR__ . "/../../";
    const DAO_PATH = self::API_PATH ."src/dao/";
    //const DAO_PATH = self::API_PATH ."src/dao/general";
    const LOGS_PATH = self::API_PATH."logs/";
    const MODELS_PATH = self::API_PATH ."src/models/";
  }