<?php

namespace Ivante2004\ColdHot\View;

use function cli\line;

class View
{
    public static function showGame($isExist)
    {

        line("1) Новая игра");
        if ($isExist) {
            line("2) Вывод списка всех сохраненных в базе партий");
            line("3) Повтор любой сохраненной партии");
        }
    }

    public static function showLose(string $secretNumber)
    {
        line("Вы проиграли! Загаданное число: $secretNumber");
    }

    public static function showWin(string $secretNumber)
    {
        line("Вы победили! Загаданное число: $secretNumber");
    }

    public static function showHints(string $hints)
    {
        line($hints);
    }

    public static function showHistory($games)
    {
        print("\033[2J\033[;H");

        line("|  id |                 дата |           имя игрока | загаданное число | результат |");
        foreach ($games as $game) {
            $result = $game['result'] ? 'Победа' : ' Проигрыш';
            printf(
                "| %3s | %20s | %20s | %16s | %15s |\n",
                $game['id'],
                $game['created_at'],
                $game['player_name'],
                $game['secret_number'],
                $result
            );
        }
    }

    public static function showGameRepeat($tries)
    {
        print("\033[2J\033[;H");

        line(" номер попытки | введенное число | результат");
        foreach ($tries as $try) {
            $result = $try['result'];

            printf(
                " %13s | %15s | %s \n",
                $try['number_try'],
                $try['number'],
                $result
            );
        }
    }
}
