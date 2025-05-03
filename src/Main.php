<?php
declare(strict_types=1);

namespace App;

class Main
{
    private const OPEN = 'open';
    private const CLOSE = 'close';

    public function execute(): void
    {
        $resSTDIN = fopen("php://stdin", 'rb');
        $this->nicePrint("Введите строку со скобочками для обработки: ");
        $strChar = fgets($resSTDIN);
        $this->nicePrint("Ваша строка: $strChar");
        if (!str_contains($strChar, '(') && !str_contains($strChar, ')')) {
            $this->nicePrint("В строке вообще нет скобочек");
            fclose($resSTDIN);
            return;
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
                $error_messages[] = $this->makeError(self::CLOSE, $o_position + 1);
                continue;
            }

            unset($close_brackets[$key]);
            if ($o_position < $close_bracket_position) {
                continue;
            }
            $error_messages[] = $this->makeError(self::OPEN, $close_bracket_position + 1);
        }

        foreach ($close_brackets as [, $c_position]) {
            $error_messages[] = $this->makeError(self::OPEN, $c_position + 1);
        }

        if (empty($error_messages)) {
            $this->nicePrint("В строке скобки расставлены правильно");
            fclose($resSTDIN);
            return;
        }

        foreach ($error_messages as $error_message) {
            $this->nicePrint($error_message);
        }
        fclose($resSTDIN);
    }

    public function nicePrint(string $text): void
    {
        echo $text . PHP_EOL;
    }

    public function makeError(string $type, int $position): string
    {
        $text = self::OPEN === $type ? 'открывающей для закрывающей' : 'закрывающей для открывающей';

        return "Нет $text скобки на позиции " . $position;
    }
}