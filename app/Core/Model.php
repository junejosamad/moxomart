<?php

namespace App\Core;

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBy($column, $value)
    {
        // Validate column name to prevent SQL injection
        $column = $this->sanitizeColumnName($column);
        
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    public function findFirst($conditions = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $column = $this->sanitizeColumnName($column);
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function all($orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $orderBy = $this->sanitizeOrderBy($orderBy);
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $limit = $this->sanitizeLimit($limit);
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findAll($conditions = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $column = $this->sanitizeColumnName($column);
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        if ($orderBy) {
            $orderBy = $this->sanitizeOrderBy($orderBy);
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $limit = $this->sanitizeLimit($limit);
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function where($conditions, $orderBy = null, $limit = null)
    {
        $whereParts = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $column = $this->sanitizeColumnName($column);
            $whereParts[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $whereClause = implode(' AND ', $whereParts);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        
        if ($orderBy) {
            $orderBy = $this->sanitizeOrderBy($orderBy);
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $limit = $this->sanitizeLimit($limit);
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $column = $this->sanitizeColumnName($column);
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'];
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function paginate($page = 1, $perPage = 15, $conditions = [], $orderBy = null)
    {
        $page = max(1, (int)$page);
        $perPage = min(100, max(1, (int)$perPage)); // Max 100 items per page
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $totalCount = $this->count($conditions);
        
        // Build query
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $column = $this->sanitizeColumnName($column);
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        if ($orderBy) {
            $orderBy = $this->sanitizeOrderBy($orderBy);
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $totalCount,
            'last_page' => ceil($totalCount / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $totalCount)
        ];
    }

    /**
     * Sanitize column name to prevent SQL injection
     */
    protected function sanitizeColumnName($column)
    {
        // Only allow alphanumeric characters, underscores, and dots
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name: {$column}");
        }
        return $column;
    }

    /**
     * Sanitize ORDER BY clause to prevent SQL injection
     */
    protected function sanitizeOrderBy($orderBy)
    {
        // Split by comma and validate each part
        $parts = array_map('trim', explode(',', $orderBy));
        $validatedParts = [];
        
        foreach ($parts as $part) {
            // Allow column names with optional ASC/DESC
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?\s*(ASC|DESC)?$/i', trim($part))) {
                throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
            }
            $validatedParts[] = trim($part);
        }
        
        return implode(', ', $validatedParts);
    }

    /**
     * Sanitize LIMIT value to prevent SQL injection
     */
    protected function sanitizeLimit($limit)
    {
        if (!is_numeric($limit) || $limit < 0) {
            throw new \InvalidArgumentException("Invalid LIMIT value: {$limit}");
        }
        return (int)$limit;
    }
}
