<?php
namespace App\Validation;

use App\Exceptions\LoginValidationException;

class LoginFormValidator
{
    private array $data;
    private array $errors =[];
    private array $rules;

    public function __construct(array $data, array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function passes(): void
    {
        foreach ($this->rules as $key => $rules)
        {
            foreach ($rules as $rule){
                [$name, $attribute] = explode(':', $rule);
                $ruleName = 'validate' . ucfirst($name); // dynamically calls a private function (for example) 'validateRequired()' / validateMin:3

                if(!$ruleName){
                    $_SESSION['status'] = 'Invalid validation rule'; // check if method exists --> not: invalid validation rule
                }
                $this->{$ruleName}($key, $attribute);
            }

        }

        if(count($this->errors) > 0){
            throw new LoginValidationException();
        }
    }


    private function validateRequired(string $key): void
    {
        if(empty(trim($this->data[$key]))){
            $this->errors[$key][] = "{$key} field is required.";
        }
    }

    private function validateMin(string $key, int $attribute): void
    {
        if(strlen($this->data[$key]) < $attribute){
            $this->errors[$key][] = "{$key} must be at least {$attribute} characters long.";
        }
    }

    private function validateEmail(string $key, string $character): void
    {
        if(!str_contains($this->data[$key], $character)){
            $this->errors[$key][] = "Invalid email.";
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}