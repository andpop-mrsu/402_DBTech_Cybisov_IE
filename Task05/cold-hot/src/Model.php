<?php

declare(strict_types=1);

namespace Ivante2004\ColdHot\Model;

use SQLite3;

class Model
{
    public function __construct($db_path)
    {
        R::setup('sqlite:' . $db_path);
    }

    public function createId()
    {
        $lastGame = R::findOne('results', 'ORDER BY id DESC');

        if ($lastGame) {
            return $lastGame->id + 1;
        } else {
            return 1;
        }
    }

    public function createTables()
    {
        R::exec("CREATE TABLE IF NOT EXISTS results (
            id INTEGER PRIMARY KEY,
            player_name TEXT NOT NULL,
            secret_number INTEGER NOT NULL,
            created_at  DATETIME NOT NULL,
            result BOOLEAN NOT NULL
        )");

        R::exec("CREATE TABLE IF NOT EXISTS tries (
            id INTEGER PRIMARY KEY,
            game_id INTEGER NOT NULL,
            number_try INTEGER NOT NULL,
            number INTEGER NOT NULL,
            result TEXT NOT NULL
        )");

        // echo "Таблицы успешно созданы";
    }

    public function storeResult($playerName, $secretNumber, $result)
    {
        $game = R::dispense("results");

        $game->player_name = $playerName;
        $game->secret_number = $secretNumber;
        $game->created_at = date('Y-m-d H:i:s');
        $game->result = $result ? 1 : 0;
        R::store($game);
    }

    public function storeTry($gameId, $numberTry, $number, $result)
    {
        $try = R::dispense('tries');
        $try->game_id = $gameId;
        $try->number_try = $numberTry;
        $try->number = $number;
        $try->result = $result;
        R::store($try);
    }

    public function getGames()
    {
        return R::findAll('results');
    }

    public function getGame($gameId)
    {
        return R::find('tries', 'game_id = ?', [$gameId]);
    }
}