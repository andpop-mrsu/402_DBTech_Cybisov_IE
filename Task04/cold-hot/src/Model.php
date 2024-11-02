<?php

declare(strict_types=1);

namespace Ivante2004\ColdHot\Model;

use SQLite3;

class Model
{
    private $db;

    public function __construct($db_path)
    {
        $this->db = new SQLite3($db_path);
    }

    public function createId()
    {
        $query = "SELECT id FROM result_games ORDER BY id DESC LIMIT 1";

        $lastGameId = $this->db->query($query);

        if ($row = $lastGameId->fetchArray()) {
            $lastId = $row[0];
            return $lastId + 1;
        } else {
            return 1;
        }
    }

    public function createTables()
    {

        $query = "CREATE TABLE IF NOT EXISTS result_games (
            id INTEGER PRIMARY KEY,
            player_name TEXT NOT NULL,
            secret_number INTEGER NOT NULL,
            created_at  DATETIME NOT NULL,
            result BOOLEAN NOT NULL
        )";

        $this->db->exec($query);

        $query = "CREATE TABLE IF NOT EXISTS tries (
            id INTEGER PRIMARY KEY,
            game_id INTEGER NOT NULL,
            number_try INTEGER NOT NULL,
            number INTEGER NOT NULL,
            result TEXT NOT NULL
        )";

        $this->db->exec($query);

        echo "Таблицы успешно созданы";
    }

    public function closeConnection()
    {
        $this->db->close();
    }

    public function storeResult($playerName, $secretNumber, $result)
    {
        $now = date('Y-m-d H:i:s');

        $query = "INSERT INTO result_games (player_name, secret_number, created_at, result) VALUES ('$playerName', $secretNumber, '$now', '$result')";

        $this->db->exec($query);
    }

    public function storeTry($gameId, $numberTry, $number, $result)
    {
        $query = "INSERT INTO tries (game_id, number_try, number, result) VALUES ('$gameId', '$numberTry', '$number', '$result')";

        $this->db->exec($query);
    }

    public function getGames()
    {
        $query = "SELECT * FROM result_games";

        $result = $this->db->query($query);

        $games = [];

        while ($game = $result->fetchArray(SQLITE3_ASSOC)) {
            $games[] = $game;
        }

        return $games;
    }

    public function getGame($gameId)
    {
        $query = "SELECT * FROM tries WHERE game_id = '$gameId'";

        $result = $this->db->query($query);

        $tries = [];

        while ($try = $result->fetchArray(SQLITE3_ASSOC)) {
            $tries[] = $try;
        }

        return $tries;
    }
}
