<?php
declare(strict_types=1);

function nice_print(string $string): void
{
    echo $string . PHP_EOL;
}

$resSTDIN = fopen("php://stdin", 'rb');
nice_print("Введите строку со скобочками для обработки: ");
$strChar = fgets($resSTDIN);
nice_print("Ваша строка: $strChar");
if (!str_contains($strChar, '(') && !str_contains($strChar, ')')) {
    nice_print("В строке вообще нет скобочек");
    exit();
}

$open_brackets = [];
$close_brackets = [];

preg_match_all("/\(/", $strChar, $open_brackets, PREG_OFFSET_CAPTURE);
preg_match_all("/\)/", $strChar, $close_brackets, PREG_OFFSET_CAPTURE);

$open_brackets = current($open_brackets);
$close_brackets = current($close_brackets);

$error_messages = [];
foreach ($open_brackets as $key => [, $o_position]) {
    [, $close_bracket_position] = $close_brackets[$key] ?? [null, null];
    if ($close_bracket_position === null) {
        $error_messages[] = "Нет закрывающей скобки для открывающей скобки на позиции " . $o_position + 1;
        continue;
    }

    unset($close_brackets[$key]);
    if ($o_position < $close_bracket_position) {
        continue;
    }
    $error_messages[] = "Нет открывающей скобки для закрывающей скобки на позиции " . $close_bracket_position + 1;
}
foreach ($close_brackets as [, $c_position]) {
    $error_messages[] = "Нет открывающей скобки для закрывающей скобки на позиции " . $c_position + 1;
}
if (empty($error_messages)) {
    nice_print("В строке скобки расставлены правильно");
    exit();
}

foreach ($error_messages as $error_message) {
    nice_print($error_message);
}
fclose($resSTDIN);
exit();