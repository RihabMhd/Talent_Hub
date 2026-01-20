<?php
namespace App\Model;
class Role
{
    private ?int $id;
    private string $name;
    private string $description;
    
   
    public function __construct(?int $id = null, string $name = '', string $description = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
    

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['description'] ?? ''
        );
    }
    

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'description' => $this->description
        ];
        
        if ($this->id !== null) {
            $data['id'] = $this->id;
        }
        
        return $data;
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }
    

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
 
    public function getName(): string
    {
        return $this->name;
    }
    

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
  
    public function getDescription(): string
    {
        return $this->description;
    }
    
  
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}