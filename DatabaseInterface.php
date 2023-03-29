<?php

interface DatabaseInterface
{

    public function create(array $data);
    public function update(array $data):bool;
    public function where(string $field , ?string $comparison , $value = 0);
    public function whereIn(string $field ,array $params);
    public function select(...$params);
    public function first():array;
    public function delete(): bool;
    public function limit(float $countRow);
    public function join(string $tableJoin , string $primaryTableBoard ,
                         string $comparison, string $foreignTableB);
    public function leftJoin(string $tableJoin, string $primaryTableBoard,
                             string $comparison, string $foreignTableB);
    public function rightJoin(string $tableJoin, string $primaryTableBoard,
                             string $comparison, string $foreignTableB);
    public function get():array;

}