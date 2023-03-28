<?php

interface DatabaseInterface
{

    public function create(array $data): bool|string;
    public function update(array $data):bool;
    public function where(string $field , ?string $comparison ,float|string $value = 0):static;
    public function whereIn(string $field ,array $params):static;
    public function select(...$params): static;
    public function first():array;
    public function delete(): bool;
    public function limit(float $countRow):static;
    public function join(string $tableJoin , string $primaryTableBoard ,
                         string $comparison, string $foreignTableB):static;
    public function leftJoin(string $tableJoin, string $primaryTableBoard,
                             string $comparison, string $foreignTableB): static;
    public function rightJoin(string $tableJoin, string $primaryTableBoard,
                             string $comparison, string $foreignTableB): static;

}