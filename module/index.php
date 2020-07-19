<?php
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/amgad/module/':
        require 'amgad/module/index.php';
        break;
    case '/amgad/module/checkBeforeConfirm.php':
        require 'amgad/module/checkBeforeConfirm.php';
        break;
    case '/amgad/module/registerUser.php':
        require 'amgad/module/registerUser.php';
        break;
    case '/amgad/module/getNearByDrivers.php':
        require 'amgad/module/getNearByDrivers.php';
        break;
    case '/amgad/module/validUser.php':
            require 'amgad/module/validUser.php';
            break;
    case '/amgad/module/verifyUser.php':
            require 'amgad/module/verifyUser.php';
            break;
    case '/amgad/module/posts.php':
            require 'amgad/module/posts.php';
            break;
    case '/amgad/module/price.php':
            require 'amgad/module/price.php';
            break;
    case '/amgad/module/status.php':
            require 'amgad/module/status.php';
            break;
    case '/amgad/module/callLog.php':
            require 'amgad/module/callLog.php';
            break;
    case '/amgad/module/fcm.php':
            require 'amgad/module/fcm.php';
            break;
    case '/amgad/module/sendMessage.php':
            require 'amgad/module/sendMessage.php';
            break;
    case '/amgad/module/sendConfirmation.php':
            require 'amgad/module/sendConfirmation.php';
            break;
    case '/amgad/module/sendStarted.php':
            require 'amgad/module/sendStarted.php';
            break;
    case '/amgad/module/sendCompleted.php':
            require 'amgad/module/sendCompleted.php';
            break;
    case '/amgad/module/sendCancelled.php':
            require 'amgad/module/sendCancelled.php';
            break;
    case '/amgad/module/checkBeforeConfirm.php':
            require 'amgad/module/checkBeforeConfirm.php';
            break;
    case '/amgad/includes/DbOperations.php':
            require 'amgad/includes/DbOperations.php';
            break;
    default:
        http_response_code(404);
        exit('Not Found');
}
