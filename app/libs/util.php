<?php
function brush_alias($lenguaje = null){
    switch($lenguaje){
        case 'Css': { return 'css'; break; }
        case 'JScript': { return 'js'; break; }
        case 'Php': { return 'php'; break; }
        case 'Plain': { return 'text'; break; }
        case 'Sql': { return 'sql'; break; }
        default: {return 'xml'; break;}
    }
    return 'xml2';
}
?>