<?php

 $_SERVER["DOCUMENT_ROOT"] = '/var/www/lastbrave/data/www/gem.style/'; // << Полный путь к корню сайта.
    $DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
 
    define("BX_CRONTAB", true);
    define("NO_KEEP_STATISTIC", true);
    define('BX_NO_ACCELERATOR_RESET', true);
    define("NOT_CHECK_PERMISSIONS",true);
 
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    if(CModule::IncludeModule("search")) {
        // Убрать комментарии ниже, если хотите отключить ограничение времени выполнения скрипта.
        // @set_time_limit(0);
        // @ignore_user_abort(true);
 
        CModule::IncludeModule("search");
        $res = CSearch::ReIndexAll(true); 
 
        echo 'Проиндексировано элементов: ', $res;
    }