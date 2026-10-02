<?php

declare(strict_types=1);

class DatabaseTable {
    public function __construct(
        private PDO $pdo,
        private string $table,
        private string $primaryKey,
        private array $columns
    ) {
        $this->assertIdentifier($table);
        $this->assertIdentifier($primaryKey);
        foreach ($columns as $column) {
            $this->assertIdentifier($column);
        }
    }

    public function findAll(?int $limit = null, ?int $offset = null): array {
        $query = "SELECT * FROM `{$this->table}`";
        if ($limit !== null) {
            $query .= ' LIMIT ' . max(0, $limit);
            if ($offset !== null) {
                $query .= ' OFFSET ' . max(0, $offset);
            }
        }
        return $this->pdo->query($query)->fetchAll();
    }

    public function delete(string $field, int|string $value): void {
        $this->assertColumn($field);
        $stmt = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE `{$field}` = :value");
        $stmt->execute(['value' => $value]);
    }

    public function findById(int $value): array|false {
        return $this->findOne($this->primaryKey, $value);
    }

    public function find(string $field, int|string|null $value): array {
        $this->assertColumn($field);
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `{$field}` = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }

    public function total(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM `{$this->table}`")->fetchColumn();
    }

    public function save(array $record): void {
        $record = array_intersect_key($record, array_flip($this->columns));
        if ($record === []) {
            throw new InvalidArgumentException('No valid columns were supplied.');
        }

        $id = $record[$this->primaryKey] ?? null;
        if ($id === null || $id === '') {
            unset($record[$this->primaryKey]);
            $this->insert($record);
            return;
        }

        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('Invalid record identifier.');
        }
        $this->update($record);
    }

    private function findOne(string $field, int|string $value): array|false {
        $this->assertColumn($field);
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `{$field}` = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetch();
    }

    private function insert(array $values): void {
        $fields = array_keys($values);
        $query = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (:%s)',
            $this->table,
            implode('`, `', $fields),
            implode(', :', $fields)
        );
        $this->pdo->prepare($query)->execute($values);
    }

    private function update(array $values): void {
        $id = $values[$this->primaryKey];
        unset($values[$this->primaryKey]);
        if ($values === []) {
            return;
        }
        $assignments = implode(', ', array_map(static fn (string $field): string => "`{$field}` = :{$field}", array_keys($values)));
        $values['primaryKey'] = $id;
        $query = "UPDATE `{$this->table}` SET {$assignments} WHERE `{$this->primaryKey}` = :primaryKey";
        $this->pdo->prepare($query)->execute($values);
    }

    private function assertColumn(string $column): void {
        if (!in_array($column, $this->columns, true)) {
            throw new InvalidArgumentException('Invalid database column.');
        }
    }

    private function assertIdentifier(string $identifier): void {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid database identifier.');
        }
    }
}
