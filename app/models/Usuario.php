<?php
/**
 * Modelo de Usuario
 */
class Usuario extends Model {
    protected $table = 'usuarios';
    
    public function authenticate($email, $password) {
        $user = $this->findBy('email', $email);
        
        if ($user && $user['estado'] === 'activo' && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function createUser($data) {
        // Hash de la contraseña
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->create($data);
    }
    
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashedPassword]);
    }
    
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getActiveUsers() {
        return $this->findAll(['estado' => 'activo'], 'nombre');
    }
    
    public function getAllUsers() {
        return $this->findAll([], 'fecha_creacion', 'DESC');
    }
    
    public function updateUser($id, $data) {
        // Si se proporciona una nueva contraseña, hashearla
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Si no se proporciona contraseña, no actualizarla
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    public function deactivateUser($id) {
        return $this->update($id, ['estado' => 'inactivo']);
    }
    
    public function activateUser($id) {
        return $this->update($id, ['estado' => 'activo']);
    }
}