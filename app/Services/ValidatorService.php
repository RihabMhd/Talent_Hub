<?php
namespace App\Services;
class ValidatorService
{
    
    public function validateUser(array $data, bool $partial = false): array
    {
        $errors = [];
        
      
        if (!$partial || isset($data['name'])) {
            if (empty($data['name'])) {
                $errors['name'] = 'name is required';
            } elseif (strlen($data['name']) < 2) {
                $errors['name'] = 'name must be at least 2 characters';
            } elseif (strlen($data['name']) > 100) {
                $errors['name'] = 'name must not exceed 100 characters';
            }
        }
        
       
        if (!$partial || isset($data['email'])) {
            if (empty($data['email'])) {
                $errors['email'] = 'email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'invalid email format';
            } elseif (strlen($data['email']) > 255) {
                $errors['email'] = 'email must not exceed 255 characters';
            }
        }
        
       
        if (!$partial || isset($data['password'])) {
            if (empty($data['password'])) {
                if (!$partial) {
                    $errors['password'] = 'password is required';
                }
            } elseif (strlen($data['password']) < 6) {
                $errors['password'] = 'password must be at least 6 characters';
            } elseif (strlen($data['password']) > 255) {
                $errors['password'] = 'password must not exceed 255 characters';
            }
        }
        
      
        if (!$partial || isset($data['role_id'])) {
            if (empty($data['role_id']) && !$partial) {
                $errors['role_id'] = 'role is required';
            } elseif (isset($data['role_id']) && !is_numeric($data['role_id'])) {
                $errors['role_id'] = 'invalid role id';
            }
        }
        
        return $errors;
    }
    
    
    public function validateRole(array $data, bool $partial = false): array
    {
        $errors = [];
        
       
        if (!$partial || isset($data['name'])) {
            if (empty($data['name'])) {
                $errors['name'] = 'name is required';
            } elseif (strlen($data['name']) < 2) {
                $errors['name'] = 'name must be at least 2 characters';
            } elseif (strlen($data['name']) > 50) {
                $errors['name'] = 'name must not exceed 50 characters';
            } elseif (!preg_match('/^[a-zA-Z_]+$/', $data['name'])) {
                $errors['name'] = 'name must contain only letters and underscores';
            }
        }
        
      
        if (!$partial || isset($data['description'])) {
            if (isset($data['description']) && strlen($data['description']) > 255) {
                $errors['description'] = 'description must not exceed 255 characters';
            }
        }
        
        return $errors;
    }
    
    
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    
    public function validatePasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];
        
        
        $length = strlen($password);
        if ($length >= 8) {
            $score += 1;
        } else {
            $feedback[] = 'password should be at least 8 characters';
        }
        
        if ($length >= 12) {
            $score += 1;
        }
        
         
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'add lowercase letters';
        }
        
     
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'add uppercase letters';
        }
        
        
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'add numbers';
        }
        
         
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'add special characters';
        }
        
      
        $strength = 'weak';
        if ($score >= 5) {
            $strength = 'strong';
        } elseif ($score >= 3) {
            $strength = 'medium';
        }
        
        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback
        ];
    }
    
    
    public function sanitizeString(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    
    public function validateRequired(array $data, array $requiredFields): array
    {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "$field is required";
            }
        }
        
        return $errors;
    }
    
     
    public function validateLength(string $value, int $min, int $max, string $fieldName): ?string
    {
        $length = strlen($value);
        
        if ($length < $min) {
            return "$fieldName must be at least $min characters";
        }
        
        if ($length > $max) {
            return "$fieldName must not exceed $max characters";
        }
        
        return null;
    }
}